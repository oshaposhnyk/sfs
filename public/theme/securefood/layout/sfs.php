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

// Sidebar state from the user preference (domain 03).
$sidebarcollapsed = $isloggedin
    && get_user_preferences('theme_securefood_sidebar', 'expanded') === 'collapsed';

$extraclasses = ['sfs-mode'];
if ($sidebarcollapsed) {
    $extraclasses[] = 'sfs-sidebar-collapsed';
}
$bodyattributes = $OUTPUT->body_attributes($extraclasses);

// Blocks region (kept per ADR-007; rendered below the main content).
$addblockbutton = $OUTPUT->addblockbutton();
$blockshtml = $OUTPUT->blocks('side-pre');
$hasblocks = (strpos($blockshtml, 'data-block=') !== false || !empty($addblockbutton));

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
$navsections = \theme_securefood\navigation::for_page($PAGE->url, $isloggedin);

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

// Global search entry point.
$searchurl = null;
if (class_exists('\core_search\manager') && \core_search\manager::is_global_search_enabled()) {
    $searchurl = (new moodle_url('/search/index.php'))->out(false);
}

$templatecontext = [
    'sitename' => format_string($SITE->shortname, true, ['context' => context_course::instance(SITEID), 'escape' => false]),
    'output' => $OUTPUT,
    'bodyattributes' => $bodyattributes,
    'sidebarcollapsed' => $sidebarcollapsed,
    'navsections' => $navsections,
    'usercard' => $usercard,
    'crumbs' => $crumbs,
    'hascrumbs' => $crumbs !== [],
    'searchurl' => $searchurl,
    'modeswitch' => $modeswitch,
    'logofulllight' => $OUTPUT->image_url('logo-full-light', 'theme')->out(false),
    'logofulldark' => $OUTPUT->image_url('logo-full-dark', 'theme')->out(false),
    'logoiconlight' => $OUTPUT->image_url('logo-icon-light', 'theme')->out(false),
    'logoicondark' => $OUTPUT->image_url('logo-icon-dark', 'theme')->out(false),
    'homeurl' => (new moodle_url('/'))->out(false),
    'sidepreblocks' => $blockshtml,
    'hasblocks' => $hasblocks,
    'addblockbutton' => $addblockbutton,
    'usermenu' => $primarymenu['user'] ?? null,
    'langmenu' => $primarymenu['lang'] ?? null,
    'secondarymoremenu' => $secondarynavigation ?: false,
    'regionmainsettingsmenu' => $regionmainsettingsmenu,
    'hasregionmainsettingsmenu' => !empty($regionmainsettingsmenu),
    'overflow' => $overflow,
    'headercontent' => $headercontent,
];

echo $OUTPUT->render_from_template('theme_securefood/shell', $templatecontext);
