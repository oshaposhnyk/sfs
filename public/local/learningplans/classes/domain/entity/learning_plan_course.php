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

namespace local_learningplans\domain\entity;

defined('MOODLE_INTERNAL') || die();

/**
 * Course item in a learning plan.
 *
 * @package    local_learningplans
 * @copyright  2026
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class learning_plan_course {
    /** @var int */
    private int $id;

    /** @var int */
    private int $planid;

    /** @var int */
    private int $courseid;

    /** @var int */
    private int $sortorder;

    /** @var bool */
    private bool $required;

    /** @var string */
    private string $stagename;

    /**
     * Constructor.
     *
     * @param int $id Id.
     * @param int $planid Plan id.
     * @param int $courseid Course id.
     * @param int $sortorder Sort order.
     * @param bool $required Required flag.
     * @param string $stagename Stage grouping name ('' = unnamed stage).
     */
    public function __construct(
        int $id,
        int $planid,
        int $courseid,
        int $sortorder,
        bool $required = true,
        string $stagename = ''
    ) {
        $this->id = $id;
        $this->planid = $planid;
        $this->courseid = $courseid;
        $this->sortorder = $sortorder;
        $this->required = $required;
        $this->stagename = trim($stagename);
    }

    /**
     * @return int
     */
    public function id(): int {
        return $this->id;
    }

    /**
     * @return int
     */
    public function plan_id(): int {
        return $this->planid;
    }

    /**
     * @return int
     */
    public function course_id(): int {
        return $this->courseid;
    }

    /**
     * @return int
     */
    public function sort_order(): int {
        return $this->sortorder;
    }

    /**
     * @return bool
     */
    public function is_required(): bool {
        return $this->required;
    }

    public function stage_name(): string {
        return $this->stagename;
    }
}

