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
use local_learningplans\domain\entity\learning_plan_stage;

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

    /** @var string Stage table. */
    private const TABLE_STAGE = 'local_learningplans_stg';

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
        $DB->delete_records(self::TABLE_STAGE, ['planid' => $planid]);
        $DB->delete_records(self::TABLE_PLAN, ['id' => $planid]);
    }

    /**
     * @inheritDoc
     */
    public function set_course_stage(int $planid, int $courseid, string $stagename): void {
        global $DB;
        $moved = $DB->get_record(self::TABLE_COURSE, ['planid' => $planid, 'courseid' => $courseid]);
        if (!$moved) {
            throw new \moodle_exception('course:notinplan', 'local_learningplans');
        }
        $stageid = $this->resolve_stage_id($planid, $stagename);

        // Re-sequence: everything else keeps its order, the moved course
        // lands at the end of its new block.
        $items = [];
        foreach ($this->get_course_records($planid) as $record) {
            if ((int)$record->id !== (int)$moved->id) {
                $items[] = ['id' => (int)$record->id, 'stageid' => (int)$record->stageid];
            }
        }
        $items = $this->insert_at_block_end($items, (int)$moved->id, $stageid);
        $this->apply_structure($planid, $items);
    }

    /**
     * @inheritDoc
     */
    public function get_stages(int $planid): array {
        global $DB;
        $stages = [];
        foreach ($DB->get_records(self::TABLE_STAGE, ['planid' => $planid], 'sortorder ASC, id ASC') as $record) {
            $stages[] = new learning_plan_stage(
                (int)$record->id,
                (int)$record->planid,
                (string)$record->name,
                (int)$record->sortorder
            );
        }
        return $stages;
    }

    /**
     * @inheritDoc
     */
    public function rename_stage(int $planid, int $stageid, string $name): void {
        global $DB;
        $name = trim($name);
        if ($name === '') {
            throw new \moodle_exception('error:emptystagename', 'local_learningplans');
        }
        $stage = $DB->get_record(self::TABLE_STAGE, ['id' => $stageid, 'planid' => $planid]);
        if (!$stage) {
            throw new \moodle_exception('error:stagenotfound', 'local_learningplans');
        }
        // Names are unique within a plan (find-or-create groups by name).
        $clash = $DB->record_exists_select(
            self::TABLE_STAGE,
            'planid = :planid AND ' . $DB->sql_equal('name', ':name') . ' AND id <> :id',
            ['planid' => $planid, 'name' => $name, 'id' => $stageid]
        );
        if ($clash) {
            throw new \moodle_exception('error:stagenameexists', 'local_learningplans');
        }
        $DB->update_record(self::TABLE_STAGE, (object)[
            'id' => $stageid,
            'name' => $name,
            'timemodified' => time(),
        ]);
    }

    /**
     * @inheritDoc
     */
    public function restructure_courses(int $planid, array $orderedcourseids, array $stageids): void {
        global $DB;
        if (count($orderedcourseids) !== count($stageids)) {
            throw new \moodle_exception('invalidparameter', 'debug');
        }

        $records = [];
        foreach ($this->get_course_records($planid) as $record) {
            $records[(int)$record->courseid] = $record;
        }
        $validstages = $DB->get_records_menu(self::TABLE_STAGE, ['planid' => $planid], '', 'id, id AS stageid');

        $items = [];
        $seen = [];
        foreach (array_values($orderedcourseids) as $index => $courseid) {
            $courseid = (int)$courseid;
            if (!isset($records[$courseid]) || isset($seen[$courseid])) {
                throw new \moodle_exception('course:notinplan', 'local_learningplans');
            }
            $seen[$courseid] = true;
            $stageid = (int)(array_values($stageids)[$index] ?? 0);
            if ($stageid !== 0 && !isset($validstages[$stageid])) {
                throw new \moodle_exception('invalidparameter', 'debug');
            }
            $items[] = ['id' => (int)$records[$courseid]->id, 'stageid' => $stageid];
        }
        if (count($items) !== count($records)) {
            throw new \moodle_exception('invalidparameter', 'debug');
        }
        $this->apply_structure($planid, $items);
    }

    /**
     * @inheritDoc
     */
    public function add_course(int $planid, int $courseid, string $stagename = ''): learning_plan_course {
        global $DB;

        if ($DB->record_exists(self::TABLE_COURSE, ['planid' => $planid, 'courseid' => $courseid])) {
            throw new \moodle_exception('course:duplicate', 'local_learningplans');
        }

        $stageid = $this->resolve_stage_id($planid, $stagename);
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
            'stageid' => $stageid > 0 ? $stageid : null,
            'timecreated' => $now,
            'timemodified' => $now,
        ];
        $id = (int)$DB->insert_record(self::TABLE_COURSE, $record);

        // Pull the new course to the end of its stage block.
        $items = [];
        foreach ($this->get_course_records($planid) as $existing) {
            if ((int)$existing->id !== $id) {
                $items[] = ['id' => (int)$existing->id, 'stageid' => (int)$existing->stageid];
            }
        }
        $items = $this->insert_at_block_end($items, $id, $stageid);
        $this->apply_structure($planid, $items);

        $saved = $DB->get_record(self::TABLE_COURSE, ['id' => $id], '*', MUST_EXIST);
        return new learning_plan_course(
            $id,
            $planid,
            $courseid,
            (int)$saved->sortorder,
            true,
            $stageid > 0 ? $stageid : null,
            trim($stagename)
        );
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

        // After the swap the moved course sits at $target and the neighbour
        // at $index. Crossing a stage boundary means entering the
        // neighbour's block, so the moved course adopts its stage.
        $neighbourstage = $courses[$index]->stage_id() ?? 0;
        $items = [];
        foreach ($courses as $position => $course) {
            $stageid = $course->stage_id() ?? 0;
            if ($position === $target && $neighbourstage !== $stageid) {
                $stageid = $neighbourstage;
            }
            $items[] = ['id' => $course->id(), 'stageid' => $stageid];
        }
        $this->apply_structure($planid, $items);
    }

    /**
     * @inheritDoc
     */
    public function reorder_courses(int $planid, array $orderedcourseids): void {
        if ($orderedcourseids === []) {
            return;
        }
        $records = [];
        foreach ($this->get_course_records($planid) as $record) {
            $records[(int)$record->courseid] = $record;
        }
        $items = [];
        foreach ($orderedcourseids as $courseid) {
            if (isset($records[(int)$courseid])) {
                $items[] = ['id' => (int)$records[(int)$courseid]->id, 'stageid' => (int)$records[(int)$courseid]->stageid];
            }
        }
        if ($items === []) {
            return;
        }
        $this->apply_structure($planid, $items);
    }

    /**
     * @inheritDoc
     */
    public function get_courses(int $planid): array {
        global $DB;
        $sql = "SELECT c.*, s.name AS stagename
                  FROM {" . self::TABLE_COURSE . "} c
             LEFT JOIN {" . self::TABLE_STAGE . "} s ON s.id = c.stageid
                 WHERE c.planid = :planid
              ORDER BY c.sortorder ASC, c.id ASC";
        $courses = [];
        foreach ($DB->get_records_sql($sql, ['planid' => $planid]) as $record) {
            $courses[] = new learning_plan_course(
                (int)$record->id,
                (int)$record->planid,
                (int)$record->courseid,
                (int)$record->sortorder,
                !empty($record->required),
                $record->stageid !== null ? (int)$record->stageid : null,
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
        $items = [];
        foreach ($this->get_course_records($planid) as $record) {
            $items[] = ['id' => (int)$record->id, 'stageid' => (int)$record->stageid];
        }
        $this->apply_structure($planid, $items);
    }

    /**
     * Raw course records of a plan in sequence order.
     *
     * @param int $planid Plan id.
     * @return \stdClass[]
     */
    private function get_course_records(int $planid): array {
        global $DB;
        return array_values($DB->get_records(self::TABLE_COURSE, ['planid' => $planid], 'sortorder ASC, id ASC'));
    }

    /**
     * Find a stage by name within a plan, creating it when missing.
     *
     * @param int $planid Plan id.
     * @param string $stagename Stage name; '' resolves to 0 (unstaged).
     * @return int Stage id, or 0 for unstaged.
     */
    private function resolve_stage_id(int $planid, string $stagename): int {
        global $DB;
        $stagename = trim($stagename);
        if ($stagename === '') {
            return 0;
        }
        $existing = $DB->get_record(self::TABLE_STAGE, ['planid' => $planid, 'name' => $stagename]);
        if ($existing) {
            return (int)$existing->id;
        }
        $max = $DB->get_field_sql(
            "SELECT MAX(sortorder) FROM {" . self::TABLE_STAGE . "} WHERE planid = :planid",
            ['planid' => $planid]
        );
        $now = time();
        return (int)$DB->insert_record(self::TABLE_STAGE, (object)[
            'planid' => $planid,
            'name' => $stagename,
            'sortorder' => ((int)$max) + 1,
            'timecreated' => $now,
            'timemodified' => $now,
        ]);
    }

    /**
     * Insert a course record at the end of its stage block.
     *
     * @param array<int, array{id: int, stageid: int}> $items Sequence without the course.
     * @param int $recordid Course record id to insert.
     * @param int $stageid Target stage (0 = unstaged).
     * @return array<int, array{id: int, stageid: int}>
     */
    private function insert_at_block_end(array $items, int $recordid, int $stageid): array {
        $insertafter = -1;
        foreach ($items as $position => $item) {
            if ($item['stageid'] === $stageid) {
                $insertafter = $position;
            }
        }
        $entry = ['id' => $recordid, 'stageid' => $stageid];
        if ($insertafter === -1) {
            $items[] = $entry;
            return $items;
        }
        array_splice($items, $insertafter + 1, 0, [$entry]);
        return $items;
    }

    /**
     * Persist a course sequence while enforcing the aggregate invariants:
     * stage blocks stay contiguous (grouped by first appearance), stage
     * sortorder mirrors block order, and stages without courses are removed.
     *
     * @param int $planid Plan id.
     * @param array<int, array{id: int, stageid: int}> $items Desired sequence.
     * @return void
     */
    private function apply_structure(int $planid, array $items): void {
        global $DB;

        // Group into contiguous blocks by first appearance of each stage
        // (0 = the unstaged block).
        $blocks = [];
        foreach ($items as $item) {
            $blocks[$item['stageid']][] = $item;
        }

        $transaction = $DB->start_delegated_transaction();
        try {
            $now = time();
            $sortorder = 0;
            $stagesort = 0;
            $usedstageids = [];
            foreach ($blocks as $stageid => $blockitems) {
                if ($stageid > 0) {
                    $stagesort++;
                    $usedstageids[] = (int)$stageid;
                    $DB->set_field(self::TABLE_STAGE, 'sortorder', $stagesort, ['id' => $stageid]);
                }
                foreach ($blockitems as $item) {
                    $sortorder++;
                    $DB->update_record(self::TABLE_COURSE, (object)[
                        'id' => $item['id'],
                        'sortorder' => $sortorder,
                        'stageid' => $item['stageid'] > 0 ? $item['stageid'] : null,
                        'timemodified' => $now,
                    ]);
                }
            }

            // Garbage-collect stages that lost their last course.
            if ($usedstageids === []) {
                $DB->delete_records(self::TABLE_STAGE, ['planid' => $planid]);
            } else {
                [$notinsql, $params] = $DB->get_in_or_equal($usedstageids, SQL_PARAMS_NAMED, 'sid', false);
                $params['planid'] = $planid;
                $DB->delete_records_select(self::TABLE_STAGE, "planid = :planid AND id $notinsql", $params);
            }
            $transaction->allow_commit();
        } catch (\Throwable $exception) {
            $transaction->rollback($exception);
        }
    }
}

