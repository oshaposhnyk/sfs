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
     * Stage entity lifecycle: find-or-create, rename move, GC, guard.
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
        $planid = (int)$plan->id();

        $course = $this->getDataGenerator()->create_course();
        $repository->add_course($planid, (int)$course->id, 'Stage 1');
        $this->assertSame('Stage 1', $repository->get_courses($planid)[0]->stage_name());
        $this->assertCount(1, $repository->get_stages($planid));
        $stageid = $repository->get_courses($planid)[0]->stage_id();
        $this->assertNotNull($stageid);

        // Moving to a new name creates the stage and GCs the emptied one.
        $repository->set_course_stage($planid, (int)$course->id, '  Stage 2 · Practice  ');
        $this->assertSame('Stage 2 · Practice', $repository->get_courses($planid)[0]->stage_name());
        $stages = $repository->get_stages($planid);
        $this->assertCount(1, $stages);
        $this->assertSame('Stage 2 · Practice', $stages[0]->name());
        $this->assertNotEquals($stageid, $stages[0]->id());

        // Same name resolves to the same stage entity (find-or-create).
        $course2 = $this->getDataGenerator()->create_course();
        $repository->add_course($planid, (int)$course2->id, 'Stage 2 · Practice');
        $this->assertCount(1, $repository->get_stages($planid));
        $courses = $repository->get_courses($planid);
        $this->assertSame($courses[0]->stage_id(), $courses[1]->stage_id());

        // Clearing unassigns; the stage survives while course2 remains in it.
        $repository->set_course_stage($planid, (int)$course->id, '');
        $courses = $repository->get_courses($planid);
        foreach ($courses as $courseitem) {
            if ($courseitem->course_id() === (int)$course->id) {
                $this->assertNull($courseitem->stage_id());
                $this->assertSame('', $courseitem->stage_name());
            }
        }
        $this->assertCount(1, $repository->get_stages($planid));

        // A course that is not part of the plan is rejected.
        $other = $this->getDataGenerator()->create_course();
        $this->expectException(\moodle_exception::class);
        $repository->set_course_stage($planid, (int)$other->id, 'X');
    }

    /**
     * Restructure: order + stage assignment in one operation, with the
     * contiguity invariant and stage sortorder mirroring block order.
     */
    public function test_restructure_courses(): void {
        $this->resetAfterTest();
        $repository = new moodle_learning_plan_repository();

        $plan = $repository->create(new learning_plan(
            null,
            'Plan C',
            '',
            true,
            true,
            enrolment_mode::IMMEDIATE,
            1,
            time(),
            time()
        ));
        $planid = (int)$plan->id();

        $c1 = $this->getDataGenerator()->create_course();
        $c2 = $this->getDataGenerator()->create_course();
        $c3 = $this->getDataGenerator()->create_course();
        $repository->add_course($planid, (int)$c1->id, 'Alpha');
        $repository->add_course($planid, (int)$c2->id, 'Alpha');
        $repository->add_course($planid, (int)$c3->id, 'Beta');

        $stages = $repository->get_stages($planid);
        $alpha = (int)$stages[0]->id();
        $beta = (int)$stages[1]->id();

        // Drag c1 from Alpha into Beta (after c3), Beta block first.
        $repository->restructure_courses(
            $planid,
            [(int)$c3->id, (int)$c1->id, (int)$c2->id],
            [$beta, $beta, $alpha]
        );

        $courses = $repository->get_courses($planid);
        $this->assertSame([(int)$c3->id, (int)$c1->id, (int)$c2->id],
            array_map(static fn($c) => $c->course_id(), $courses));
        $this->assertSame(['Beta', 'Beta', 'Alpha'],
            array_map(static fn($c) => $c->stage_name(), $courses));

        // Stage block order now: Beta first, Alpha second.
        $stages = $repository->get_stages($planid);
        $this->assertSame(['Beta', 'Alpha'], array_map(static fn($s) => $s->name(), $stages));

        // Emptying Alpha garbage-collects it.
        $repository->restructure_courses(
            $planid,
            [(int)$c3->id, (int)$c1->id, (int)$c2->id],
            [$beta, $beta, $beta]
        );
        $stages = $repository->get_stages($planid);
        $this->assertCount(1, $stages);
        $this->assertSame('Beta', $stages[0]->name());

        // A permutation mismatch is rejected.
        $this->expectException(\moodle_exception::class);
        $repository->restructure_courses($planid, [(int)$c1->id], [$beta]);
    }
}

