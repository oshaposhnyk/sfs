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
 * External: set the current user's active learning plan (Phase 6.3).
 *
 * @package    local_learningplans
 * @copyright  2026
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

declare(strict_types=1);

namespace local_learningplans\external;

use core_external\external_api;
use core_external\external_function_parameters;
use core_external\external_single_structure;
use core_external\external_value;
use local_learningplans\infrastructure\moodle\factory\learning_plan_service_factory;

/**
 * Own preference only: the plan must be one of the caller's memberships.
 */
final class set_active_plan extends external_api {

    /**
     * Parameters.
     *
     * @return external_function_parameters
     */
    public static function execute_parameters(): external_function_parameters {
        return new external_function_parameters([
            'planid' => new external_value(PARAM_INT, 'Plan to activate (own membership required)'),
        ]);
    }

    /**
     * Execute.
     *
     * @param int $planid Plan id.
     * @return array
     */
    public static function execute(int $planid): array {
        global $USER;

        ['planid' => $planid] = self::validate_parameters(self::execute_parameters(), ['planid' => $planid]);
        $context = \context_system::instance();
        self::validate_context($context);
        require_capability('local/learningplans:viewprogress', $context);

        learning_plan_service_factory::set_active_plan()->execute((int)$USER->id, $planid);

        return ['success' => true];
    }

    /**
     * Return description.
     *
     * @return external_single_structure
     */
    public static function execute_returns(): external_single_structure {
        return new external_single_structure([
            'success' => new external_value(PARAM_BOOL, 'Always true on success'),
        ]);
    }
}
