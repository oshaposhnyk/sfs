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

namespace local_learningplans\application\port;

defined('MOODLE_INTERNAL') || die();

/**
 * Read port for Moodle cohorts.
 *
 * @package    local_learningplans
 * @copyright  2026
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
interface cohort_reader_interface {
    /**
     * Member user ids of a cohort.
     *
     * @param int $cohortid Cohort id.
     * @return array<int, int>
     */
    public function list_member_ids(int $cohortid): array;

    /**
     * Whether a cohort exists.
     *
     * @param int $cohortid Cohort id.
     * @return bool
     */
    public function exists(int $cohortid): bool;

    /**
     * Display name of a cohort, or null if it no longer exists.
     *
     * @param int $cohortid Cohort id.
     * @return string|null
     */
    public function get_name(int $cohortid): ?string;

    /**
     * Cohorts visible for linking, as id => name.
     *
     * @return array<int, string>
     */
    public function list_for_selector(): array;
}