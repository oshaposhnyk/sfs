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

use local_learningplans\domain\entity\learning_plan_membership;

defined('MOODLE_INTERNAL') || die();

/**
 * Membership repository port.
 *
 * @package    local_learningplans
 * @copyright  2026
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
interface learning_plan_membership_repository_interface {
    /**
     * Create or reactivate membership.
     *
     * A manual membership is never downgraded to a cohort membership: when an
     * existing record is already manually sourced, the cohort source is ignored.
     *
     * @param int $planid Plan id.
     * @param int $userid User id.
     * @param int $enrolledby Actor id (0 for system/cohort).
     * @param int $time Timestamp.
     * @param string $source Membership provenance.
     * @return learning_plan_membership
     */
    public function upsert_active(
        int $planid,
        int $userid,
        int $enrolledby,
        int $time,
        string $source
    ): learning_plan_membership;

    /**
     * Deactivate membership.
     *
     * @param int $planid Plan id.
     * @param int $userid User id.
     * @param int $time Timestamp.
     * @return learning_plan_membership|null
     */
    public function deactivate(int $planid, int $userid, int $time): ?learning_plan_membership;

    /**
     * Find membership by plan and user.
     *
     * @param int $planid Plan id.
     * @param int $userid User id.
     * @return learning_plan_membership|null
     */
    public function find_by_plan_and_user(int $planid, int $userid): ?learning_plan_membership;

    /**
     * Find membership by id.
     *
     * @param int $membershipid Membership id.
     * @return learning_plan_membership|null
     */
    public function find_by_id(int $membershipid): ?learning_plan_membership;

    /**
     * List memberships by plan.
     *
     * @param int $planid Plan id.
     * @param bool $activeonly Active only.
     * @return array<int, learning_plan_membership>
     */
    public function list_by_plan(int $planid, bool $activeonly = false): array;

    /**
     * List active memberships by user.
     *
     * @param int $userid User id.
     * @return array<int, learning_plan_membership>
     */
    public function list_active_by_user(int $userid): array;

    /**
     * List active memberships in a plan with a given provenance.
     *
     * @param int $planid Plan id.
     * @param string $source Membership provenance.
     * @return array<int, learning_plan_membership>
     */
    public function list_active_by_plan_and_source(int $planid, string $source): array;

    /**
     * Count active memberships in plan.
     *
     * @param int $planid Plan id.
     * @return int
     */
    public function count_active_by_plan(int $planid): int;

    /**
     * Set completion timestamp.
     *
     * @param int $membershipid Membership id.
     * @param int $time Timestamp.
     * @return void
     */
    public function mark_completed(int $membershipid, int $time): void;

    /**
     * Clear completion timestamp.
     *
     * @param int $membershipid Membership id.
     * @return void
     */
    public function clear_completed(int $membershipid): void;

    /**
     * Delete all memberships in plan.
     *
     * @param int $planid Plan id.
     * @return void
     */
    public function delete_by_plan(int $planid): void;

    /**
     * Deactivate all memberships for user.
     *
     * @param int $userid User id.
     * @param int $time Timestamp.
     * @return void
     */
    public function deactivate_by_user(int $userid, int $time): void;
}

