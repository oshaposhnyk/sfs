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
 * Set active learning plan use case (ADR-008).
 *
 * @package    local_learningplans
 * @copyright  2026
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

declare(strict_types=1);

namespace local_learningplans\application\usecase;

use local_learningplans\application\port\user_preference_repository_interface;
use local_learningplans\application\service\learning_plan_service;
use local_learningplans\domain\exception\domain_exception;

defined('MOODLE_INTERNAL') || die();

/**
 * Stores which of the user's own plans is the active one.
 */
final class set_active_learning_plan {
    /** @var learning_plan_service */
    private learning_plan_service $service;

    /** @var user_preference_repository_interface */
    private user_preference_repository_interface $preferences;

    /**
     * @param learning_plan_service $service Application service.
     * @param user_preference_repository_interface $preferences Preference repository.
     */
    public function __construct(
        learning_plan_service $service,
        user_preference_repository_interface $preferences
    ) {
        $this->service = $service;
        $this->preferences = $preferences;
    }

    /**
     * Execute use case.
     *
     * @param int $userid Acting user — may only change their own preference.
     * @param int $planid Plan the user wants active.
     * @return void
     */
    public function execute(int $userid, int $planid): void {
        foreach ($this->service->get_user_memberships($userid) as $membership) {
            if ($membership->plan_id() === $planid) {
                $this->preferences->set_active_plan_id($userid, $planid);
                return;
            }
        }
        throw new domain_exception('error:membershipnotfound');
    }
}
