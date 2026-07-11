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
 * Course repository port.
 *
 * @package    local_learningplans
 * @copyright  2026
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
interface course_repository_interface {
    /**
     * Find course by id.
     *
     * @param int $courseid Course id.
     * @return \stdClass|null
     */
    public function find_by_id(int $courseid): ?\stdClass;

    /**
     * List course records by ids.
     *
     * @param array<int, int> $courseids Course ids.
     * @return array<int, \stdClass>
     */
    public function list_by_ids(array $courseids): array;

    /**
     * List all available courses for managers.
     *
     * @return array<int, \stdClass>
     */
    public function list_for_selector(): array;

    /**
     * Is user enrolled in course.
     *
     * @param int $courseid Course id.
     * @param int $userid User id.
     * @return bool
     */
    public function is_user_enrolled(int $courseid, int $userid): bool;
}

