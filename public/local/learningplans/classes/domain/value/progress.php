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

namespace local_learningplans\domain\value;

defined('MOODLE_INTERNAL') || die();

/**
 * Progress value object.
 *
 * @package    local_learningplans
 * @copyright  2026
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class progress {
    /** @var int */
    private int $totalcourses;

    /** @var int */
    private int $completedcourses;

    /** @var int */
    private int $inprogresscourses;

    /** @var int */
    private int $notstartedcourses;

    /** @var float */
    private float $percentage;

    /** @var int|null */
    private ?int $nextcourseid;

    /**
     * Constructor.
     *
     * @param int $totalcourses Total courses.
     * @param int $completedcourses Completed courses.
     * @param int $inprogresscourses In-progress courses.
     * @param int $notstartedcourses Not-started courses.
     * @param float $percentage Completion percentage.
     * @param int|null $nextcourseid Next required course id.
     */
    public function __construct(
        int $totalcourses,
        int $completedcourses,
        int $inprogresscourses,
        int $notstartedcourses,
        float $percentage,
        ?int $nextcourseid
    ) {
        $this->totalcourses = max(0, $totalcourses);
        $this->completedcourses = max(0, $completedcourses);
        $this->inprogresscourses = max(0, $inprogresscourses);
        $this->notstartedcourses = max(0, $notstartedcourses);
        $this->percentage = max(0.0, min(100.0, $percentage));
        $this->nextcourseid = $nextcourseid !== null ? max(1, $nextcourseid) : null;
    }

    /**
     * Total courses.
     *
     * @return int
     */
    public function total_courses(): int {
        return $this->totalcourses;
    }

    /**
     * Completed courses.
     *
     * @return int
     */
    public function completed_courses(): int {
        return $this->completedcourses;
    }

    /**
     * In-progress courses.
     *
     * @return int
     */
    public function inprogress_courses(): int {
        return $this->inprogresscourses;
    }

    /**
     * Not-started courses.
     *
     * @return int
     */
    public function notstarted_courses(): int {
        return $this->notstartedcourses;
    }

    /**
     * Completion percentage.
     *
     * @return float
     */
    public function percentage(): float {
        return $this->percentage;
    }

    /**
     * Next required course id.
     *
     * @return int|null
     */
    public function next_course_id(): ?int {
        return $this->nextcourseid;
    }

    /**
     * Is plan completed.
     *
     * @return bool
     */
    public function is_completed(): bool {
        return $this->totalcourses > 0 && $this->completedcourses >= $this->totalcourses;
    }
}

