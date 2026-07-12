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
 * Student Lab overview read model (ADR-008).
 *
 * @package    local_learningplans
 * @copyright  2026
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

declare(strict_types=1);

namespace local_learningplans\application\usecase;

use local_learningplans\application\port\completion_reader_interface;
use local_learningplans\application\port\course_repository_interface;
use local_learningplans\application\port\user_preference_repository_interface;
use local_learningplans\application\service\learning_plan_service;
use local_learningplans\domain\policy\student_lab_status_policy;

defined('MOODLE_INTERNAL') || die();

/**
 * Assembles everything the Student Lab page needs for one user in one call.
 *
 * Return shape:
 * [
 *   'hasplans'   => bool,
 *   'plans'      => [['planid', 'name', 'active', 'completed', 'total'], …],
 *   'activeplan' => ['planid', 'name', 'sequential']|null,
 *   'progress'   => ['total', 'completed', 'percentage']|null,
 *   'continueindex' => int|null,
 *   'courses'    => [['courseid', 'fullname', 'summary', 'position',
 *                     'status', 'action', 'percentage'], …],
 * ]
 */
final class get_student_lab_overview {
    /** @var learning_plan_service */
    private learning_plan_service $service;

    /** @var completion_reader_interface */
    private completion_reader_interface $completionreader;

    /** @var course_repository_interface */
    private course_repository_interface $courserepository;

    /** @var user_preference_repository_interface */
    private user_preference_repository_interface $preferences;

    /**
     * @param learning_plan_service $service Application service.
     * @param completion_reader_interface $completionreader Completion reader.
     * @param course_repository_interface $courserepository Course repository.
     * @param user_preference_repository_interface $preferences Preference repository.
     */
    public function __construct(
        learning_plan_service $service,
        completion_reader_interface $completionreader,
        course_repository_interface $courserepository,
        user_preference_repository_interface $preferences
    ) {
        $this->service = $service;
        $this->completionreader = $completionreader;
        $this->courserepository = $courserepository;
        $this->preferences = $preferences;
    }

    /**
     * Execute use case.
     *
     * @param int $userid User whose Student Lab is shown (their own).
     * @return array See class docblock.
     */
    public function execute(int $userid): array {
        $memberships = $this->service->get_user_memberships($userid);
        if ($memberships === []) {
            return [
                'hasplans' => false,
                'plans' => [],
                'activeplan' => null,
                'progress' => null,
                'continueindex' => null,
                'courses' => [],
            ];
        }

        $planids = array_map(static fn($membership): int => $membership->plan_id(), $memberships);
        $activeplanid = $this->preferences->get_active_plan_id($userid);
        if ($activeplanid === null || !in_array($activeplanid, $planids, true)) {
            $activeplanid = $planids[0];
        }

        $plans = [];
        foreach ($planids as $planid) {
            $plan = $this->service->get_plan($planid);
            $planprogress = $this->service->get_user_progress($planid, $userid);
            $plans[] = [
                'planid' => $planid,
                'name' => $plan->name(),
                'active' => $planid === $activeplanid,
                'completed' => $planprogress->completed_courses(),
                'total' => $planprogress->total_courses(),
            ];
        }

        $activeplan = $this->service->get_plan($activeplanid);
        $progress = $this->service->get_user_progress($activeplanid, $userid);

        $plancourses = $this->service->get_plan_courses($activeplanid);
        $courserecords = $this->courserepository->list_by_ids(
            array_map(static fn($item): int => $item->course_id(), $plancourses)
        );

        $states = [];
        foreach ($plancourses as $planitem) {
            $courseid = $planitem->course_id();
            $states[] = [
                'complete' => $this->completionreader->is_course_complete($courseid, $userid),
                'enrolled' => $this->courserepository->is_user_enrolled($courseid, $userid),
                'percentage' => $this->completionreader->get_course_progress_percentage($courseid, $userid),
            ];
        }
        $resolved = student_lab_status_policy::resolve($states);

        $courses = [];
        foreach ($plancourses as $index => $planitem) {
            $courseid = $planitem->course_id();
            $record = $courserecords[$courseid] ?? null;
            $percentage = $states[$index]['percentage'];
            if ($states[$index]['complete']) {
                $percentage = 100.0;
            }
            $courses[] = [
                'courseid' => $courseid,
                'fullname' => $record->fullname ?? '',
                'summary' => (string)($record->summary ?? ''),
                'position' => $index + 1,
                'status' => $resolved[$index]['status'],
                'action' => $resolved[$index]['action'],
                'percentage' => $percentage !== null ? (float)$percentage : 0.0,
            ];
        }

        return [
            'hasplans' => true,
            'plans' => $plans,
            'activeplan' => [
                'planid' => $activeplanid,
                'name' => $activeplan->name(),
                'sequential' => $activeplan->sequential_mode(),
            ],
            'progress' => [
                'total' => $progress->total_courses(),
                'completed' => $progress->completed_courses(),
                'percentage' => (int)round($progress->percentage()),
            ],
            'continueindex' => student_lab_status_policy::continue_index($resolved),
            'courses' => $courses,
        ];
    }
}
