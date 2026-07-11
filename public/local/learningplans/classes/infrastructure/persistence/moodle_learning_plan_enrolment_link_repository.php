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

use local_learningplans\application\port\learning_plan_enrolment_link_repository_interface;
use local_learningplans\domain\value\membership_status;

defined('MOODLE_INTERNAL') || die();

/**
 * Moodle DML repository for plan course enrolment links.
 *
 * @package    local_learningplans
 * @copyright  2026
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class moodle_learning_plan_enrolment_link_repository implements learning_plan_enrolment_link_repository_interface {
    /** @var string */
    private const TABLE = 'local_learningplans_enrl';

    /**
     * @inheritDoc
     */
    public function upsert_active(
        int $membershipid,
        int $planid,
        int $courseid,
        int $userid,
        int $enrolid,
        ?int $userenrolmentid,
        int $time
    ): void {
        global $DB;

        $record = $DB->get_record(self::TABLE, ['membershipid' => $membershipid, 'courseid' => $courseid], '*', IGNORE_MISSING);
        if ($record) {
            $record->userid = $userid;
            $record->planid = $planid;
            $record->enrolid = $enrolid;
            $record->userenrolmentid = $userenrolmentid;
            $record->status = membership_status::ACTIVE;
            $record->timemodified = $time;
            $DB->update_record(self::TABLE, $record);
            return;
        }

        $DB->insert_record(self::TABLE, (object)[
            'membershipid' => $membershipid,
            'planid' => $planid,
            'courseid' => $courseid,
            'userid' => $userid,
            'enrolid' => $enrolid,
            'userenrolmentid' => $userenrolmentid,
            'status' => membership_status::ACTIVE,
            'timecreated' => $time,
            'timemodified' => $time,
        ]);
    }

    /**
     * @inheritDoc
     */
    public function list_by_membership(int $membershipid, bool $activeonly = false): array {
        global $DB;
        $conditions = ['membershipid' => $membershipid];
        if ($activeonly) {
            $conditions['status'] = membership_status::ACTIVE;
        }
        return array_values($DB->get_records(self::TABLE, $conditions, 'id ASC'));
    }

    /**
     * @inheritDoc
     */
    public function mark_removed(int $membershipid, int $courseid, int $time): void {
        global $DB;
        $DB->execute(
            'UPDATE {' . self::TABLE . '}
                SET status = :status, timemodified = :timemodified
              WHERE membershipid = :membershipid
                AND courseid = :courseid',
            [
                'status' => membership_status::INACTIVE,
                'timemodified' => $time,
                'membershipid' => $membershipid,
                'courseid' => $courseid,
            ]
        );
    }

    /**
     * @inheritDoc
     */
    public function mark_removed_by_membership(int $membershipid, int $time): void {
        global $DB;
        $DB->execute(
            'UPDATE {' . self::TABLE . '}
                SET status = :status, timemodified = :timemodified
              WHERE membershipid = :membershipid',
            [
                'status' => membership_status::INACTIVE,
                'timemodified' => $time,
                'membershipid' => $membershipid,
            ]
        );
    }

    /**
     * @inheritDoc
     */
    public function find_by_userenrolmentid(int $userenrolmentid): ?\stdClass {
        global $DB;
        $record = $DB->get_record(self::TABLE, ['userenrolmentid' => $userenrolmentid], '*', IGNORE_MISSING);
        return $record ?: null;
    }

    /**
     * @inheritDoc
     */
    public function mark_removed_by_userenrolmentid(int $userenrolmentid, int $time): void {
        global $DB;
        $DB->execute(
            'UPDATE {' . self::TABLE . '}
                SET status = :status, timemodified = :timemodified
              WHERE userenrolmentid = :userenrolmentid',
            [
                'status' => membership_status::INACTIVE,
                'timemodified' => $time,
                'userenrolmentid' => $userenrolmentid,
            ]
        );
    }

    /**
     * @inheritDoc
     */
    public function delete_by_plan(int $planid): void {
        global $DB;
        $DB->delete_records(self::TABLE, ['planid' => $planid]);
    }
}
