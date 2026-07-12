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

namespace local_learningplans\form;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/formslib.php');

/**
 * Add course to plan form.
 *
 * @package    local_learningplans
 * @copyright  2026
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class add_course_form extends \moodleform {
    /**
     * @inheritDoc
     */
    public function definition() {
        $mform = $this->_form;
        $courses = (array)($this->_customdata['courses'] ?? []);
        $planid = (int)($this->_customdata['planid'] ?? 0);

        $mform->addElement('hidden', 'planid', $planid);
        $mform->setType('planid', PARAM_INT);

        $mform->addElement('select', 'courseid', get_string('course:add:courseid', 'local_learningplans'), $courses);
        $mform->setType('courseid', PARAM_INT);
        $mform->addRule('courseid', null, 'required', null, 'client');

        $mform->addElement('text', 'stagename', get_string('course:stagename', 'local_learningplans'));
        $mform->setType('stagename', PARAM_TEXT);
        $mform->addHelpButton('stagename', 'course:stagename', 'local_learningplans');

        $this->add_action_buttons(false, get_string('course:add:submit', 'local_learningplans'));
    }
}

