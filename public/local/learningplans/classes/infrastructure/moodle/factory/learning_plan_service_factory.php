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

namespace local_learningplans\infrastructure\moodle\factory;

use local_learningplans\application\service\learning_plan_service;
use local_learningplans\domain\policy\default_progress_calculation_policy;
use local_learningplans\infrastructure\moodle\access\moodle_permission_checker;
use local_learningplans\infrastructure\moodle\cohort\moodle_cohort_reader;
use local_learningplans\infrastructure\moodle\completion\moodle_completion_reader;
use local_learningplans\infrastructure\moodle\enrolment\moodle_learning_plan_enrolment_service;
use local_learningplans\infrastructure\moodle\event\moodle_event_dispatcher;
use local_learningplans\infrastructure\moodle\time\moodle_clock;
use local_learningplans\infrastructure\moodle\transaction\moodle_transaction_manager;
use local_learningplans\infrastructure\persistence\moodle_cohort_link_repository;
use local_learningplans\infrastructure\persistence\moodle_course_repository;
use local_learningplans\infrastructure\persistence\moodle_learning_plan_enrolment_link_repository;
use local_learningplans\infrastructure\persistence\moodle_learning_plan_membership_repository;
use local_learningplans\infrastructure\persistence\moodle_learning_plan_progress_repository;
use local_learningplans\infrastructure\persistence\moodle_learning_plan_repository;
use local_learningplans\infrastructure\persistence\moodle_user_repository;

defined('MOODLE_INTERNAL') || die();

/**
 * Factory for application service graph.
 *
 * @package    local_learningplans
 * @copyright  2026
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class learning_plan_service_factory {
    /**
     * Build service with default Moodle adapters.
     *
     * @return learning_plan_service
     */
    public static function create(): learning_plan_service {
        return new learning_plan_service(
            new moodle_learning_plan_repository(),
            new moodle_learning_plan_membership_repository(),
            new moodle_learning_plan_enrolment_link_repository(),
            new moodle_learning_plan_progress_repository(),
            new moodle_cohort_link_repository(),
            new moodle_learning_plan_enrolment_service(),
            new moodle_course_repository(),
            new moodle_user_repository(),
            new moodle_cohort_reader(),
            new moodle_completion_reader(),
            new moodle_permission_checker(),
            new moodle_event_dispatcher(),
            new moodle_transaction_manager(),
            new moodle_clock(),
            new default_progress_calculation_policy()
        );
    }
}

