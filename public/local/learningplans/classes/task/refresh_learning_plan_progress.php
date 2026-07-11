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
 * Scheduled task: refresh stale learning plan progress snapshots.
 *
 * @package    local_learningplans
 * @copyright  2026
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class refresh_learning_plan_progress extends \core\task\scheduled_task {
    /**
     * @inheritDoc
     */
    public function get_name() {
        return get_string('task:refreshprogress', 'local_learningplans');
    }

    /**
     * @inheritDoc
     */
    public function execute() {
        $service = learning_plan_service_factory::create();
        $service->refresh_stale_progress(time() - 300, 500);
    }
}

