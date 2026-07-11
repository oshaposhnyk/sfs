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

use local_learningplans\domain\value\progress;

defined('MOODLE_INTERNAL') || die();

/**
 * Progress cache/storage port.
 *
 * @package    local_learningplans
 * @copyright  2026
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
interface learning_plan_progress_repository_interface {
    /**
     * Get cached progress.
     *
     * @param int $planid Plan id.
     * @param int $userid User id.
     * @return progress|null
     */
    public function get(int $planid, int $userid): ?progress;

    /**
     * Save progress.
     *
     * @param int $planid Plan id.
     * @param int $userid User id.
     * @param progress $progress Progress object.
     * @param int $calculatedat Calculation time.
     * @return void
     */
    public function save(int $planid, int $userid, progress $progress, int $calculatedat): void;

    /**
     * Invalidate one plan-user key.
     *
     * @param int $planid Plan id.
     * @param int $userid User id.
     * @return void
     */
    public function invalidate(int $planid, int $userid): void;

    /**
     * Invalidate entire plan.
     *
     * @param int $planid Plan id.
     * @return void
     */
    public function invalidate_plan(int $planid): void;

    /**
     * Delete all plan rows.
     *
     * @param int $planid Plan id.
     * @return void
     */
    public function delete_by_plan(int $planid): void;

    /**
     * Find stale snapshots.
     *
     * @param int $olderthan Timestamp threshold.
     * @param int $limit Max rows.
     * @return array<int, \stdClass>
     */
    public function find_stale(int $olderthan, int $limit = 1000): array;
}

