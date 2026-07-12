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
 * Course plan-context read model (Phase 6.1).
 *
 * @package    local_learningplans
 * @copyright  2026
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

declare(strict_types=1);

namespace local_learningplans\application\usecase;

use local_learningplans\application\port\completion_reader_interface;
use local_learningplans\application\port\learning_plan_membership_repository_interface;
use local_learningplans\application\port\learning_plan_repository_interface;

defined('MOODLE_INTERNAL') || die();

/**
 * Answers: how does this course sit inside the viewer's learning plan?
 *
 * Own-data only (the user's active memberships); returns null when the
 * course belongs to none of the user's plans. No permission gate beyond
 * membership itself — the data describes the user's own journey.
 */
final class get_course_plan_context {
    /** @var learning_plan_repository_interface */
    private learning_plan_repository_interface $plans;

    /** @var learning_plan_membership_repository_interface */
    private learning_plan_membership_repository_interface $memberships;

    /** @var completion_reader_interface */
    private completion_reader_interface $completion;

    /**
     * @param learning_plan_repository_interface $plans Plan repository.
     * @param learning_plan_membership_repository_interface $memberships Membership repository.
     * @param completion_reader_interface $completion Completion reader.
     */
    public function __construct(
        learning_plan_repository_interface $plans,
        learning_plan_membership_repository_interface $memberships,
        completion_reader_interface $completion
    ) {
        $this->plans = $plans;
        $this->memberships = $memberships;
        $this->completion = $completion;
    }

    /**
     * Execute use case.
     *
     * @param int $courseid Course being viewed.
     * @param int $userid Viewing user.
     * @return array|null ['planname', 'position', 'total', 'percentage'] or null.
     */
    public function execute(int $courseid, int $userid): ?array {
        $planids = $this->plans->get_plan_ids_by_course($courseid);
        if ($planids === []) {
            return null;
        }

        foreach ($this->memberships->list_active_by_user($userid) as $membership) {
            if (!in_array($membership->plan_id(), array_map('intval', $planids), true)) {
                continue;
            }
            $plan = $this->plans->find_by_id($membership->plan_id());
            if ($plan === null) {
                continue;
            }
            $courses = $this->plans->get_courses($membership->plan_id());
            $position = 0;
            foreach ($courses as $index => $item) {
                if ($item->course_id() === $courseid) {
                    $position = $index + 1;
                    break;
                }
            }
            $percentage = $this->completion->get_course_progress_percentage($courseid, $userid);
            if ($this->completion->is_course_complete($courseid, $userid)) {
                $percentage = 100.0;
            }
            return [
                'planname' => $plan->name(),
                'position' => $position,
                'total' => count($courses),
                'percentage' => $percentage !== null ? (int)round($percentage) : 0,
            ];
        }
        return null;
    }
}
