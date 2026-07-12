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
 * Tests for the learning plan enrolment plugin.
 *
 * @package    enrol_learningplan
 * @copyright  2026 SecureFood School
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

declare(strict_types=1);

namespace enrol_learningplan;

/**
 * Enrol/unenrol round-trip and instance behaviour.
 *
 * @covers \enrol_learningplan_plugin
 */
final class lib_test extends \advanced_testcase {

    /**
     * Enable the plugin and return it.
     *
     * @return \enrol_plugin
     */
    private function enabled_plugin(): \enrol_plugin {
        $enabled = enrol_get_plugins(true);
        if (!isset($enabled['learningplan'])) {
            $list = array_keys($enabled);
            $list[] = 'learningplan';
            set_config('enrol_plugins_enabled', implode(',', $list));
            \core\plugininfo\enrol::get_enabled_plugins();
        }
        $plugin = enrol_get_plugin('learningplan');
        $this->assertNotNull($plugin, 'enrol_learningplan must be installed');
        return $plugin;
    }

    public function test_instance_created_with_plan_id(): void {
        global $DB;
        $this->resetAfterTest();
        $plugin = $this->enabled_plugin();
        $course = $this->getDataGenerator()->create_course();

        $instanceid = $plugin->add_instance($course, ['customint1' => 42]);
        $this->assertNotEmpty($instanceid);

        $instance = $DB->get_record('enrol', ['id' => $instanceid], '*', MUST_EXIST);
        $this->assertSame('learningplan', $instance->enrol);
        $this->assertEquals(42, $instance->customint1);
        $this->assertEquals($course->id, $instance->courseid);
    }

    public function test_enrol_unenrol_round_trip(): void {
        global $DB;
        $this->resetAfterTest();
        $plugin = $this->enabled_plugin();
        $course = $this->getDataGenerator()->create_course();
        $user = $this->getDataGenerator()->create_user();
        $studentrole = $DB->get_record('role', ['shortname' => 'student'], '*', MUST_EXIST);

        $instanceid = $plugin->add_instance($course, ['customint1' => 7]);
        $instance = $DB->get_record('enrol', ['id' => $instanceid], '*', MUST_EXIST);

        $plugin->enrol_user($instance, (int)$user->id, (int)$studentrole->id, 0, 0, ENROL_USER_ACTIVE);
        $this->assertTrue(is_enrolled(\context_course::instance($course->id), $user));

        // Idempotent re-enrol: still exactly one user enrolment.
        $plugin->enrol_user($instance, (int)$user->id, (int)$studentrole->id, 0, 0, ENROL_USER_ACTIVE);
        $this->assertEquals(1, $DB->count_records('user_enrolments', [
            'enrolid' => $instance->id, 'userid' => $user->id,
        ]));

        $plugin->unenrol_user($instance, (int)$user->id);
        $this->assertFalse(is_enrolled(\context_course::instance($course->id), $user));
        $this->assertEquals(0, $DB->count_records('user_enrolments', [
            'enrolid' => $instance->id, 'userid' => $user->id,
        ]));
    }

    public function test_manual_instance_creation_via_ui_is_disallowed(): void {
        $this->resetAfterTest();
        $plugin = $this->enabled_plugin();
        $course = $this->getDataGenerator()->create_course();
        $this->assertFalse($plugin->can_add_instance($course->id));
    }
}
