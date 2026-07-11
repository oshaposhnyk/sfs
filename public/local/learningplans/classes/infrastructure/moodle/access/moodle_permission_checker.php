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

namespace local_learningplans\infrastructure\moodle\access;

use local_learningplans\application\port\permission_checker_interface;

defined('MOODLE_INTERNAL') || die();

/**
 * Moodle access API adapter.
 *
 * @package    local_learningplans
 * @copyright  2026
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class moodle_permission_checker implements permission_checker_interface {
    /**
     * @inheritDoc
     */
    public function require_view(): void {
        require_login();
        require_capability('local/learningplans:view', \context_system::instance());
    }

    /**
     * @inheritDoc
     */
    public function require_manage(): void {
        require_login();
        require_capability('local/learningplans:manage', \context_system::instance());
    }

    /**
     * @inheritDoc
     */
    public function require_enrol_users(): void {
        require_login();
        require_capability('local/learningplans:enrolusers', \context_system::instance());
    }

    /**
     * @inheritDoc
     */
    public function require_unenrol_users(): void {
        require_login();
        require_capability('local/learningplans:unenrolusers', \context_system::instance());
    }

    /**
     * @inheritDoc
     */
    public function require_view_progress(): void {
        require_login();
        require_capability('local/learningplans:viewprogress', \context_system::instance());
    }

    /**
     * @inheritDoc
     */
    public function require_course_enrol_permission(int $courseid): void {
        $context = \context_course::instance($courseid, MUST_EXIST);
        require_capability('enrol/learningplan:enrol', $context);
    }
}

