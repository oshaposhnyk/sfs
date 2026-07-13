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
 * Future Food settings.
 *
 * @package    local_sfsgame
 * @copyright  2026 SecureFood School
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

declare(strict_types=1);

defined('MOODLE_INTERNAL') || die();

if ($hassiteconfig) {
    $settings = new admin_settingpage('local_sfsgame', get_string('pluginname', 'local_sfsgame'));
    $ADMIN->add('localplugins', $settings);

    $settings->add(new admin_setting_heading(
        'local_sfsgame/pageheading',
        get_string('settings:pageheading', 'local_sfsgame'),
        get_string('settings:pageheading_desc', 'local_sfsgame')
    ));

    foreach (['showpagehead', 'showhero', 'showachievements', 'showmissions'] as $name) {
        $settings->add(new admin_setting_configcheckbox(
            "local_sfsgame/{$name}",
            get_string($name, 'local_sfsgame'),
            get_string("{$name}_desc", 'local_sfsgame'),
            1
        ));
    }

    foreach (['pagekicker', 'pagetitle', 'achievementstitle', 'missionstitle'] as $name) {
        $settings->add(new admin_setting_configtext(
            "local_sfsgame/{$name}",
            get_string("setting:{$name}", 'local_sfsgame'),
            get_string('pagefallback_desc', 'local_sfsgame'),
            '',
            PARAM_TEXT
        ));
    }

    foreach (['pagelede', 'noachievements', 'nomissions'] as $name) {
        $settings->add(new admin_setting_configtextarea(
            "local_sfsgame/{$name}",
            get_string("setting:{$name}", 'local_sfsgame'),
            get_string('pagefallback_desc', 'local_sfsgame'),
            '',
            PARAM_TEXT
        ));
    }

    foreach (['startdailylabel', 'startmissionlabel', 'currentranklabel', 'totalxplabel'] as $name) {
        $settings->add(new admin_setting_configtext(
            "local_sfsgame/{$name}",
            get_string("setting:{$name}", 'local_sfsgame'),
            get_string('labelfallback_desc', 'local_sfsgame'),
            '',
            PARAM_TEXT
        ));
    }

    $settings->add(new admin_setting_heading(
        'local_sfsgame/heroheading',
        get_string('settings:heroheading', 'local_sfsgame'),
        get_string('settings:heroheading_desc', 'local_sfsgame')
    ));

    foreach (['herokicker', 'herotitle', 'herotext'] as $name) {
        $settings->add(new admin_setting_configtext(
            "local_sfsgame/{$name}",
            get_string($name, 'local_sfsgame'),
            get_string('herofallback_desc', 'local_sfsgame'),
            '',
            PARAM_TEXT
        ));
    }

    $settings->add(new admin_setting_heading(
        'local_sfsgame/decisionheading',
        get_string('settings:decisionheading', 'local_sfsgame'),
        get_string('settings:decisionheading_desc', 'local_sfsgame')
    ));

    $settings->add(new admin_setting_configcheckbox(
        'local_sfsgame/showdecision',
        get_string('showdecision', 'local_sfsgame'),
        get_string('showdecision_desc', 'local_sfsgame'),
        1
    ));
    foreach (['decisionkicker', 'decisiontitle'] as $name) {
        $settings->add(new admin_setting_configtext(
            "local_sfsgame/{$name}",
            get_string($name, 'local_sfsgame'),
            get_string('decisionfallback_desc', 'local_sfsgame'),
            '',
            PARAM_TEXT
        ));
    }
    $settings->add(new admin_setting_configtextarea(
        'local_sfsgame/decisionbody',
        get_string('decisionbody', 'local_sfsgame'),
        get_string('decisionbody_desc', 'local_sfsgame'),
        '',
        PARAM_RAW
    ));
    $settings->add(new \local_sfsgame\admin_setting\repeat_blocks(
        'local_sfsgame/decisionchoices',
        get_string('decisionchoices', 'local_sfsgame'),
        get_string('decisionchoices_desc', 'local_sfsgame'),
        '',
        \local_sfsgame\admin_setting\repeat_blocks::TYPE_DECISIONS
    ));
    foreach (['decisionempty', 'decisionhint', 'decisionpill'] as $name) {
        $settings->add(new admin_setting_configtext(
            "local_sfsgame/{$name}",
            get_string("setting:{$name}", 'local_sfsgame'),
            get_string('decisionfallback_desc', 'local_sfsgame'),
            '',
            PARAM_TEXT
        ));
    }

    $settings->add(new admin_setting_heading(
        'local_sfsgame/missionsheading',
        get_string('settings:missionsheading', 'local_sfsgame'),
        get_string('settings:missionsheading_desc', 'local_sfsgame')
    ));

    $settings->add(new \local_sfsgame\admin_setting\repeat_blocks(
        'local_sfsgame/missions',
        get_string('missions', 'local_sfsgame'),
        get_string('missions_desc', 'local_sfsgame'),
        '',
        \local_sfsgame\admin_setting\repeat_blocks::TYPE_MISSIONS
    ));
}
