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
 * Learning plan create/edit form.
 *
 * @package    local_learningplans
 * @copyright  2026
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class learning_plan_form extends \moodleform {
    /**
     * @inheritDoc
     */
    public function definition() {
        $mform = $this->_form;

        $mform->addElement('hidden', 'id');
        $mform->setType('id', PARAM_INT);

        $mform->addElement('text', 'name', get_string('plan:name', 'local_learningplans'), ['size' => 60]);
        $mform->setType('name', PARAM_TEXT);
        $mform->addRule('name', null, 'required', null, 'client');

        $editoroptions = [
            'maxfiles' => 0,
            'maxbytes' => 0,
            'subdirs' => false,
            'noclean' => false,
            'context' => \context_system::instance(),
        ];
        $mform->addElement(
            'editor',
            'description_editor',
            get_string('plan:description', 'local_learningplans'),
            ['rows' => 8],
            $editoroptions
        );
        $mform->setType('description_editor', PARAM_RAW);

        $mform->addElement('advcheckbox', 'enabled', get_string('plan:enabled', 'local_learningplans'));
        $mform->setDefault('enabled', 1);

        $mform->addElement('advcheckbox', 'sequentialmode', get_string('plan:sequentialmode', 'local_learningplans'));
        $mform->setDefault('sequentialmode', 0);

        $mform->addElement('select', 'enrolmentmode', get_string('plan:enrolmentmode', 'local_learningplans'), [
            'immediate' => get_string('plan:enrolmentmode:immediate', 'local_learningplans'),
            'sequentialrelease' => get_string('plan:enrolmentmode:sequentialrelease', 'local_learningplans'),
        ]);
        $mform->setDefault('enrolmentmode', 'immediate');

        $this->add_action_buttons(true, get_string('plan:save', 'local_learningplans'));
    }
}
