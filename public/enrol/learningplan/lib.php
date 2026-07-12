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
 * Learning plan enrolment plugin.
 *
 * @package    enrol_learningplan
 * @copyright  2026
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Enrolment plugin used by local_learningplans.
 */
class enrol_learningplan_plugin extends enrol_plugin {
    /**
     * Roles can be managed later.
     *
     * @return bool
     */
    public function roles_protected() {
        return false;
    }

    /**
     * Manual add via course UI is disabled.
     *
     * @param int $courseid Course id.
     * @return bool
     */
    public function can_add_instance($courseid) {
        return false;
    }

    /**
     * Users with capability may enrol users through this method.
     *
     * @param stdClass $instance Enrol instance.
     * @return bool
     */
    public function allow_enrol(stdClass $instance) {
        return true;
    }

    /**
     * Users with capability may unenrol users through this method.
     *
     * @param stdClass $instance Enrol instance.
     * @return bool
     */
    public function allow_unenrol(stdClass $instance) {
        return true;
    }

    /**
     * Users with capability may manage user enrolments.
     *
     * @param stdClass $instance Enrol instance.
     * @return bool
     */
    public function allow_manage(stdClass $instance) {
        return true;
    }

    /**
     * Is instance removable in UI.
     *
     * @param stdClass $instance Enrol instance.
     * @return bool
     */
    public function can_delete_instance($instance) {
        $context = context_course::instance($instance->courseid);
        return has_capability('enrol/learningplan:config', $context);
    }

    /**
     * Is instance hidden/shown in UI.
     *
     * @param stdClass $instance Enrol instance.
     * @return bool
     */
    public function can_hide_show_instance($instance) {
        $context = context_course::instance($instance->courseid);
        return has_capability('enrol/learningplan:config', $context);
    }

    /**
     * Use standard instance editing UI for diagnostics.
     *
     * @return bool
     */
    public function use_standard_editing_ui() {
        return true;
    }

    /**
     * Add new plugin instance.
     *
     * customint1 stores local_learningplans plan id.
     *
     * @param stdClass $course Course.
     * @param array|null $fields Fields.
     * @return int|null
     */
    public function add_instance($course, ?array $fields = null) {
        global $DB;

        $fields = $fields ?? [];
        $planid = (int)($fields['customint1'] ?? 0);
        if ($planid <= 0) {
            return null;
        }

        if ($DB->record_exists('enrol', [
            'enrol' => 'learningplan',
            'courseid' => $course->id,
            'customint1' => $planid,
        ])) {
            return null;
        }

        return parent::add_instance($course, $fields);
    }

    /**
     * Localised name of enrol instance.
     *
     * @param stdClass|null $instance Instance.
     * @return string
     */
    public function get_instance_name($instance) {
        global $DB;

        $defaultname = get_string('pluginname', 'enrol_learningplan');
        if (empty($instance)) {
            return $defaultname;
        }

        if (!empty($instance->name)) {
            return format_string($instance->name);
        }

        $planid = (int)($instance->customint1 ?? 0);
        if ($planid <= 0) {
            return $defaultname;
        }

        $name = $DB->get_field('local_learningplans_plan', 'name', ['id' => $planid], IGNORE_MISSING);
        if ($name === false) {
            return $defaultname . ' (' . $planid . ')';
        }

        return $defaultname . ' - ' . format_string((string)$name);
    }
}

