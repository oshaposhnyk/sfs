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

use local_learningplans\application\port\user_repository_interface;

defined('MOODLE_INTERNAL') || die();

/**
 * Moodle DML user repository adapter.
 *
 * @package    local_learningplans
 * @copyright  2026
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class moodle_user_repository implements user_repository_interface {
    /**
     * @inheritDoc
     */
    public function exists(int $userid): bool {
        global $DB;
        return $DB->record_exists('user', ['id' => $userid, 'deleted' => 0]);
    }

    /**
     * @inheritDoc
     */
    public function find_by_id(int $userid): ?\stdClass {
        global $DB;
        $user = $DB->get_record('user', ['id' => $userid, 'deleted' => 0], '*', IGNORE_MISSING);
        return $user ?: null;
    }

    /**
     * @inheritDoc
     */
    public function list_by_ids(array $userids): array {
        global $DB;
        if ($userids === []) {
            return [];
        }
        [$insql, $params] = $DB->get_in_or_equal($userids, SQL_PARAMS_NAMED);
        $records = $DB->get_records_select('user', "id $insql AND deleted = 0", $params);
        return array_map(static function($record) {
            return $record;
        }, $records);
    }
}

