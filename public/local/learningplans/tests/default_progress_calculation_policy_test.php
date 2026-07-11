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

use local_learningplans\domain\entity\learning_plan_course;
use local_learningplans\domain\policy\default_progress_calculation_policy;

defined('MOODLE_INTERNAL') || die();

/**
 * Tests for default progress strategy.
 *
 * @package    local_learningplans
 * @category   test
 * @copyright  2026
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class default_progress_calculation_policy_test extends \basic_testcase {
    /**
     * Basic completed/total percentage and counts.
     */
    public function test_progress_counts_and_percentage(): void {
        $policy = new default_progress_calculation_policy();
        $courses = [
            new learning_plan_course(1, 10, 100, 1),
            new learning_plan_course(2, 10, 101, 2),
            new learning_plan_course(3, 10, 102, 3),
        ];
        $completion = [100 => true, 101 => false, 102 => false];
        $enrolment = [100 => true, 101 => true, 102 => false];

        $progress = $policy->calculate($courses, $completion, $enrolment, true);
        $this->assertSame(3, $progress->total_courses());
        $this->assertSame(1, $progress->completed_courses());
        $this->assertSame(1, $progress->inprogress_courses());
        $this->assertSame(1, $progress->notstarted_courses());
        $this->assertSame(33.33, $progress->percentage());
        $this->assertSame(101, $progress->next_course_id());
    }

    /**
     * Next course should not be provided for non-sequential mode.
     */
    public function test_next_course_not_set_in_non_sequential_mode(): void {
        $policy = new default_progress_calculation_policy();
        $courses = [new learning_plan_course(1, 10, 100, 1)];
        $progress = $policy->calculate($courses, [100 => false], [100 => false], false);
        $this->assertNull($progress->next_course_id());
    }
}

