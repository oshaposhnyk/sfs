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

use local_learningplans\domain\exception\domain_exception;
use local_learningplans\domain\value\enrolment_mode;

defined('MOODLE_INTERNAL') || die();

/**
 * Learning plan aggregate root.
 *
 * @package    local_learningplans
 * @copyright  2026
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class learning_plan {
    /** @var int|null */
    private ?int $id;

    /** @var string */
    private string $name;

    /** @var string */
    private string $description;

    /** @var bool */
    private bool $enabled;

    /** @var bool */
    private bool $sequentialmode;

    /** @var string */
    private string $enrolmentmode;

    /** @var int */
    private int $createdby;

    /** @var int */
    private int $timecreated;

    /** @var int */
    private int $timemodified;

    /**
     * Constructor.
     *
     * @param int|null $id Plan id.
     * @param string $name Plan name.
     * @param string $description Description.
     * @param bool $enabled Enabled.
     * @param bool $sequentialmode Sequential mode.
     * @param string $enrolmentmode Enrolment mode.
     * @param int $createdby Creator id.
     * @param int $timecreated Created timestamp.
     * @param int $timemodified Updated timestamp.
     */
    public function __construct(
        ?int $id,
        string $name,
        string $description,
        bool $enabled,
        bool $sequentialmode,
        string $enrolmentmode,
        int $createdby,
        int $timecreated,
        int $timemodified
    ) {
        $this->assert_name($name);
        $this->id = $id;
        $this->name = trim($name);
        $this->description = $description;
        $this->enabled = $enabled;
        $this->sequentialmode = $sequentialmode;
        $this->enrolmentmode = enrolment_mode::normalize($enrolmentmode);
        $this->createdby = $createdby;
        $this->timecreated = max(0, $timecreated);
        $this->timemodified = max(0, $timemodified);
    }

    /**
     * @return int|null
     */
    public function id(): ?int {
        return $this->id;
    }

    /**
     * @return string
     */
    public function name(): string {
        return $this->name;
    }

    /**
     * @return string
     */
    public function description(): string {
        return $this->description;
    }

    /**
     * @return bool
     */
    public function enabled(): bool {
        return $this->enabled;
    }

    /**
     * @return bool
     */
    public function sequential_mode(): bool {
        return $this->sequentialmode;
    }

    /**
     * @return string
     */
    public function enrolment_mode(): string {
        return $this->enrolmentmode;
    }

    /**
     * @return int
     */
    public function created_by(): int {
        return $this->createdby;
    }

    /**
     * @return int
     */
    public function time_created(): int {
        return $this->timecreated;
    }

    /**
     * @return int
     */
    public function time_modified(): int {
        return $this->timemodified;
    }

    /**
     * Validate plan name.
     *
     * @param string $name Name.
     * @return void
     */
    private function assert_name(string $name): void {
        if (trim($name) === '') {
            throw new domain_exception('Learning plan name is required.');
        }
    }
}

