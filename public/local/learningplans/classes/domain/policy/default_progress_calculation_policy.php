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
 * Default progress strategy:
 * completed courses / total courses * 100.
 *
 * @package    local_learningplans
 * @copyright  2026
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class default_progress_calculation_policy implements progress_calculation_policy {
    /**
     * @inheritDoc
     */
    public function calculate(
        array $courses,
        array $completionbycourseid,
        array $enrolmentbycourseid,
        bool $sequentialmode
    ): progress {
        $total = count($courses);
        $completed = 0;
        $inprogress = 0;
        $notstarted = 0;
        $nextcourseid = null;

        foreach ($courses as $course) {
            $courseid = $course->course_id();
            $iscomplete = !empty($completionbycourseid[$courseid]);
            $isenrolled = !empty($enrolmentbycourseid[$courseid]);

            if ($iscomplete) {
                $completed++;
                continue;
            }

            if ($nextcourseid === null && $sequentialmode) {
                $nextcourseid = $courseid;
            }

            if ($isenrolled) {
                $inprogress++;
            } else {
                $notstarted++;
            }
        }

        if ($sequentialmode === false) {
            $nextcourseid = null;
        }

        $percentage = $total > 0 ? round(($completed / $total) * 100, 2) : 0.0;

        return new progress($total, $completed, $inprogress, $notstarted, $percentage, $nextcourseid);
    }
}

