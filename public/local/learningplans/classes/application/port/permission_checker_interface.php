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
 * Permission checker port.
 *
 * @package    local_learningplans
 * @copyright  2026
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
interface permission_checker_interface {
    /**
     * Ensure user can view plans.
     *
     * @return void
     */
    public function require_view(): void;

    /**
     * Ensure user can manage plans.
     *
     * @return void
     */
    public function require_manage(): void;

    /**
     * Ensure user can enrol plan members.
     *
     * @return void
     */
    public function require_enrol_users(): void;

    /**
     * Ensure user can unenrol plan members.
     *
     * @return void
     */
    public function require_unenrol_users(): void;

    /**
     * Ensure user can view progress.
     *
     * @return void
     */
    public function require_view_progress(): void;

    /**
     * Ensure caller can perform course-level enrol actions.
     *
     * @param int $courseid Course id.
     * @return void
     */
    public function require_course_enrol_permission(int $courseid): void;
}

