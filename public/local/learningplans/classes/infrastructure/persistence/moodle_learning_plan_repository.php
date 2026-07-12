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

use local_learningplans\application\port\learning_plan_repository_interface;
use local_learningplans\domain\entity\learning_plan;
use local_learningplans\domain\entity\learning_plan_course;

defined('MOODLE_INTERNAL') || die();

/**
 * Moodle DML learning plan repository adapter.
 *
 * @package    local_learningplans
 * @copyright  2026
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class moodle_learning_plan_repository implements learning_plan_repository_interface {
    /** @var string */
    private const TABLE_PLAN = 'local_learningplans_plan';

    /** @var string */
    private const TABLE_COURSE = 'local_learningplans_crs';

    /**
     * @inheritDoc
     */
    public function create(learning_plan $plan): learning_plan {
        global $DB;
        $record = (object)[
            'name' => $plan->name(),
            'description' => $plan->description(),
            'enabled' => $plan->enabled() ? 1 : 0,
            'sequentialmode' => $plan->sequential_mode() ? 1 : 0,
            'enrolmentmode' => $plan->enrolment_mode(),
            'createdby' => $plan->created_by(),
            'timecreated' => $plan->time_created(),
            'timemodified' => $plan->time_modified(),
        ];
        $id = (int)$DB->insert_record(self::TABLE_PLAN, $record);
        return $this->find_by_id($id);
    }

    /**
     * @inheritDoc
     */
    public function update(learning_plan $plan): learning_plan {
        global $DB;
        $record = (object)[
            'id' => $plan->id(),
            'name' => $plan->name(),
            'description' => $plan->description(),
            'enabled' => $plan->enabled() ? 1 : 0,
            'sequentialmode' => $plan->sequential_mode() ? 1 : 0,
            'enrolmentmode' => $plan->enrolment_mode(),
            'createdby' => $plan->created_by(),
            'timecreated' => $plan->time_created(),
            'timemodified' => $plan->time_modified(),
        ];
        $DB->update_record(self::TABLE_PLAN, $record);
        return $this->find_by_id((int)$plan->id());
    }

    /**
     * @inheritDoc
     */
    public function find_by_id(int $planid): ?learning_plan {
        global $DB;
        $record = $DB->get_record(self::TABLE_PLAN, ['id' => $planid], '*', IGNORE_MISSING);
        if (!$record) {
            return null;
        }

        return $this->to_plan($record);
    }

    /**
     * @inheritDoc
     */
    public function list_all(): array {
        global $DB;
        $records = $DB->get_records(self::TABLE_PLAN, null, 'timemodified DESC');
        $plans = [];
        foreach ($records as $record) {
            $plans[] = $this->to_plan($record);
        }
        return $plans;
    }

    /**
     * @inheritDoc
     */
    public function delete(int $planid): void {
        global $DB;
        $DB->delete_records(self::TABLE_COURSE, ['planid' => $planid]);
        $DB->delete_records(self::TABLE_PLAN, ['id' => $planid]);
    }

    /**
     * @inheritDoc
     */
    public function set_course_stage(int $planid, int $courseid, string $stagename): void {
        global $DB;
        $record = $DB->get_record(self::TABLE_COURSE, ['planid' => $planid, 'courseid' => $courseid]);
        if (!$record) {
            throw new \moodle_exception('course:notinplan', 'local_learningplans');
        }
        $record->stagename = trim($stagename);
        $record->timemodified = time();
        $DB->update_record(self::TABLE_COURSE, $record);
    }

    /**
     * @inheritDoc
     */
    public function add_course(int $planid, int $courseid, string $stagename = ''): learning_plan_course {
        global $DB;

        if ($DB->record_exists(self::TABLE_COURSE, ['planid' => $planid, 'courseid' => $courseid])) {
            throw new \moodle_exception('course:duplicate', 'local_learningplans');
        }

        $max = $DB->get_field_sql(
            "SELECT MAX(sortorder) FROM {" . self::TABLE_COURSE . "} WHERE planid = :planid",
            ['planid' => $planid]
        );
        $sortorder = ((int)$max) + 1;
        $now = time();
        $record = (object)[
            'planid' => $planid,
            'courseid' => $courseid,
            'sortorder' => $sortorder,
            'required' => 1,
            'stagename' => trim($stagename),
            'timecreated' => $now,
            'timemodified' => $now,
        ];
        $id = (int)$DB->insert_record(self::TABLE_COURSE, $record);
        return new learning_plan_course($id, $planid, $courseid, $sortorder, true, trim($stagename));
    }

    /**
     * @inheritDoc
     */
    public function remove_course(int $planid, int $courseid): void {
        global $DB;
        $DB->delete_records(self::TABLE_COURSE, ['planid' => $planid, 'courseid' => $courseid]);
        $this->normalize_course_order($planid);
    }

    /**
     * @inheritDoc
     */
    public function move_course(int $planid, int $courseid, int $direction): void {
        $courses = $this->get_courses($planid);
        if (count($courses) <= 1) {
            return;
        }

        $index = null;
        foreach ($courses as $position => $course) {
            if ($course->course_id() === $courseid) {
                $index = $position;
                break;
            }
        }

        if ($index === null) {
            return;
        }

        $target = $direction < 0 ? $index - 1 : $index + 1;
        if ($target < 0 || $target >= count($courses)) {
            return;
        }

        $tmp = $courses[$index];
        $courses[$index] = $courses[$target];
        $courses[$target] = $tmp;

        $orderedids = array_map(static function(learning_plan_course $course): int {
            return $course->course_id();
        }, $courses);
        $this->reorder_courses($planid, $orderedids);
    }

    /**
     * @inheritDoc
     */
    public function reorder_courses(int $planid, array $orderedcourseids): void {
        global $DB;
        if ($orderedcourseids === []) {
            return;
        }

        $transaction = $DB->start_delegated_transaction();
        try {
            $sortorder = 1;
            foreach ($orderedcourseids as $courseid) {
                $DB->set_field(
                    self::TABLE_COURSE,
                    'sortorder',
                    $sortorder,
                    ['planid' => $planid, 'courseid' => $courseid]
                );
                $sortorder++;
            }
            $DB->set_field(self::TABLE_COURSE, 'timemodified', time(), ['planid' => $planid]);
            $transaction->allow_commit();
        } catch (\Throwable $exception) {
            $transaction->rollback($exception);
        }
    }

    /**
     * @inheritDoc
     */
    public function get_courses(int $planid): array {
        global $DB;
        $records = $DB->get_records(self::TABLE_COURSE, ['planid' => $planid], 'sortorder ASC, id ASC');
        $courses = [];
        foreach ($records as $record) {
            $courses[] = new learning_plan_course(
                (int)$record->id,
                (int)$record->planid,
                (int)$record->courseid,
                (int)$record->sortorder,
                !empty($record->required),
                (string)($record->stagename ?? '')
            );
        }
        return $courses;
    }

    /**
     * @inheritDoc
     */
    public function count_courses(int $planid): int {
        global $DB;
        return (int)$DB->count_records(self::TABLE_COURSE, ['planid' => $planid]);
    }

    /**
     * @inheritDoc
     */
    public function get_plan_ids_by_course(int $courseid): array {
        global $DB;
        return array_map('intval', $DB->get_fieldset_select(self::TABLE_COURSE, 'planid', 'courseid = :courseid', [
            'courseid' => $courseid,
        ]));
    }

    /**
     * Convert DB record to aggregate.
     *
     * @param \stdClass $record DB row.
     * @return learning_plan
     */
    private function to_plan(\stdClass $record): learning_plan {
        return new learning_plan(
            (int)$record->id,
            (string)$record->name,
            (string)($record->description ?? ''),
            !empty($record->enabled),
            !empty($record->sequentialmode),
            (string)$record->enrolmentmode,
            (int)($record->createdby ?? 0),
            (int)$record->timecreated,
            (int)$record->timemodified
        );
    }

    /**
     * Normalize sort order values.
     *
     * @param int $planid Plan id.
     * @return void
     */
    private function normalize_course_order(int $planid): void {
        $courses = $this->get_courses($planid);
        $orderedids = array_map(static function(learning_plan_course $course): int {
            return $course->course_id();
        }, $courses);
        $this->reorder_courses($planid, $orderedids);
    }
}

