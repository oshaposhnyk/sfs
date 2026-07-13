<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * SecureFood shell layout with runtime fallback to Boost drawers (ADR-002).
 *
 * Thin controller: resolves the mode, shapes the template context and renders.
 * No business logic here.
 *
 * @package    theme_securefood
 * @copyright  2026 SecureFood School
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

if (!\theme_securefood\mode_manager::uses_shell($PAGE)) {
    // Standard mode, excluded layout or editing: stock Boost experience.
    require($CFG->dirroot . '/theme/boost/layout/drawers.php');
    return;
}

require_once($CFG->libdir . '/behat/lib.php');
require_once($CFG->dirroot . '/course/lib.php');
require_once($CFG->dirroot . '/cohort/lib.php');

$isloggedin = isloggedin() && !isguestuser();
$settingsprovider = \theme_securefood\settings_provider::from_theme_settings($PAGE->theme->settings ?? null);

// Sidebar state from the user preference (domain 03).
$sidebarcollapsed = $isloggedin
    && get_user_preferences('theme_securefood_sidebar', 'expanded') === 'collapsed';

$schemepreference = $isloggedin
    ? get_user_preferences('theme_securefood_colourscheme', 'system')
    : 'system';
if (!in_array($schemepreference, ['light', 'dark', 'system'], true)) {
    $schemepreference = 'system';
}

$extraclasses = ['sfs-mode'];
if ($sidebarcollapsed) {
    $extraclasses[] = 'sfs-sidebar-collapsed';
}
$bodyattributes = $OUTPUT->body_attributes($extraclasses);

// Block regions (ADR-007): optional content-top / side / content-bottom slots.
$blockregion = static function(string $region, array $classes, string $tag) use ($PAGE, $OUTPUT): array {
    $addblockbutton = $OUTPUT->addblockbutton($region);
    $hascontent = $PAGE->blocks->region_has_content($region, $OUTPUT);

    return [
        'html' => $hascontent ? $OUTPUT->blocks($region, $classes, $tag) : '',
        'addblockbutton' => $addblockbutton,
        'hascontent' => $hascontent || $addblockbutton !== '',
    ];
};
$emptyblockregion = ['html' => '', 'addblockbutton' => '', 'hascontent' => false];

$contenttopblocks = $settingsprovider->enabled('showblockcontenttop')
    ? $blockregion('content-top', ['sfs-shell__blockregion', 'sfs-shell__blockregion--content-top'], 'section')
    : $emptyblockregion;
$sidepreblocks = $settingsprovider->enabled('showblockside')
    ? $blockregion('side-pre', ['sfs-shell__blockregion', 'sfs-shell__blockregion--side'], 'aside')
    : $emptyblockregion;
$contentbottomblocks = $settingsprovider->enabled('showblockcontentbottom')
    ? $blockregion('content-bottom', ['sfs-shell__blockregion', 'sfs-shell__blockregion--content-bottom'], 'section')
    : $emptyblockregion;

// Secondary navigation (course/user tabs) — same wiring as Boost drawers.
$secondarynavigation = false;
$overflow = '';
if ($PAGE->has_secondary_navigation()) {
    $tablistnav = $PAGE->has_tablist_secondary_navigation();
    $moremenu = new \core\navigation\output\more_menu($PAGE->secondarynav, 'nav-tabs', true, $tablistnav);
    $secondarynavigation = $moremenu->export_for_template($OUTPUT);
    $overflowdata = $PAGE->secondarynav->get_overflow_menu_data();
    if (!is_null($overflowdata)) {
        $selectmenu = new \core\output\select_menu('tertiarynavigation', $overflowdata->urls, $overflowdata->selected);
        $selectmenu->set_label($overflowdata->label, $overflowdata->labelattributes);
        $overflow = $selectmenu->export_for_template($OUTPUT);
    }
}

$primary = new core\navigation\output\primary($PAGE);
$renderer = $PAGE->get_renderer('core');
$primarymenu = $primary->export_for_template($renderer);

$buildregionmainsettings = !$PAGE->include_region_main_settings_in_header_actions() && !$PAGE->has_secondary_navigation();
$regionmainsettingsmenu = $buildregionmainsettings ? $OUTPUT->region_main_settings_menu() : false;

$header = $PAGE->activityheader;
$headercontent = $header->export_for_template($renderer);

// Breadcrumbs for the topbar.
$crumbs = [];
foreach ($PAGE->navbar->get_items() as $item) {
    $title = trim((string)$item->get_title()) !== '' ? $item->get_title() : $item->text;
    $crumbs[] = [
        'text' => $title,
        'url' => $item->action instanceof moodle_url ? $item->action->out(false) : null,
    ];
}
if ($crumbs !== []) {
    $crumbs[count($crumbs) - 1]['last'] = true;
}

// Sidebar navigation model (settings-driven, ADR-007).
$navsections = \theme_securefood\navigation::for_page($PAGE->url, $isloggedin, $PAGE->context);

