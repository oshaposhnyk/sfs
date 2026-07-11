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
 * Repository port for plan-to-cohort links.
 *
 * @package    local_learningplans
 * @copyright  2026
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
interface cohort_link_repository_interface {
    /**
     * Link a cohort to a plan. No-op if the link already exists.
     *
     * @param int $planid Plan id.
     * @param int $cohortid Cohort id.
     * @param int $createdby Actor id (0 for system).
     * @param int $time Timestamp.
     * @return bool True when a new link was created.
     */
    public function link(int $planid, int $cohortid, int $createdby, int $time): bool;

    /**
     * Remove a cohort link from a plan.
     *
     * @param int $planid Plan id.
     * @param int $cohortid Cohort id.
     * @return void
     */
    public function unlink(int $planid, int $cohortid): void;

    /**
     * Whether a plan is linked to a cohort.
     *
     * @param int $planid Plan id.
     * @param int $cohortid Cohort id.
     * @return bool
     */
    public function exists(int $planid, int $cohortid): bool;

    /**
     * Cohort ids linked to a plan.
     *
     * @param int $planid Plan id.
     * @return array<int, int>
     */
    public function get_cohort_ids_by_plan(int $planid): array;

    /**
     * Plan ids linked to a cohort.
     *
     * @param int $cohortid Cohort id.
     * @return array<int, int>
     */
    public function get_plan_ids_by_cohort(int $cohortid): array;

    /**
     * Distinct plan ids that have at least one cohort link.
     *
     * @return array<int, int>
     */
    public function get_linked_plan_ids(): array;

    /**
     * Delete all cohort links for a plan.
     *
     * @param int $planid Plan id.
     * @return void
     */
    public function delete_by_plan(int $planid): void;

    /**
     * Delete all links for a cohort.
     *
     * @param int $cohortid Cohort id.
     * @return void
     */
    public function delete_by_cohort(int $cohortid): void;
}