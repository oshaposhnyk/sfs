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

use local_learningplans\application\port\learning_plan_membership_repository_interface;
use local_learningplans\domain\entity\learning_plan_membership;
use local_learningplans\domain\value\membership_source;
use local_learningplans\domain\value\membership_status;

defined('MOODLE_INTERNAL') || die();

/**
 * Moodle DML learning plan membership repository.
 *
 * @package    local_learningplans
 * @copyright  2026
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class moodle_learning_plan_membership_repository implements learning_plan_membership_repository_interface {
    /** @var string */
    private const TABLE = 'local_learningplans_mem';

    /**
     * @inheritDoc
     */
    public function upsert_active(
        int $planid,
        int $userid,
        int $enrolledby,
        int $time,
        string $source
    ): learning_plan_membership {
        global $DB;

        $source = membership_source::normalize($source);
        $enrolledby = $enrolledby > 0 ? $enrolledby : null;

        $record = $DB->get_record(self::TABLE, ['planid' => $planid, 'userid' => $userid], '*', IGNORE_MISSING);
        if ($record) {
            $record->status = membership_status::ACTIVE;
            $record->timemodified = $time;
            $record->timecompleted = null;
            // A manual membership always wins: never let an automatic cohort
            // sync downgrade an explicit manual enrolment or rewrite its actor.
            if (!($source === membership_source::COHORT && (string)$record->source === membership_source::MANUAL)) {
                $record->source = $source;
                $record->enrolledby = $enrolledby;
            }
            $DB->update_record(self::TABLE, $record);
            return $this->find_by_id((int)$record->id);
        }

        $record = (object)[
            'planid' => $planid,
            'userid' => $userid,
            'status' => membership_status::ACTIVE,
            'source' => $source,
            'enrolledby' => $enrolledby,
            'timecreated' => $time,
            'timemodified' => $time,
            'timecompleted' => null,
        ];
        $id = (int)$DB->insert_record(self::TABLE, $record);
        return $this->find_by_id($id);
    }

    /**
     * @inheritDoc
     */
    public function deactivate(int $planid, int $userid, int $time): ?learning_plan_membership {
        global $DB;
        $record = $DB->get_record(self::TABLE, ['planid' => $planid, 'userid' => $userid], '*', IGNORE_MISSING);
        if (!$record) {
            return null;
        }
        $record->status = membership_status::INACTIVE;
        $record->timemodified = $time;
        $DB->update_record(self::TABLE, $record);
        return $this->find_by_id((int)$record->id);
    }

    /**
     * @inheritDoc
     */
    public function find_by_plan_and_user(int $planid, int $userid): ?learning_plan_membership {
        global $DB;
        $record = $DB->get_record(self::TABLE, ['planid' => $planid, 'userid' => $userid], '*', IGNORE_MISSING);
        if (!$record) {
            return null;
        }
        return $this->to_entity($record);
    }

    /**
     * @inheritDoc
     */
    public function find_by_id(int $membershipid): ?learning_plan_membership {
        global $DB;
        $record = $DB->get_record(self::TABLE, ['id' => $membershipid], '*', IGNORE_MISSING);
        if (!$record) {
            return null;
        }
        return $this->to_entity($record);
    }

    /**
     * @inheritDoc
     */
    public function list_by_plan(int $planid, bool $activeonly = false): array {
        global $DB;
        $conditions = ['planid' => $planid];
        if ($activeonly) {
            $conditions['status'] = membership_status::ACTIVE;
        }
        $records = $DB->get_records(self::TABLE, $conditions, 'timecreated ASC, id ASC');
        return array_map(function($record) {
            return $this->to_entity($record);
        }, array_values($records));
    }

    /**
     * @inheritDoc
     */
    public function list_active_by_user(int $userid): array {
        global $DB;
        $records = $DB->get_records(self::TABLE, ['userid' => $userid, 'status' => membership_status::ACTIVE], 'timecreated ASC');
        return array_map(function($record) {
            return $this->to_entity($record);
        }, array_values($records));
    }

    /**
     * @inheritDoc
     */
    public function list_active_by_plan_and_source(int $planid, string $source): array {
        global $DB;
        $records = $DB->get_records(self::TABLE, [
            'planid' => $planid,
            'status' => membership_status::ACTIVE,
            'source' => membership_source::normalize($source),
        ], 'timecreated ASC, id ASC');
        return array_map(function($record) {
            return $this->to_entity($record);
        }, array_values($records));
    }

    /**
     * @inheritDoc
     */
    public function count_active_by_plan(int $planid): int {
        global $DB;
        return (int)$DB->count_records(self::TABLE, ['planid' => $planid, 'status' => membership_status::ACTIVE]);
    }

    /**
     * @inheritDoc
     */
    public function mark_completed(int $membershipid, int $time): void {
        global $DB;
        $DB->execute(
            'UPDATE {' . self::TABLE . '}
                SET timecompleted = :timecompleted, timemodified = :timemodified
              WHERE id = :id',
            [
                'timecompleted' => $time,
                'timemodified' => $time,
                'id' => $membershipid,
            ]
        );
    }

    /**
     * @inheritDoc
     */
    public function clear_completed(int $membershipid): void {
        global $DB;
        $DB->set_field(self::TABLE, 'timecompleted', null, ['id' => $membershipid]);
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
    public function deactivate_by_user(int $userid, int $time): void {
        global $DB;
        $DB->execute(
            'UPDATE {' . self::TABLE . '}
                SET status = :newstatus, timemodified = :timemodified
              WHERE userid = :userid
                AND status = :oldstatus',
            [
                'newstatus' => membership_status::INACTIVE,
                'timemodified' => $time,
                'userid' => $userid,
                'oldstatus' => membership_status::ACTIVE,
            ]
        );
    }

    /**
     * Map DB row to entity.
     *
     * @param \stdClass $record Row.
     * @return learning_plan_membership
     */
    private function to_entity(\stdClass $record): learning_plan_membership {
        return new learning_plan_membership(
            (int)$record->id,
            (int)$record->planid,
            (int)$record->userid,
            (int)$record->status,
            (string)($record->source ?? membership_source::MANUAL),
            (int)$record->timecreated,
            (int)$record->timemodified,
            isset($record->timecompleted) ? (int)$record->timecompleted : null
        );
    }
}
