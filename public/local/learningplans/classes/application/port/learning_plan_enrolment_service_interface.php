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
 * Course enrolment adapter port.
 *
 * @package    local_learningplans
 * @copyright  2026
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
interface learning_plan_enrolment_service_interface {
    /**
     * Ensure user is enrolled in target course via enrol_learningplan instance tied to plan.
     *
     * @param int $planid Plan id.
     * @param int $courseid Course id.
     * @param int $userid User id.
     * @param int $roleid Role id.
     * @return array<string, int|null> Contains enrolid and userenrolmentid.
     */
    public function enrol_user(int $planid, int $courseid, int $userid, int $roleid): array;

    /**
     * Unenrol user from target course via the enrol_learningplan plan-bound instance.
     *
     * @param int $planid Plan id.
     * @param int $courseid Course id.
     * @param int $userid User id.
     * @return void
     */
    public function unenrol_user(int $planid, int $courseid, int $userid): void;
}

