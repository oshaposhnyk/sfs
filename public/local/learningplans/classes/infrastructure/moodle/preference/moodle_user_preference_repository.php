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
 * Moodle user-preference adapter.
 *
 * @package    local_learningplans
 * @copyright  2026
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

declare(strict_types=1);

namespace local_learningplans\infrastructure\moodle\preference;

use local_learningplans\application\port\user_preference_repository_interface;

defined('MOODLE_INTERNAL') || die();

/**
 * Persists the active-plan choice as a Moodle user preference.
 */
final class moodle_user_preference_repository implements user_preference_repository_interface {
    /** @var string Preference name (declared in the privacy provider). */
    private const ACTIVE_PLAN = 'local_learningplans_activeplan';

    /**
     * @inheritDoc
     */
    public function get_active_plan_id(int $userid): ?int {
        $value = (int)get_user_preferences(self::ACTIVE_PLAN, 0, $userid);
        return $value > 0 ? $value : null;
    }

    /**
     * @inheritDoc
     */
    public function set_active_plan_id(int $userid, int $planid): void {
        set_user_preference(self::ACTIVE_PLAN, (string)$planid, $userid);
    }
}