// User card.
$usercard = null;
if ($isloggedin) {
    global $USER;
    $initials = mb_strtoupper(mb_substr($USER->firstname, 0, 1) . mb_substr($USER->lastname, 0, 1));
    $cohorts = cohort_get_user_cohorts($USER->id);
    $usercard = [
        'fullname' => fullname($USER),
        'initials' => $initials,
        'subtitle' => $cohorts ? format_string(reset($cohorts)->name) : '',
        'profileurl' => (new moodle_url('/user/profile.php', ['id' => $USER->id]))->out(false),
    ];
}

$usermenu = $primarymenu['user'] ?? null;
if (is_array($usermenu) && $usercard !== null) {
    $usermenu['subtitle'] = $usercard['subtitle'];

    $usermenuvalue = static function($item, string $name) {
        if (is_array($item) && array_key_exists($name, $item)) {
            return $item[$name];
        }
        if (is_object($item) && property_exists($item, $name)) {
            return $item->{$name};
        }
        return null;
    };

    $usermenustring = static function($value): string {
        if ($value instanceof moodle_url) {
            return $value->out(false);
        }
        if ($value === null) {
            return '';
        }
        return (string)$value;
    };

    $usermenuicon = static function($item) use ($usermenuvalue, $usermenustring): string {
        if ((bool)$usermenuvalue($item, 'submenulink')) {
            return 'language';
        }

        $url = $usermenustring($usermenuvalue($item, 'url'));
        $path = (string)(parse_url($url, PHP_URL_PATH) ?: $url);
        $title = $usermenustring($usermenuvalue($item, 'title'));
        $identifier = $usermenustring($usermenuvalue($item, 'titleidentifier'));
        $needle = \core_text::strtolower($path . ' ' . $title . ' ' . $identifier);

        return match (true) {
            str_contains($needle, '/user/profile') || str_contains($needle, 'profile') => 'person',
            str_contains($needle, '/grade/') || str_contains($needle, 'grade')
                || str_contains($needle, 'оцін') => 'grade',
            str_contains($needle, '/calendar/') || str_contains($needle, 'calendar')
                || str_contains($needle, 'календар') => 'calendar_today',
            str_contains($needle, '/user/files') || str_contains($needle, 'file')
                || str_contains($needle, 'файл') => 'folder',
            str_contains($needle, '/report/') || str_contains($needle, 'report')
                || str_contains($needle, 'звіт') => 'assessment',
            str_contains($needle, '/user/preferences') || str_contains($needle, 'preferences')
                || str_contains($needle, 'уподоб') => 'tune',
            str_contains($needle, 'language') || str_contains($needle, 'мова') => 'language',
            str_contains($needle, '/course/switchrole') || str_contains($needle, 'switchrole') => 'people',
            str_contains($needle, '/login/logout') || str_contains($needle, 'logout')
                || str_contains($needle, 'вийти') => 'logout',
            default => 'chevron_right',
        };
    };

    $assignusermenuicon = static function(&$item, ?string $forcedicon = null) use ($usermenuicon): void {
        $icon = $forcedicon ?? $usermenuicon($item);
        if (is_array($item)) {
            $item['sfsicon'] = $icon;
            return;
        }
        if (is_object($item)) {
            $item->sfsicon = $icon;
        }
    };

    foreach (array_keys($usermenu['items'] ?? []) as $itemindex) {
        $assignusermenuicon($usermenu['items'][$itemindex]);
    }

    foreach (array_keys($usermenu['submenus'] ?? []) as $submenuindex) {
        if (!is_object($usermenu['submenus'][$submenuindex])
                || !isset($usermenu['submenus'][$submenuindex]->items)
                || !is_array($usermenu['submenus'][$submenuindex]->items)) {
            continue;
        }
        foreach (array_keys($usermenu['submenus'][$submenuindex]->items) as $itemindex) {
            $assignusermenuicon($usermenu['submenus'][$submenuindex]->items[$itemindex], 'language');
        }
    }
}

// Mode switch control (domain 02).
$modeswitch = null;
if (\theme_securefood\mode_manager::can_user_switch()) {
    $modeswitch = [
        'url' => (new moodle_url('/theme/securefood/mode.php', [
            'mode' => \theme_securefood\mode_manager::MODE_STANDARD,
            'sesskey' => sesskey(),
            'returnurl' => $PAGE->url->out_as_local_url(false),
        ]))->out(false),
    ];
}

// Optional help destination for the topbar "?" button (Navigation tab).
$helpurl = $settingsprovider->help_url();

// Global search entry point.
$searchurl = null;
if (class_exists('\core_search\manager') && \core_search\manager::is_global_search_enabled()) {
    $searchurl = (new moodle_url('/search/index.php'))->out(false);
}

// Optional admin-configured footer HTML (Advanced tab).
$footerhtml = '';
$footerrawhtml = $settingsprovider->html('footerhtml');
if ($footerrawhtml !== '') {
    $footerhtml = format_text($footerrawhtml, FORMAT_HTML, ['context' => context_system::instance()]);
}

