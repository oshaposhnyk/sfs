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

/**
 * External: current user's Student Lab overview (Phase 6.3).
 *
 * @package    local_learningplans
 * @copyright  2026
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

declare(strict_types=1);

namespace local_learningplans\external;

use core_external\external_api;
use core_external\external_function_parameters;
use core_external\external_multiple_structure;
use core_external\external_single_structure;
use core_external\external_value;
use local_learningplans\infrastructure\moodle\factory\learning_plan_service_factory;

/**
 * Own-data read model: plans, stages, courses, continue target.
 */
final class get_student_lab extends external_api {

    /**
     * Parameters.
     *
     * @return external_function_parameters
     */
    public static function execute_parameters(): external_function_parameters {
        return new external_function_parameters([]);
    }

    /**
     * Execute.
     *
     * @return array
     */
    public static function execute(): array {
        global $USER;

        self::validate_parameters(self::execute_parameters(), []);
        $context = \context_system::instance();
        self::validate_context($context);
        require_capability('local/learningplans:viewprogress', $context);

        $overview = learning_plan_service_factory::student_lab_overview()->execute((int)$USER->id);

        return [
            'hasplans' => $overview['hasplans'],
            'plans' => array_map(static fn(array $plan): array => [
                'planid' => $plan['planid'],
                'name' => \core_external\util::format_string($plan['name'], \context_system::instance()),
                'active' => $plan['active'],
                'completed' => $plan['completed'],
                'total' => $plan['total'],
            ], $overview['plans']),
            'progress' => $overview['progress'] ?? ['total' => 0, 'completed' => 0, 'percentage' => 0],
            'continueindex' => $overview['continueindex'],
            'stages' => array_map(static fn(array $stage): array => [
                'name' => $stage['name'],
                'status' => $stage['status'],
                'completed' => $stage['completed'],
                'total' => $stage['total'],
            ], $overview['stages'] ?? []),
            'courses' => array_map(static fn(array $course): array => [
                'courseid' => $course['courseid'],
                'fullname' => \core_external\util::format_string($course['fullname'], \context_system::instance()),
                'stagename' => $course['stagename'],
                'position' => $course['position'],
                'status' => $course['status'],
                'action' => $course['action'],
                'percentage' => (int)round($course['percentage']),
            ], $overview['courses']),
        ];
    }

    /**
     * Return description.
     *
     * @return external_single_structure
     */
    public static function execute_returns(): external_single_structure {
        return new external_single_structure([
            'hasplans' => new external_value(PARAM_BOOL, 'Whether the user belongs to any plan'),
            'plans' => new external_multiple_structure(new external_single_structure([
                'planid' => new external_value(PARAM_INT, 'Plan id'),
                'name' => new external_value(PARAM_TEXT, 'Plan name'),
                'active' => new external_value(PARAM_BOOL, 'Is the active plan'),
                'completed' => new external_value(PARAM_INT, 'Completed courses'),
                'total' => new external_value(PARAM_INT, 'Total courses'),
            ])),
            'progress' => new external_single_structure([
                'total' => new external_value(PARAM_INT, 'Total courses in the active plan'),
                'completed' => new external_value(PARAM_INT, 'Completed courses in the active plan'),
                'percentage' => new external_value(PARAM_INT, 'Completion percentage'),
            ]),
            'continueindex' => new external_value(PARAM_INT, 'Index of the continue-learning course', VALUE_OPTIONAL, null, NULL_ALLOWED),
            'stages' => new external_multiple_structure(new external_single_structure([
                'name' => new external_value(PARAM_TEXT, 'Stage name ("" = unnamed)'),
                'status' => new external_value(PARAM_ALPHA, 'done, locked or active'),
                'completed' => new external_value(PARAM_INT, 'Completed courses in the stage'),
                'total' => new external_value(PARAM_INT, 'Courses in the stage'),
            ])),
            'courses' => new external_multiple_structure(new external_single_structure([
                'courseid' => new external_value(PARAM_INT, 'Course id'),
                'fullname' => new external_value(PARAM_TEXT, 'Course name'),
                'stagename' => new external_value(PARAM_TEXT, 'Stage name'),
                'position' => new external_value(PARAM_INT, '1-based position in the plan'),
                'status' => new external_value(PARAM_ALPHA, 'done, active, upnext or locked'),
                'action' => new external_value(PARAM_ALPHA, 'review, resume, start or none'),
                'percentage' => new external_value(PARAM_INT, 'Course completion percentage'),
            ])),
        ]);
    }
}
