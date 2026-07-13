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
 * Resources & Standards settings.
 *
 * @package    local_sfsresources
 * @copyright  2026 SecureFood School
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

declare(strict_types=1);

defined('MOODLE_INTERNAL') || die();

if ($hassiteconfig) {
    $settings = new admin_settingpage('local_sfsresources', get_string('pluginname', 'local_sfsresources'));
    $ADMIN->add('localplugins', $settings);

    $settings->add(new admin_setting_heading(
        'local_sfsresources/pageheading',
        get_string('settings:pageheading', 'local_sfsresources'),
        get_string('settings:pageheading_desc', 'local_sfsresources')
    ));

    foreach ([
        'showresourcesheader',
        'showresourcesstats',
        'showresourcestools',
        'showresourcesfilters',
        'showresourceslibrary',
    ] as $name) {
        $settings->add(new admin_setting_configcheckbox(
            "local_sfsresources/{$name}",
            get_string("settings:{$name}", 'local_sfsresources'),
            get_string("settings:{$name}_desc", 'local_sfsresources'),
            1
        ));
    }

    foreach (['resourceskicker', 'resourcestitle', 'resourceslibrarytitle'] as $name) {
        $settings->add(new admin_setting_configtext(
            "local_sfsresources/{$name}",
            get_string("settings:{$name}", 'local_sfsresources'),
            get_string('settings:fallback_desc', 'local_sfsresources'),
            '',
            PARAM_TEXT
        ));
    }

    foreach (['resourceslede', 'resourcesempty'] as $name) {
        $settings->add(new admin_setting_configtextarea(
            "local_sfsresources/{$name}",
            get_string("settings:{$name}", 'local_sfsresources'),
            get_string('settings:fallback_desc', 'local_sfsresources'),
            '',
            PARAM_TEXT
        ));
    }

    $settings->add(new admin_setting_configstoredfile(
        'local_sfsresources/documentfiles',
        get_string('documentfiles', 'local_sfsresources'),
        get_string('documentfiles_desc', 'local_sfsresources'),
        'documents',
        0,
        ['maxfiles' => -1]
    ));

    $settings->add(new admin_setting_configtextarea(
        'local_sfsresources/documents',
        get_string('documents', 'local_sfsresources'),
        get_string('documents_desc', 'local_sfsresources'),
        '',
        PARAM_RAW
    ));

    $settings->add(new admin_setting_configtextarea(
        'local_sfsresources/stats',
        get_string('stats', 'local_sfsresources'),
        get_string('stats_desc', 'local_sfsresources'),
        '',
        PARAM_RAW
    ));
}
