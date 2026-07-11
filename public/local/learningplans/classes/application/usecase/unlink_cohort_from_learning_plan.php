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

namespace local_learningplans\application\usecase;

use local_learningplans\application\service\learning_plan_service;

defined('MOODLE_INTERNAL') || die();

/**
 * Unlink a cohort from a learning plan.
 */
final class unlink_cohort_from_learning_plan {
    /** @var learning_plan_service */
    private learning_plan_service $service;

    /**
     * @param learning_plan_service $service Service.
     */
    public function __construct(learning_plan_service $service) {
        $this->service = $service;
    }

    /**
     * Execute use case.
     *
     * @param int $planid Plan id.
     * @param int $cohortid Cohort id.
     * @param int $actorid Actor id.
     * @return void
     */
    public function execute(int $planid, int $cohortid, int $actorid): void {
        $this->service->unlink_cohort($planid, $cohortid, $actorid);
    }
}