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

namespace local_learningplans\infrastructure\moodle\event;

use local_learningplans\application\port\event_dispatcher_interface;

defined('MOODLE_INTERNAL') || die();

/**
 * Moodle events API adapter.
 *
 * @package    local_learningplans
 * @copyright  2026
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class moodle_event_dispatcher implements event_dispatcher_interface {
    /** @var array<string, string> */
    private const MAP = [
        'learning_plan_created' => '\local_learningplans\event\learning_plan_created',
        'learning_plan_updated' => '\local_learningplans\event\learning_plan_updated',
        'learning_plan_deleted' => '\local_learningplans\event\learning_plan_deleted',
        'learning_plan_archived' => '\local_learningplans\event\learning_plan_archived',
        'course_added' => '\local_learningplans\event\course_added_to_learning_plan',
        'course_removed' => '\local_learningplans\event\course_removed_from_learning_plan',
        'courses_reordered' => '\local_learningplans\event\courses_reordered_in_learning_plan',
        'user_enrolled' => '\local_learningplans\event\user_enrolled_into_learning_plan',
        'user_unenrolled' => '\local_learningplans\event\user_unenrolled_from_learning_plan',
        'user_completed' => '\local_learningplans\event\user_completed_learning_plan',
        'progress_recalculated' => '\local_learningplans\event\user_progress_recalculated',
        'enrolment_failed' => '\local_learningplans\event\learning_plan_enrolment_failed',
        'course_enrolment_created' => '\local_learningplans\event\course_enrolment_created_from_learning_plan',
        'course_enrolment_removed' => '\local_learningplans\event\course_enrolment_removed_from_learning_plan',
        'cohort_linked' => '\local_learningplans\event\cohort_linked_to_learning_plan',
        'cohort_unlinked' => '\local_learningplans\event\cohort_unlinked_from_learning_plan',
    ];

    /**
     * @inheritDoc
     */
    public function dispatch(string $eventkey, array $payload): void {
        if (!isset(self::MAP[$eventkey])) {
            return;
        }

        $classname = self::MAP[$eventkey];
        $objectid = (int)($payload['objectid'] ?? 0);
        if ($objectid <= 0) {
            return;
        }

        $courseid = isset($payload['courseid']) ? (int)$payload['courseid'] : null;
        $context = $courseid ? \context_course::instance($courseid) : \context_system::instance();

        $data = [
            'context' => $context,
            'objectid' => $objectid,
            'other' => isset($payload['other']) && is_array($payload['other']) ? $payload['other'] : [],
        ];
        if (!empty($payload['userid'])) {
            $data['userid'] = (int)$payload['userid'];
        }
        if (!empty($payload['relateduserid'])) {
            $data['relateduserid'] = (int)$payload['relateduserid'];
        }
        if ($courseid) {
            $data['courseid'] = $courseid;
        }

        $event = $classname::create($data);
        $event->trigger();
    }
}
