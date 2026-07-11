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
use local_learningplans\domain\exception\domain_exception;
use local_learningplans\domain\value\enrolment_mode;

defined('MOODLE_INTERNAL') || die();

/**
 * Domain tests for learning plan aggregate.
 *
 * @package    local_learningplans
 * @category   test
 * @copyright  2026
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class learning_plan_domain_test extends \basic_testcase {
    /**
     * Empty plan name must be rejected.
     */
    public function test_empty_name_rejected(): void {
        $this->expectException(domain_exception::class);
        new learning_plan(null, '', '', true, false, enrolment_mode::IMMEDIATE, 1, 1, 1);
    }

    /**
     * Valid plan data should be accepted.
     */
    public function test_valid_plan(): void {
        $plan = new learning_plan(10, 'Plan A', 'Description', true, true, enrolment_mode::SEQUENTIAL_RELEASE, 2, 3, 4);
        $this->assertSame(10, $plan->id());
        $this->assertTrue($plan->sequential_mode());
        $this->assertSame(enrolment_mode::SEQUENTIAL_RELEASE, $plan->enrolment_mode());
    }
}

