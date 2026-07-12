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

/**
 * Port: per-user plugin preferences.
 *
 * @package    local_learningplans
 * @copyright  2026
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

declare(strict_types=1);

namespace local_learningplans\application\port;

/**
 * Stores which learning plan a user treats as their active one.
 */
interface user_preference_repository_interface {
    /**
     * The user's chosen active plan id, if any.
     *
     * @param int $userid User id.
     * @return int|null Plan id or null when never chosen.
     */
    public function get_active_plan_id(int $userid): ?int;

    /**
     * Persist the user's active plan choice.
     *
     * @param int $userid User id.
     * @param int $planid Plan id.
     * @return void
     */
    public function set_active_plan_id(int $userid, int $planid): void;
}
