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
use local_learningplans\domain\entity\learning_plan;

defined('MOODLE_INTERNAL') || die();

/**
 * Update learning plan use case.
 */
final class update_learning_plan {
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
     * @param string $name Name.
     * @param string $description Description.
     * @param bool $enabled Enabled.
     * @param bool $sequentialmode Sequential mode.
     * @param string $enrolmentmode Enrolment mode.
     * @param int $actorid Actor id.
     * @return learning_plan
     */
    public function execute(
        int $planid,
        string $name,
        string $description,
        bool $enabled,
        bool $sequentialmode,
        string $enrolmentmode,
        int $actorid
    ): learning_plan {
        return $this->service->update_plan($planid, $name, $description, $enabled, $sequentialmode, $enrolmentmode, $actorid);
    }
}

