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
 * Callbacks for local_sfsgame.
 *
 * @package    local_sfsgame
 * @copyright  2026 SecureFood School
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

declare(strict_types=1);

defined('MOODLE_INTERNAL') || die();

/**
 * Add the Future Food page to the global navigation with its own icon.
 *
 * @param global_navigation $navigation Global navigation.
 * @return void
 */
function local_sfsgame_extend_navigation(global_navigation $navigation): void {
    if (!isloggedin() || isguestuser()) {
        return;
    }

    $navigation->add(
        get_string('pluginname', 'local_sfsgame'),
        new moodle_url('/local/sfsgame/index.php'),
        navigation_node::TYPE_CUSTOM,
        null,
        'local_sfsgame',
        new pix_icon('icon', '', 'local_sfsgame')
    );
}
