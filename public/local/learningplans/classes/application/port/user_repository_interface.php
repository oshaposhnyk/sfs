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
 * User repository port.
 *
 * @package    local_learningplans
 * @copyright  2026
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
interface user_repository_interface {
    /**
     * Check whether user exists and is not deleted.
     *
     * @param int $userid User id.
     * @return bool
     */
    public function exists(int $userid): bool;

    /**
     * Get user record.
     *
     * @param int $userid User id.
     * @return \stdClass|null
     */
    public function find_by_id(int $userid): ?\stdClass;

    /**
     * List users by ids.
     *
     * @param array<int, int> $userids User ids.
     * @return array<int, \stdClass>
     */
    public function list_by_ids(array $userids): array;
}

