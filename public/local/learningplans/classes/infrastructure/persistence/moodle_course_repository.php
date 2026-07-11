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

use local_learningplans\application\port\course_repository_interface;

defined('MOODLE_INTERNAL') || die();

/**
 * Moodle DML course repository adapter.
 *
 * @package    local_learningplans
 * @copyright  2026
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class moodle_course_repository implements course_repository_interface {
    /**
     * @inheritDoc
     */
    public function find_by_id(int $courseid): ?\stdClass {
        global $DB;
        $course = $DB->get_record('course', ['id' => $courseid], '*', IGNORE_MISSING);
        return $course ?: null;
    }

    /**
     * @inheritDoc
     */
    public function list_by_ids(array $courseids): array {
        global $DB;
        if ($courseids === []) {
            return [];
        }
        [$insql, $params] = $DB->get_in_or_equal($courseids, SQL_PARAMS_NAMED);
        $records = $DB->get_records_select('course', "id $insql", $params);
        return array_map(static function($record) {
            return $record;
        }, $records);
    }

    /**
     * @inheritDoc
     */
    public function list_for_selector(): array {
        global $DB;
        return array_values($DB->get_records_select('course', 'id <> :siteid', ['siteid' => SITEID], 'fullname ASC', 'id, fullname'));
    }

    /**
     * @inheritDoc
     */
    public function is_user_enrolled(int $courseid, int $userid): bool {
        $context = \context_course::instance($courseid);
        return is_enrolled($context, $userid, '', true);
    }
}

