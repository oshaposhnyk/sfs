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
 * Settings for local_learningplans.
 *
 * @package    local_learningplans
 * @copyright  2026
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

if ($hassiteconfig) {
    $settings = new admin_settingpage(
        'local_learningplans_settings',
        get_string('settings:title', 'local_learningplans')
    );

    $settings->add(new admin_setting_configselect(
        'local_learningplans/unenrolpolicy',
        get_string('settings:unenrolpolicy', 'local_learningplans'),
        get_string('settings:unenrolpolicy_desc', 'local_learningplans'),
        'remove',
        [
            'remove' => get_string('settings:unenrolpolicy:remove', 'local_learningplans'),
            'keep' => get_string('settings:unenrolpolicy:keep', 'local_learningplans'),
        ]
    ));

    $settings->add(new admin_setting_configselect(
        'local_learningplans/defaultroleid',
        get_string('settings:defaultroleid', 'local_learningplans'),
        get_string('settings:defaultroleid_desc', 'local_learningplans'),
        5,
        get_default_enrol_roles(context_system::instance())
    ));

    $settings->add(new admin_setting_configcheckbox(
        'local_learningplans/cohortsync',
        get_string('settings:cohortsync', 'local_learningplans'),
        get_string('settings:cohortsync_desc', 'local_learningplans'),
        1
    ));

    $ADMIN->add('localplugins', $settings);
}

