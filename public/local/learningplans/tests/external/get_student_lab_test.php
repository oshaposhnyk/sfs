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
 * External function tests (Phase 6.3).
 *
 * @package    local_learningplans
 * @copyright  2026
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

declare(strict_types=1);

namespace local_learningplans\external;

use core_external\external_api;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/webservice/tests/helpers.php');

/**
 * Own-data access, structure validity and denial paths.
 *
 * @covers \local_learningplans\external\get_student_lab
 * @covers \local_learningplans\external\set_active_plan
 */
final class get_student_lab_test extends \externallib_advanced_testcase {

    /**
     * Seed a plan with one course and one enrolled member.
     *
     * @return array{planid: int, user: \stdClass}
     */
    private function seed(): array {
        global $DB;
        $this->setAdminUser();
        $factory = \local_learningplans\infrastructure\moodle\factory\learning_plan_service_factory::create();

        // enrol_learningplan must be enabled for course enrolment.
        $enabled = array_keys(enrol_get_plugins(true));
        $enabled[] = 'learningplan';
        set_config('enrol_plugins_enabled', implode(',', array_unique($enabled)));
        \core\plugininfo\enrol::get_enabled_plugins();

        $course = $this->getDataGenerator()->create_course(['enablecompletion' => 1]);
        $user = $this->getDataGenerator()->create_user();
        $admin = get_admin();

        $plan = $factory->create_plan('Test Pathway', 'Desc', true, false, 'immediate', (int)$admin->id);
        $factory->add_course((int)$plan->id(), (int)$course->id, (int)$admin->id, 'Stage A');
        $factory->enrol_users((int)$plan->id(), [(int)$user->id], (int)$admin->id);

        // Learners need the system-level read capability (as db/access.php grants).
        $userrole = $DB->get_record('role', ['shortname' => 'user'], '*', MUST_EXIST);
        assign_capability('local/learningplans:viewprogress', CAP_ALLOW,
            $userrole->id, \context_system::instance()->id, true);

        return ['planid' => (int)$plan->id(), 'user' => $user];
    }

    public function test_get_student_lab_returns_own_overview(): void {
        $this->resetAfterTest();
        ['planid' => $planid, 'user' => $user] = $this->seed();

        $this->setUser($user);
        $result = external_api::clean_returnvalue(
            get_student_lab::execute_returns(),
            get_student_lab::execute()
        );

        $this->assertTrue($result['hasplans']);
        $this->assertCount(1, $result['plans']);
        $this->assertSame($planid, $result['plans'][0]['planid']);
        $this->assertSame('Stage A', $result['stages'][0]['name']);
        $this->assertSame('active', $result['courses'][0]['status']);
    }

    public function test_set_active_plan_rejects_foreign_plan(): void {
        $this->resetAfterTest();
        $this->seed();
        $stranger = $this->getDataGenerator()->create_user();

        $this->setUser($stranger);
        $this->expectException(\local_learningplans\domain\exception\domain_exception::class);
        set_active_plan::execute(999999);
    }

    public function test_set_active_plan_accepts_own_membership(): void {
        $this->resetAfterTest();
        ['planid' => $planid, 'user' => $user] = $this->seed();

        $this->setUser($user);
        $result = external_api::clean_returnvalue(
            set_active_plan::execute_returns(),
            set_active_plan::execute($planid)
        );
        $this->assertTrue($result['success']);
        $this->assertSame((string)$planid,
            get_user_preferences('local_learningplans_activeplan', '', $user));
    }

    public function test_guest_is_denied(): void {
        $this->resetAfterTest();
        $this->setGuestUser();
        $this->expectException(\required_capability_exception::class);
        get_student_lab::execute();
    }
}
