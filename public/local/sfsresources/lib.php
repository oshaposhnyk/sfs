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
 * Callbacks for local_sfsresources.
 *
 * @package    local_sfsresources
 * @copyright  2026 SecureFood School
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

declare(strict_types=1);

defined('MOODLE_INTERNAL') || die();

/**
 * Add the Resources & Standards page to the global navigation with its icon.
 *
 * @param global_navigation $navigation Global navigation.
 * @return void
 */
function local_sfsresources_extend_navigation(global_navigation $navigation): void {
    if (!isloggedin() || isguestuser()) {
        return;
    }

    $navigation->add(
        get_string('pluginname', 'local_sfsresources'),
        new moodle_url('/local/sfsresources/index.php'),
        navigation_node::TYPE_CUSTOM,
        null,
        'local_sfsresources',
        new pix_icon('icon', '', 'local_sfsresources')
    );
}

/**
 * Serve library documents (Phase 6.4, owner-approved filearea storage).
 *
 * Every download re-checks login here — audience tags on the page are
 * presentation hints, not access control.
 *
 * @param stdClass $course Course.
 * @param stdClass|null $cm Course module.
 * @param context $context Context.
 * @param string $filearea File area.
 * @param array $args Path arguments.
 * @param bool $forcedownload Force download flag.
 * @param array $options Options.
 * @return bool False when not found.
 */
function local_sfsresources_pluginfile(
    $course,
    $cm,
    context $context,
    string $filearea,
    array $args,
    bool $forcedownload,
    array $options = []
): bool {
    if ($context->contextlevel !== CONTEXT_SYSTEM || $filearea !== 'documents') {
        return false;
    }
    require_login(null, false);
    if (isguestuser()) {
        return false;
    }

    $itemid = (int)array_shift($args);
    $filename = array_pop($args);
    $filepath = $args ? '/' . implode('/', $args) . '/' : '/';

    $file = get_file_storage()->get_file(
        $context->id, 'local_sfsresources', 'documents', $itemid, $filepath, $filename
    );
    if (!$file || $file->is_directory()) {
        return false;
    }

    send_stored_file($file, 0, 0, $forcedownload, $options);
    return true;
}
