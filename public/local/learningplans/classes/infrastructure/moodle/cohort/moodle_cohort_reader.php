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

namespace local_learningplans\infrastructure\moodle\cohort;

use local_learningplans\application\port\cohort_reader_interface;

defined('MOODLE_INTERNAL') || die();

/**
 * Cohort read adapter backed by Moodle cohort tables and API.
 *
 * @package    local_learningplans
 * @copyright  2026
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class moodle_cohort_reader implements cohort_reader_interface {
    /**
     * @inheritDoc
     */
    public function list_member_ids(int $cohortid): array {
        global $DB;
        return array_map('intval', $DB->get_fieldset_select('cohort_members', 'userid', 'cohortid = :cohortid', [
            'cohortid' => $cohortid,
        ]));
    }

    /**
     * @inheritDoc
     */
    public function exists(int $cohortid): bool {
        global $DB;
        return $DB->record_exists('cohort', ['id' => $cohortid]);
    }

    /**
     * @inheritDoc
     */
    public function get_name(int $cohortid): ?string {
        global $DB;
        $name = $DB->get_field('cohort', 'name', ['id' => $cohortid], IGNORE_MISSING);
        return $name === false ? null : (string)$name;
    }

    /**
     * @inheritDoc
     */
    public function list_for_selector(): array {
        global $CFG;
        require_once($CFG->dirroot . '/cohort/lib.php');

        $result = cohort_get_all_cohorts(0, 0);
        $options = [];
        foreach ($result['cohorts'] as $cohort) {
            $options[(int)$cohort->id] = format_string($cohort->name);
        }
        return $options;
    }
}