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
 * Stage of a learning plan — an ordered, named block of plan courses.
 *
 * Part of the learning_plan aggregate. Invariants enforced by the
 * aggregate repository: stage names are unique within a plan, the global
 * course order keeps each stage's courses contiguous, and a stage that
 * loses its last course is removed.
 *
 * @package    local_learningplans
 * @copyright  2026
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class learning_plan_stage {
    /** @var int */
    private int $id;

    /** @var int */
    private int $planid;

    /** @var string */
    private string $name;

    /** @var int */
    private int $sortorder;

    /**
     * Constructor.
     *
     * @param int $id Id.
     * @param int $planid Plan id.
     * @param string $name Stage name (non-empty).
     * @param int $sortorder Position of the stage block within the plan.
     */
    public function __construct(int $id, int $planid, string $name, int $sortorder) {
        $this->id = $id;
        $this->planid = $planid;
        $this->name = trim($name);
        $this->sortorder = $sortorder;
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
     * @return string
     */
    public function name(): string {
        return $this->name;
    }

    /**
     * @return int
     */
    public function sort_order(): int {
        return $this->sortorder;
    }
}
