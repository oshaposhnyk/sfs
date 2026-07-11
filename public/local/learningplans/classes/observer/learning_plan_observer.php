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

namespace local_learningplans\observer;

use local_learningplans\infrastructure\moodle\factory\learning_plan_service_factory;
use local_learningplans\task\reconcile_cohort_plan;

defined('MOODLE_INTERNAL') || die();

/**
 * Core event observer callbacks.
 *
 * @package    local_learningplans
 * @copyright  2026
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class learning_plan_observer {
    /**
     * Handle course completion changes.
     *
     * @param \core\event\course_completed $event Event.
     * @return void
     */
    public static function course_completed(\core\event\course_completed $event): void {
        if (empty($event->relateduserid) || empty($event->courseid)) {
            return;
        }
        $service = learning_plan_service_factory::create();
        $service->handle_course_completed((int)$event->courseid, (int)$event->relateduserid);
    }

    /**
     * Handle course deletion.
     *
     * @param \core\event\course_deleted $event Event.
     * @return void
     */
    public static function course_deleted(\core\event\course_deleted $event): void {
        $courseid = (int)($event->objectid ?? 0);
        if ($courseid <= 0) {
            return;
        }
        $service = learning_plan_service_factory::create();
        $service->handle_course_deleted($courseid);
    }

    /**
     * Handle user deletion.
     *
     * @param \core\event\user_deleted $event Event.
     * @return void
     */
    public static function user_deleted(\core\event\user_deleted $event): void {
        $userid = (int)($event->objectid ?? 0);
        if ($userid <= 0) {
            return;
        }
        $service = learning_plan_service_factory::create();
        $service->handle_user_deleted($userid);
    }

    /**
     * Handle user enrolment deletion.
     *
     * @param \core\event\user_enrolment_deleted $event Event.
     * @return void
     */
    public static function user_enrolment_deleted(\core\event\user_enrolment_deleted $event): void {
        $userenrolmentid = (int)($event->objectid ?? 0);
        if ($userenrolmentid <= 0) {
            return;
        }
        $service = learning_plan_service_factory::create();
        $service->handle_user_enrolment_deleted($userenrolmentid);
    }

    /**
     * Handle a user being added to a cohort.
     *
     * Kept deliberately light: it only queues a background reconciliation for
     * each affected plan so that bulk cohort changes never enrol users inline.
     *
     * @param \core\event\cohort_member_added $event Event.
     * @return void
     */
    public static function cohort_member_added(\core\event\cohort_member_added $event): void {
        self::queue_cohort_reconcile((int)$event->objectid);
    }

    /**
     * Handle a user being removed from a cohort.
     *
     * @param \core\event\cohort_member_removed $event Event.
     * @return void
     */
    public static function cohort_member_removed(\core\event\cohort_member_removed $event): void {
        self::queue_cohort_reconcile((int)$event->objectid);
    }

    /**
     * Handle a cohort being deleted: drop links and reconcile affected plans.
     *
     * @param \core\event\cohort_deleted $event Event.
     * @return void
     */
    public static function cohort_deleted(\core\event\cohort_deleted $event): void {
        $cohortid = (int)($event->objectid ?? 0);
        if ($cohortid <= 0) {
            return;
        }
        $service = learning_plan_service_factory::create();
        $planids = $service->handle_cohort_deleted($cohortid);
        foreach ($planids as $planid) {
            reconcile_cohort_plan::queue((int)$planid);
        }
    }

    /**
     * Queue a reconcile task for every plan linked to the given cohort.
     *
     * @param int $cohortid Cohort id.
     * @return void
     */
    private static function queue_cohort_reconcile(int $cohortid): void {
        if ($cohortid <= 0) {
            return;
        }
        $service = learning_plan_service_factory::create();
        foreach ($service->get_plan_ids_for_cohort($cohortid) as $planid) {
            reconcile_cohort_plan::queue((int)$planid);
        }
    }
}