// Course pages: right rail (audit C1) + section fractions (audit C2).
$railhtml = '';
if ($PAGE->pagelayout === 'course' && $isloggedin
        && !empty($PAGE->course->id) && (int)$PAGE->course->id !== (int)$SITE->id) {
    global $USER;
    $raildata = \theme_securefood\courserail::context($PAGE->course, (int)$USER->id);
    $railhtml = $OUTPUT->render_from_template('theme_securefood/course_rail', $raildata['rail']);
    if ($raildata['sections'] !== []) {
        $PAGE->requires->js_call_amd('theme_securefood/sectionprogress', 'init', [$raildata['sections']]);
    }
}

$themefileurl = static function(string $setting, string $filearea, string $fallbackpix) use ($PAGE, $OUTPUT, $settingsprovider): string {
    return $settingsprovider->theme_file_url(
        $PAGE->theme,
        $setting,
        $filearea,
        $OUTPUT->image_url($fallbackpix, 'theme')->out(false)
    );
};

// Front page carries the settings-driven About content above site content.
$abouthtml = '';
if ($PAGE->pagelayout === 'frontpage' && $settingsprovider->enabled('showaboutpage')) {
    $aboutcontext = \theme_securefood\about::context($settingsprovider->settings());
    $aboutcontext['logourl'] = $themefileurl('logoicondark', 'logoicondark', 'logo-icon-dark');
    $abouthtml = $OUTPUT->render_from_template('theme_securefood/about', $aboutcontext);

    // Interactive hubs map (self-hosted Leaflet + bundled GeoJSON; the
    // static dot map in the template stays as the no-JS fallback).
    $maphubs = [];
    foreach ($aboutcontext['hubs'] ?? [] as $hub) {
        if (isset($hub['lat'], $hub['lon'])) {
            $maphubs[] = [
                'hubid' => $hub['hubid'],
                'name' => $hub['name'],
                'country' => $hub['country'],
                'islab' => $hub['islab'],
                'markerlabel' => $hub['markerlabel'],
                'lat' => $hub['lat'],
                'lon' => $hub['lon'],
            ];
        }
    }
    if ($maphubs !== []) {
        $PAGE->requires->js_call_amd('theme_securefood/aboutmap', 'init', [[
            'hubs' => $maphubs,
            'geourl' => (new moodle_url('/theme/securefood/map/europe.json'))->out(false),
            'maplabel' => get_string('aboutmaplabel', 'theme_securefood'),
            'lablabel' => get_string('abouthub_lab', 'theme_securefood'),
            'partnerlabel' => get_string('abouthub_partner', 'theme_securefood'),
        ]]);
    }
}

$templatecontext = [
    'abouthtml' => $abouthtml,
    'railhtml' => $railhtml,
    'sitename' => format_string(
        $settingsprovider->brand_name((string)$SITE->shortname),
        true,
        ['context' => context_course::instance(SITEID), 'escape' => false]
    ),
    'output' => $OUTPUT,
    'bodyattributes' => $bodyattributes,
    'sidebarcollapsed' => $sidebarcollapsed,
    'schemepreference' => $schemepreference,
    'navsections' => $navsections,
    'usercard' => $usercard,
    'crumbs' => $crumbs,
    'hascrumbs' => $crumbs !== [],
    'searchurl' => $searchurl,
    'helpurl' => $helpurl,
    'footerhtml' => $footerhtml,
    'modeswitch' => $modeswitch,
    'logofulllight' => $themefileurl('logofulllight', 'logofulllight', 'logo-full-light'),
    'logofulldark' => $themefileurl('logofulldark', 'logofulldark', 'logo-full-dark'),
    'logoiconlight' => $themefileurl('logoiconlight', 'logoiconlight', 'logo-icon-light'),
    'logoicondark' => $themefileurl('logoicondark', 'logoicondark', 'logo-icon-dark'),
    'homeurl' => (new moodle_url('/'))->out(false),
    'contenttopblocks' => $contenttopblocks['html'],
    'contenttopaddblockbutton' => $contenttopblocks['addblockbutton'],
    'hascontenttopblocks' => $contenttopblocks['hascontent'],
    'sidepreblocks' => $sidepreblocks['html'],
    'hasblocks' => $sidepreblocks['hascontent'],
    'addblockbutton' => $sidepreblocks['addblockbutton'],
    'contentbottomblocks' => $contentbottomblocks['html'],
    'contentbottomaddblockbutton' => $contentbottomblocks['addblockbutton'],
    'hascontentbottomblocks' => $contentbottomblocks['hascontent'],
    'usermenu' => $usermenu,
    'langmenu' => $primarymenu['lang'] ?? null,
    'secondarymoremenu' => $secondarynavigation ?: false,
    'regionmainsettingsmenu' => $regionmainsettingsmenu,
    'hasregionmainsettingsmenu' => !empty($regionmainsettingsmenu),
    'overflow' => $overflow,
    'headercontent' => $headercontent,
];

echo $OUTPUT->render_from_template('theme_securefood/shell', $templatecontext);
