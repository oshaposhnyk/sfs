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
 * Repository port for enrolment link records.
 *
 * @package    local_learningplans
 * @copyright  2026
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
interface learning_plan_enrolment_link_repository_interface {
    /**
     * Upsert active link.
     *
     * @param int $membershipid Membership id.
     * @param int $planid Plan id.
     * @param int $courseid Course id.
     * @param int $userid User id.
     * @param int $enrolid Enrol instance id.
     * @param int|null $userenrolmentid User enrolment id.
     * @param int $time Timestamp.
     * @return void
     */
    public function upsert_active(
        int $membershipid,
        int $planid,
        int $courseid,
        int $userid,
        int $enrolid,
        ?int $userenrolmentid,
        int $time
    ): void;

    /**
     * List links by membership.
     *
     * @param int $membershipid Membership id.
     * @param bool $activeonly Active only.
     * @return array<int, \stdClass>
     */
    public function list_by_membership(int $membershipid, bool $activeonly = false): array;

    /**
     * Mark link removed.
     *
     * @param int $membershipid Membership id.
     * @param int $courseid Course id.
     * @param int $time Timestamp.
     * @return void
     */
    public function mark_removed(int $membershipid, int $courseid, int $time): void;

    /**
     * Mark links removed for membership.
     *
     * @param int $membershipid Membership id.
     * @param int $time Timestamp.
     * @return void
     */
    public function mark_removed_by_membership(int $membershipid, int $time): void;

    /**
     * Find link by user enrolment id.
     *
     * @param int $userenrolmentid User enrolment id.
     * @return \stdClass|null
     */
    public function find_by_userenrolmentid(int $userenrolmentid): ?\stdClass;

    /**
     * Mark removed by user enrolment id.
     *
     * @param int $userenrolmentid User enrolment id.
     * @param int $time Timestamp.
     * @return void
     */
    public function mark_removed_by_userenrolmentid(int $userenrolmentid, int $time): void;

    /**
     * Delete links for plan.
     *
     * @param int $planid Plan id.
     * @return void
     */
    public function delete_by_plan(int $planid): void;
}

