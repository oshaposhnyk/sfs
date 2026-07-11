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

use local_learningplans\domain\value\membership_source;
use local_learningplans\domain\value\membership_status;

defined('MOODLE_INTERNAL') || die();

/**
 * Learning plan membership.
 *
 * @package    local_learningplans
 * @copyright  2026
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class learning_plan_membership {
    /** @var int */
    private int $id;

    /** @var int */
    private int $planid;

    /** @var int */
    private int $userid;

    /** @var int */
    private int $status;

    /** @var string */
    private string $source;

    /** @var int */
    private int $timecreated;

    /** @var int */
    private int $timemodified;

    /** @var int|null */
    private ?int $timecompleted;

    /**
     * Constructor.
     *
     * @param int $id Membership id.
     * @param int $planid Plan id.
     * @param int $userid User id.
     * @param int $status Status.
     * @param string $source Membership provenance.
     * @param int $timecreated Time created.
     * @param int $timemodified Time modified.
     * @param int|null $timecompleted Completion time.
     */
    public function __construct(
        int $id,
        int $planid,
        int $userid,
        int $status,
        string $source,
        int $timecreated,
        int $timemodified,
        ?int $timecompleted
    ) {
        $this->id = $id;
        $this->planid = $planid;
        $this->userid = $userid;
        $this->status = $status;
        $this->source = membership_source::normalize($source);
        $this->timecreated = $timecreated;
        $this->timemodified = $timemodified;
        $this->timecompleted = $timecompleted;
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
    public function user_id(): int {
        return $this->userid;
    }

    /**
     * @return bool
     */
    public function is_active(): bool {
        return $this->status === membership_status::ACTIVE;
    }

    /**
     * @return string
     */
    public function source(): string {
        return $this->source;
    }

    /**
     * Whether the membership was created automatically from a cohort.
     *
     * @return bool
     */
    public function is_cohort_sourced(): bool {
        return $this->source === membership_source::COHORT;
    }

    /**
     * @return int|null
     */
    public function time_completed(): ?int {
        return $this->timecompleted;
    }
}

