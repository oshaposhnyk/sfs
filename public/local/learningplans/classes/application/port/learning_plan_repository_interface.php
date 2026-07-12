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

namespace local_learningplans\application\port;

use local_learningplans\domain\entity\learning_plan;
use local_learningplans\domain\entity\learning_plan_course;

defined('MOODLE_INTERNAL') || die();

/**
 * Repository port for learning plans.
 *
 * @package    local_learningplans
 * @copyright  2026
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
interface learning_plan_repository_interface {
    /**
     * Persist new plan.
     *
     * @param learning_plan $plan Plan.
     * @return learning_plan
     */
    public function create(learning_plan $plan): learning_plan;

    /**
     * Persist existing plan.
     *
     * @param learning_plan $plan Plan.
     * @return learning_plan
     */
    public function update(learning_plan $plan): learning_plan;

    /**
     * Find plan by id.
     *
     * @param int $planid Plan id.
     * @return learning_plan|null
     */
    public function find_by_id(int $planid): ?learning_plan;

    /**
     * List all plans.
     *
     * @return array<int, learning_plan>
     */
    public function list_all(): array;

    /**
     * Delete plan.
     *
     * @param int $planid Plan id.
     * @return void
     */
    public function delete(int $planid): void;

    /**
     * Add course to plan.
     *
     * @param int $planid Plan id.
     * @param int $courseid Course id.
     * @return learning_plan_course
     */
    public function add_course(int $planid, int $courseid, string $stagename = ''): learning_plan_course;

    /**
     * Remove course from plan.
     *
     * @param int $planid Plan id.
     * @param int $courseid Course id.
     * @return void
     */
    public function remove_course(int $planid, int $courseid): void;

    /**
     * Move course in order.
     *
     * @param int $planid Plan id.
     * @param int $courseid Course id.
     * @param int $direction Negative for up, positive for down.
     * @return void
     */
    public function move_course(int $planid, int $courseid, int $direction): void;

    /**
     * Reorder all courses.
     *
     * @param int $planid Plan id.
     * @param array<int, int> $orderedcourseids Course ids in final order.
     * @return void
     */
    public function reorder_courses(int $planid, array $orderedcourseids): void;

    /**
     * Get ordered courses for plan.
     *
     * @param int $planid Plan id.
     * @return array<int, learning_plan_course>
     */
    public function get_courses(int $planid): array;

    /**
     * Count courses in plan.
     *
     * @param int $planid Plan id.
     * @return int
     */
    public function count_courses(int $planid): int;

    /**
     * Find plan ids that include a course.
     *
     * @param int $courseid Course id.
     * @return array<int, int>
     */
    public function get_plan_ids_by_course(int $courseid): array;

    /**
     * Move a plan course into the stage with the given name.
     *
     * Find-or-create semantics: an unknown name creates the stage, '' moves
     * the course out of any stage. The course is placed at the end of its
     * new block, the global order is re-normalised (stage blocks stay
     * contiguous) and stages left without courses are removed.
     *
     * @param int $planid Plan id.
     * @param int $courseid Course id.
     * @param string $stagename Target stage name ('' = unstaged).
     * @return void
     */
    public function set_course_stage(int $planid, int $courseid, string $stagename): void;

    /**
     * Stages of a plan, ordered by their block position.
     *
     * @param int $planid Plan id.
     * @return \local_learningplans\domain\entity\learning_plan_stage[]
     */
    public function get_stages(int $planid): array;

    /**
     * Apply a full new structure: course order plus stage assignment.
     *
     * $stageids is parallel to $orderedcourseids (0/null = unstaged). The
     * repository re-derives stage block order from first appearance, keeps
     * blocks contiguous and removes emptied stages.
     *
     * @param int $planid Plan id.
     * @param array<int, int> $orderedcourseids Every plan course id, in order.
     * @param array<int, int> $stageids Stage id per course (0 = unstaged).
     * @return void
     */
    public function restructure_courses(int $planid, array $orderedcourseids, array $stageids): void;
}

