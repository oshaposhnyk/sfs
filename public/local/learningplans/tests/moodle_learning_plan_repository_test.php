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

namespace local_learningplans;

use local_learningplans\domain\entity\learning_plan;
use local_learningplans\domain\value\enrolment_mode;
use local_learningplans\infrastructure\persistence\moodle_learning_plan_repository;

defined('MOODLE_INTERNAL') || die();

/**
 * Repository integration tests.
 *
 * @package    local_learningplans
 * @category   test
 * @copyright  2026
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class moodle_learning_plan_repository_test extends \advanced_testcase {
    /**
     * Add/remove/reorder course flow.
     */
    public function test_add_remove_and_reorder_courses(): void {
        $this->resetAfterTest();
        $repository = new moodle_learning_plan_repository();

        $plan = $repository->create(new learning_plan(
            null,
            'Plan A',
            '',
            true,
            true,
            enrolment_mode::IMMEDIATE,
            1,
            time(),
            time()
        ));

        $course1 = $this->getDataGenerator()->create_course();
        $course2 = $this->getDataGenerator()->create_course();
        $course3 = $this->getDataGenerator()->create_course();

        $repository->add_course((int)$plan->id(), (int)$course1->id);
        $repository->add_course((int)$plan->id(), (int)$course2->id);
        $repository->add_course((int)$plan->id(), (int)$course3->id);
        $this->assertSame(3, $repository->count_courses((int)$plan->id()));

        $repository->move_course((int)$plan->id(), (int)$course3->id, -1);
        $courses = $repository->get_courses((int)$plan->id());
        $this->assertSame((int)$course1->id, $courses[0]->course_id());
        $this->assertSame((int)$course3->id, $courses[1]->course_id());
        $this->assertSame((int)$course2->id, $courses[2]->course_id());

        $repository->remove_course((int)$plan->id(), (int)$course1->id);
        $courses = $repository->get_courses((int)$plan->id());
        $this->assertCount(2, $courses);
        $this->assertSame((int)$course3->id, $courses[0]->course_id());
        $this->assertSame((int)$course2->id, $courses[1]->course_id());
    }

    /**
     * Stage rename round-trip and the not-in-plan guard.
     */
    public function test_set_course_stage(): void {
        $this->resetAfterTest();
        $repository = new moodle_learning_plan_repository();

        $plan = $repository->create(new learning_plan(
            null,
            'Plan B',
            '',
            true,
            true,
            enrolment_mode::IMMEDIATE,
            1,
            time(),
            time()
        ));

        $course = $this->getDataGenerator()->create_course();
        $repository->add_course((int)$plan->id(), (int)$course->id, 'Stage 1');
        $this->assertSame('Stage 1', $repository->get_courses((int)$plan->id())[0]->stage_name());

        $repository->set_course_stage((int)$plan->id(), (int)$course->id, '  Stage 2 · Practice  ');
        $this->assertSame('Stage 2 · Practice', $repository->get_courses((int)$plan->id())[0]->stage_name());

        // Clearing the stage.
        $repository->set_course_stage((int)$plan->id(), (int)$course->id, '');
        $this->assertSame('', $repository->get_courses((int)$plan->id())[0]->stage_name());

        // A course that is not part of the plan is rejected.
        $other = $this->getDataGenerator()->create_course();
        $this->expectException(\moodle_exception::class);
        $repository->set_course_stage((int)$plan->id(), (int)$other->id, 'X');
    }
}

