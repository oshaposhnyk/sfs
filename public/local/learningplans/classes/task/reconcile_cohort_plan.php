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

namespace local_learningplans\task;

use local_learningplans\infrastructure\moodle\factory\learning_plan_service_factory;

defined('MOODLE_INTERNAL') || die();

/**
 * Adhoc task: reconcile cohort-driven memberships for a single plan.
 *
 * Queued by cohort membership observers and by the link/unlink controller. The
 * reconciliation itself runs here, off the web request, so that adding a large
 * cohort never blocks or times out the triggering request. Identical pending
 * tasks (same plan) are de-duplicated when queued.
 *
 * @package    local_learningplans
 * @copyright  2026
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class reconcile_cohort_plan extends \core\task\adhoc_task {
    /**
     * Queue a reconciliation for a plan, de-duplicating identical pending tasks.
     *
     * @param int $planid Plan id.
     * @return void
     */
    public static function queue(int $planid): void {
        $task = new self();
        $task->set_custom_data((object)['planid' => $planid]);
        $task->set_component('local_learningplans');
        \core\task\manager::queue_adhoc_task($task, true);
    }

    /**
     * @inheritDoc
     */
    public function execute() {
        $data = $this->get_custom_data();
        $planid = (int)($data->planid ?? 0);
        if ($planid <= 0) {
            return;
        }

        $service = learning_plan_service_factory::create();
        $service->reconcile_plan($planid);
    }
}