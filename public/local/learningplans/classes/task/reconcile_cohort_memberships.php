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
 * Scheduled task: reconcile cohort-driven memberships across all linked plans.
 *
 * A periodic safety net that repairs any drift the event observers may have
 * missed (events lost, plans re-enabled after archiving, members added while
 * cohort sync was disabled, etc.). Reconciliation is idempotent.
 *
 * @package    local_learningplans
 * @copyright  2026
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class reconcile_cohort_memberships extends \core\task\scheduled_task {
    /**
     * @inheritDoc
     */
    public function get_name() {
        return get_string('task:reconcilecohorts', 'local_learningplans');
    }

    /**
     * @inheritDoc
     */
    public function execute() {
        $service = learning_plan_service_factory::create();
        $service->reconcile_all();
    }
}