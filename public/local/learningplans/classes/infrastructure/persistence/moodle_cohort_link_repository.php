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

namespace local_learningplans\infrastructure\persistence;

use local_learningplans\application\port\cohort_link_repository_interface;

defined('MOODLE_INTERNAL') || die();

/**
 * Moodle DML repository for plan-to-cohort links.
 *
 * @package    local_learningplans
 * @copyright  2026
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class moodle_cohort_link_repository implements cohort_link_repository_interface {
    /** @var string */
    private const TABLE = 'local_learningplans_coh';

    /**
     * @inheritDoc
     */
    public function link(int $planid, int $cohortid, int $createdby, int $time): bool {
        global $DB;

        if ($DB->record_exists(self::TABLE, ['planid' => $planid, 'cohortid' => $cohortid])) {
            return false;
        }

        $DB->insert_record(self::TABLE, (object)[
            'planid' => $planid,
            'cohortid' => $cohortid,
            'createdby' => $createdby > 0 ? $createdby : null,
            'timecreated' => $time,
        ]);
        return true;
    }

    /**
     * @inheritDoc
     */
    public function unlink(int $planid, int $cohortid): void {
        global $DB;
        $DB->delete_records(self::TABLE, ['planid' => $planid, 'cohortid' => $cohortid]);
    }

    /**
     * @inheritDoc
     */
    public function exists(int $planid, int $cohortid): bool {
        global $DB;
        return $DB->record_exists(self::TABLE, ['planid' => $planid, 'cohortid' => $cohortid]);
    }

    /**
     * @inheritDoc
     */
    public function get_cohort_ids_by_plan(int $planid): array {
        global $DB;
        return array_map('intval', $DB->get_fieldset_select(self::TABLE, 'cohortid', 'planid = :planid', [
            'planid' => $planid,
        ]));
    }

    /**
     * @inheritDoc
     */
    public function get_plan_ids_by_cohort(int $cohortid): array {
        global $DB;
        return array_map('intval', $DB->get_fieldset_select(self::TABLE, 'planid', 'cohortid = :cohortid', [
            'cohortid' => $cohortid,
        ]));
    }

    /**
     * @inheritDoc
     */
    public function get_linked_plan_ids(): array {
        global $DB;
        return array_map('intval', $DB->get_fieldset_sql(
            'SELECT DISTINCT planid FROM {' . self::TABLE . '} ORDER BY planid ASC'
        ));
    }

    /**
     * @inheritDoc
     */
    public function delete_by_plan(int $planid): void {
        global $DB;
        $DB->delete_records(self::TABLE, ['planid' => $planid]);
    }

    /**
     * @inheritDoc
     */
    public function delete_by_cohort(int $cohortid): void {
        global $DB;
        $DB->delete_records(self::TABLE, ['cohortid' => $cohortid]);
    }
}