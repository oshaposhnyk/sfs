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

namespace local_learningplans\domain\policy;

use local_learningplans\domain\entity\learning_plan_course;
use local_learningplans\domain\value\progress;

defined('MOODLE_INTERNAL') || die();

/**
 * Policy interface for learning plan progress calculations.
 *
 * @package    local_learningplans
 * @copyright  2026
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
interface progress_calculation_policy {
    /**
     * Calculate progress.
     *
     * @param array<int, learning_plan_course> $courses Ordered plan courses.
     * @param array<int, bool> $completionbycourseid Course completion flags.
     * @param array<int, bool> $enrolmentbycourseid Course enrolment flags.
     * @param bool $sequentialmode Sequential mode.
     * @return progress
     */
    public function calculate(
        array $courses,
        array $completionbycourseid,
        array $enrolmentbycourseid,
        bool $sequentialmode
    ): progress;
}

