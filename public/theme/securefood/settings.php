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
 * SecureFood School theme settings (ADR-007 — no-code customisation).
 *
 * Phase 1 tabs: Colours, Advanced. Navigation / Pages & content / Blocks
 * tabs arrive with domains 01, 02 and 11 (Phase 2).
 *
 * @package    theme_securefood
 * @copyright  2026 SecureFood School
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

declare(strict_types=1);

defined('MOODLE_INTERNAL') || die();

if ($ADMIN->fulltree) {
    $settings = new theme_boost_admin_settingspage_tabs(
        'themesettingsecurefood',
        get_string('configtitle', 'theme_securefood')
    );

    // -------------------------------------------------------------------
    // Tab: General — mode switch policy (ADR-002).
    // -------------------------------------------------------------------
    $page = new admin_settingpage('theme_securefood_general', get_string('generaltab', 'theme_securefood'));

    $page->add(new admin_setting_configselect(
        'theme_securefood/forcemode',
        get_string('forcemode', 'theme_securefood'),
        get_string('forcemode_desc', 'theme_securefood'),
        '',
        [
            '' => get_string('forcemode_userchoice', 'theme_securefood'),
            'standard' => get_string('mode_standard', 'theme_securefood'),
            'securefood' => get_string('mode_securefood', 'theme_securefood'),
        ]
    ));

    $page->add(new admin_setting_configselect(
        'theme_securefood/defaultmode',
        get_string('defaultmode', 'theme_securefood'),
        get_string('defaultmode_desc', 'theme_securefood'),
        'securefood',
        [
            'standard' => get_string('mode_standard', 'theme_securefood'),
            'securefood' => get_string('mode_securefood', 'theme_securefood'),
        ]
    ));

    $settings->add($page);

    // -------------------------------------------------------------------
    // Tab: Navigation — settings-driven sidebar menu (ADR-007).
    // -------------------------------------------------------------------
    $page = new admin_settingpage('theme_securefood_navigation', get_string('navigationtab', 'theme_securefood'));

    $page->add(new admin_setting_configtextarea(
        'theme_securefood/navigation',
        get_string('navigation', 'theme_securefood'),
        get_string('navigation_desc', 'theme_securefood'),
        '',
        PARAM_RAW
    ));

    $settings->add($page);

    // -------------------------------------------------------------------
    // Tab: Pages & content — About the Project front page (ADR-007).
    // Empty values fall back to the design copy.
    // -------------------------------------------------------------------
    $page = new admin_settingpage('theme_securefood_pages', get_string('pagestab', 'theme_securefood'));

    foreach (['aboutkicker', 'abouttitle'] as $name) {
        $page->add(new admin_setting_configtext(
            "theme_securefood/{$name}",
            get_string($name, 'theme_securefood'),
            get_string('aboutfallback_desc', 'theme_securefood'),
            '',
            PARAM_TEXT
        ));
    }
    $page->add(new admin_setting_configtextarea(
        'theme_securefood/aboutlede',
        get_string('aboutlede', 'theme_securefood'),
        get_string('aboutfallback_desc', 'theme_securefood'),
        '',
        PARAM_RAW
    ));
    $page->add(new admin_setting_configtext(
        'theme_securefood/aboutfeedtitle',
        get_string('aboutfeedtitle', 'theme_securefood'),
        get_string('aboutfallback_desc', 'theme_securefood'),
        '',
        PARAM_TEXT
    ));
    $page->add(new admin_setting_configtextarea(
        'theme_securefood/aboutfeed',
        get_string('aboutfeed', 'theme_securefood'),
        get_string('aboutfeed_desc', 'theme_securefood'),
        '',
        PARAM_RAW
    ));
    foreach (['aboutstats', 'aboutkpis'] as $name) {
        $page->add(new admin_setting_configtextarea(
            "theme_securefood/{$name}",
            get_string($name, 'theme_securefood'),
            get_string('aboutjson_desc', 'theme_securefood'),
            '',
            PARAM_RAW
        ));
    }

    $settings->add($page);

    // -------------------------------------------------------------------
    // Tab: Colours — one override per design token, light and dark.
    // Empty value = use the SecureFood default from scss/_tokens.scss.
    // -------------------------------------------------------------------
    $page = new admin_settingpage('theme_securefood_colours', get_string('colourstab', 'theme_securefood'));

    $page->add(new admin_setting_heading(
        'theme_securefood/colourintro',
        get_string('colourintro', 'theme_securefood'),
        get_string('colourintro_desc', 'theme_securefood')
    ));

    $translucentdark = \theme_securefood\tokens::translucent_dark();
    foreach (\theme_securefood\tokens::names() as $token) {
        $tokenlabel = get_string('token_' . $token, 'theme_securefood');

        // Light variant — colour picker.
        $setting = new admin_setting_configcolourpicker(
            'theme_securefood/colour_' . $token,
            get_string('colourlight', 'theme_securefood', $tokenlabel),
            get_string('colour_desc', 'theme_securefood', '--sfs-' . $token),
            '',
            null
        );
        $setting->set_updatedcallback('theme_reset_all_caches');
        $page->add($setting);

        // Dark variant — free text where the default is a translucent rgba().
        if (in_array($token, $translucentdark, true)) {
            $setting = new admin_setting_configtext(
                'theme_securefood/colour_' . $token . '_dark',
                get_string('colourdark', 'theme_securefood', $tokenlabel),
                get_string('colourdarkrgba_desc', 'theme_securefood', '--sfs-' . $token),
                '',
                PARAM_RAW_TRIMMED
            );
        } else {
            $setting = new admin_setting_configcolourpicker(
                'theme_securefood/colour_' . $token . '_dark',
                get_string('colourdark', 'theme_securefood', $tokenlabel),
                get_string('colour_desc', 'theme_securefood', '--sfs-' . $token),
                '',
                null
            );
        }
        $setting->set_updatedcallback('theme_reset_all_caches');
        $page->add($setting);
    }

    $settings->add($page);

    // -------------------------------------------------------------------
    // Tab: Advanced — raw SCSS escape hatches (Boost pattern).
    // -------------------------------------------------------------------
    $page = new admin_settingpage('theme_securefood_advanced', get_string('advancedtab', 'theme_securefood'));

    $setting = new admin_setting_scsscode(
        'theme_securefood/scsspre',
        get_string('rawscsspre', 'theme_securefood'),
        get_string('rawscsspre_desc', 'theme_securefood'),
        '',
        PARAM_RAW
    );
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    $setting = new admin_setting_scsscode(
        'theme_securefood/scss',
        get_string('rawscss', 'theme_securefood'),
        get_string('rawscss_desc', 'theme_securefood'),
        '',
        PARAM_RAW
    );
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    $settings->add($page);
}
