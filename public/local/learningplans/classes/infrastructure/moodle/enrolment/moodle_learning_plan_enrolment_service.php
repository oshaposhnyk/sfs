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

namespace local_learningplans\infrastructure\moodle\enrolment;

use local_learningplans\application\port\learning_plan_enrolment_service_interface;

defined('MOODLE_INTERNAL') || die();

/**
 * Moodle enrolment API adapter for learning plans.
 *
 * @package    local_learningplans
 * @copyright  2026
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class moodle_learning_plan_enrolment_service implements learning_plan_enrolment_service_interface {
    /**
     * @inheritDoc
     */
    public function enrol_user(int $planid, int $courseid, int $userid, int $roleid): array {
        global $DB;

        $plugin = enrol_get_plugin('learningplan');
        if (!$plugin) {
            throw new \coding_exception('enrol_learningplan plugin must be installed and enabled.');
        }

        $instance = $this->get_or_create_instance($planid, $courseid, $roleid);
        $plugin->enrol_user($instance, $userid, $roleid, 0, 0, ENROL_USER_ACTIVE);

        $userenrolment = $DB->get_record('user_enrolments', [
            'enrolid' => $instance->id,
            'userid' => $userid,
        ], '*', IGNORE_MISSING);

        return [
            'enrolid' => (int)$instance->id,
            'userenrolmentid' => $userenrolment ? (int)$userenrolment->id : null,
        ];
    }

    /**
     * @inheritDoc
     */
    public function unenrol_user(int $planid, int $courseid, int $userid): void {
        $plugin = enrol_get_plugin('learningplan');
        if (!$plugin) {
            return;
        }

        $instance = $this->find_instance($planid, $courseid);
        if (!$instance) {
            return;
        }

        $plugin->unenrol_user($instance, $userid);
    }

    /**
     * Find or create course enrol instance.
     *
     * @param int $planid Plan id.
     * @param int $courseid Course id.
     * @param int $roleid Role id.
     * @return \stdClass
     */
    private function get_or_create_instance(int $planid, int $courseid, int $roleid): \stdClass {
        global $DB;

        $existing = $this->find_instance($planid, $courseid);
        if ($existing) {
            return $existing;
        }

        $course = get_course($courseid);
        $plugin = enrol_get_plugin('learningplan');
        $instanceid = $plugin->add_instance($course, [
            'status' => ENROL_INSTANCE_ENABLED,
            'roleid' => $roleid,
            'customint1' => $planid,
        ]);
        if (!$instanceid) {
            throw new \coding_exception('Could not create enrol_learningplan instance.');
        }

        return $DB->get_record('enrol', ['id' => $instanceid], '*', MUST_EXIST);
    }

    /**
     * Find existing enrol instance for plan and course.
     *
     * @param int $planid Plan id.
     * @param int $courseid Course id.
     * @return \stdClass|null
     */
    private function find_instance(int $planid, int $courseid): ?\stdClass {
        global $DB;
        $instance = $DB->get_record('enrol', [
            'enrol' => 'learningplan',
            'courseid' => $courseid,
            'customint1' => $planid,
        ], '*', IGNORE_MISSING);
        return $instance ?: null;
    }
}

