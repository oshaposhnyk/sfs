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
 * Unit tests for the Student Lab status policy.
 *
 * @package    local_learningplans
 * @copyright  2026
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

declare(strict_types=1);

namespace local_learningplans;

use local_learningplans\domain\policy\student_lab_status_policy;

/**
 * Tests for {@see student_lab_status_policy}.
 *
 * @covers \local_learningplans\domain\policy\student_lab_status_policy
 */
final class student_lab_status_policy_test extends \basic_testcase {

    /**
     * Build one course state.
     *
     * @param bool $complete Completed.
     * @param bool $enrolled Enrolled.
     * @param float|null $percentage Progress percentage.
     * @return array
     */
    private static function state(bool $complete, bool $enrolled, ?float $percentage): array {
        return ['complete' => $complete, 'enrolled' => $enrolled, 'percentage' => $percentage];
    }

    public function test_sequential_pathway_statuses(): void {
        $resolved = student_lab_status_policy::resolve([
            self::state(true, true, 100.0),
            self::state(true, true, 100.0),
            self::state(false, true, 40.0),
            self::state(false, false, null),
        ]);

        $this->assertSame(['done', 'review'], array_values($resolved[0]));
        $this->assertSame(['done', 'review'], array_values($resolved[1]));
        $this->assertSame(['active', 'resume'], array_values($resolved[2]));
        $this->assertSame(['locked', 'none'], array_values($resolved[3]));
        $this->assertSame(2, student_lab_status_policy::continue_index($resolved));
    }

    public function test_immediate_mode_first_incomplete_is_active(): void {
        $resolved = student_lab_status_policy::resolve([
            self::state(false, true, null),
            self::state(false, true, 0.0),
            self::state(false, true, null),
        ]);

        $this->assertSame('active', $resolved[0]['status']);
        $this->assertSame('start', $resolved[0]['action']);
        $this->assertSame('upnext', $resolved[1]['status']);
        $this->assertSame('upnext', $resolved[2]['status']);
        $this->assertSame(0, student_lab_status_policy::continue_index($resolved));
    }

    public function test_completed_plan_continues_to_review(): void {
        $resolved = student_lab_status_policy::resolve([
            self::state(true, true, 100.0),
            self::state(true, true, 100.0),
        ]);
        $this->assertSame(0, student_lab_status_policy::continue_index($resolved));
    }

    public function test_all_locked_has_no_continue_target(): void {
        $resolved = student_lab_status_policy::resolve([
            self::state(false, false, null),
        ]);
        $this->assertNull(student_lab_status_policy::continue_index($resolved));
    }

    public function test_empty_plan(): void {
        $this->assertSame([], student_lab_status_policy::resolve([]));
        $this->assertNull(student_lab_status_policy::continue_index([]));
    }
}
