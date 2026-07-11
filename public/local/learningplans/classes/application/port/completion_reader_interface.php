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

namespace local_learningplans\application\port;

defined('MOODLE_INTERNAL') || die();

/**
 * Completion read port.
 *
 * @package    local_learningplans
 * @copyright  2026
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
interface completion_reader_interface {
    /**
     * Is course complete for user.
     *
     * @param int $courseid Course id.
     * @param int $userid User id.
     * @return bool
     */
    public function is_course_complete(int $courseid, int $userid): bool;

    /**
     * Course progress percentage if available.
     *
     * @param int $courseid Course id.
     * @param int $userid User id.
     * @return float|null
     */
    public function get_course_progress_percentage(int $courseid, int $userid): ?float;
}

