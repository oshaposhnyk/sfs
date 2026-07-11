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

use local_learningplans\application\port\learning_plan_progress_repository_interface;
use local_learningplans\domain\value\progress;

defined('MOODLE_INTERNAL') || die();

/**
 * Moodle DML + Cache progress repository.
 *
 * @package    local_learningplans
 * @copyright  2026
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class moodle_learning_plan_progress_repository implements learning_plan_progress_repository_interface {
    /** @var string */
    private const TABLE = 'local_learningplans_prog';

    /** @var string */
    private const CACHE_DEFINITION = 'planprogress';

    /**
     * @inheritDoc
     */
    public function get(int $planid, int $userid): ?progress {
        global $DB;
        $cache = $this->cache();
        $key = $this->cache_key($planid, $userid);
        $cached = $cache->get($key);
        if ($cached !== false && is_array($cached)) {
            return $this->from_array($cached);
        }

        $record = $DB->get_record(self::TABLE, ['planid' => $planid, 'userid' => $userid], '*', IGNORE_MISSING);
        if (!$record) {
            return null;
        }
        $progress = $this->from_record($record);
        $cache->set($key, $this->to_array($progress));
        return $progress;
    }

    /**
     * @inheritDoc
     */
    public function save(int $planid, int $userid, progress $progress, int $calculatedat): void {
        global $DB;

        $record = (object)[
            'planid' => $planid,
            'userid' => $userid,
            'totalcourses' => $progress->total_courses(),
            'completedcourses' => $progress->completed_courses(),
            'inprogresscourses' => $progress->inprogress_courses(),
            'notstartedcourses' => $progress->notstarted_courses(),
            'progresspercent' => $progress->percentage(),
            'nextcourseid' => $progress->next_course_id(),
            'calculatedat' => $calculatedat,
        ];

        $existing = $DB->get_record(self::TABLE, ['planid' => $planid, 'userid' => $userid], 'id', IGNORE_MISSING);
        if ($existing) {
            $record->id = $existing->id;
            $DB->update_record(self::TABLE, $record);
        } else {
            $DB->insert_record(self::TABLE, $record);
        }

        $this->cache()->set($this->cache_key($planid, $userid), $this->to_array($progress));
    }

    /**
     * @inheritDoc
     */
    public function invalidate(int $planid, int $userid): void {
        $this->cache()->delete($this->cache_key($planid, $userid));
    }

    /**
     * @inheritDoc
     */
    public function invalidate_plan(int $planid): void {
        global $DB;
        $userids = $DB->get_fieldset_select(self::TABLE, 'userid', 'planid = :planid', ['planid' => $planid]);
        $cache = $this->cache();
        foreach ($userids as $userid) {
            $cache->delete($this->cache_key($planid, (int)$userid));
        }
    }

    /**
     * @inheritDoc
     */
    public function delete_by_plan(int $planid): void {
        global $DB;
        $this->invalidate_plan($planid);
        $DB->delete_records(self::TABLE, ['planid' => $planid]);
    }

    /**
     * @inheritDoc
     */
    public function find_stale(int $olderthan, int $limit = 1000): array {
        global $DB;
        return array_values($DB->get_records_select(self::TABLE, 'calculatedat < :olderthan', [
            'olderthan' => $olderthan,
        ], 'calculatedat ASC', '*', 0, $limit));
    }

    /**
     * Build cache key.
     *
     * @param int $planid Plan id.
     * @param int $userid User id.
     * @return string
     */
    private function cache_key(int $planid, int $userid): string {
        return $planid . ':' . $userid;
    }

    /**
     * Convert progress to scalar array.
     *
     * @param progress $progress Progress object.
     * @return array<string, float|int|null>
     */
    private function to_array(progress $progress): array {
        return [
            'totalcourses' => $progress->total_courses(),
            'completedcourses' => $progress->completed_courses(),
            'inprogresscourses' => $progress->inprogress_courses(),
            'notstartedcourses' => $progress->notstarted_courses(),
            'progresspercent' => $progress->percentage(),
            'nextcourseid' => $progress->next_course_id(),
        ];
    }

    /**
     * Restore progress from scalar array.
     *
     * @param array<string, mixed> $data Scalar data.
     * @return progress
     */
    private function from_array(array $data): progress {
        return new progress(
            (int)$data['totalcourses'],
            (int)$data['completedcourses'],
            (int)$data['inprogresscourses'],
            (int)$data['notstartedcourses'],
            (float)$data['progresspercent'],
            $data['nextcourseid'] !== null ? (int)$data['nextcourseid'] : null
        );
    }

    /**
     * Restore progress from DB row.
     *
     * @param \stdClass $record DB row.
     * @return progress
     */
    private function from_record(\stdClass $record): progress {
        return new progress(
            (int)$record->totalcourses,
            (int)$record->completedcourses,
            (int)$record->inprogresscourses,
            (int)$record->notstartedcourses,
            (float)$record->progresspercent,
            isset($record->nextcourseid) ? (int)$record->nextcourseid : null
        );
    }

    /**
     * Cache factory.
     *
     * @return \cache_application
     */
    private function cache(): \cache_application {
        return \cache::make('local_learningplans', self::CACHE_DEFINITION);
    }
}

