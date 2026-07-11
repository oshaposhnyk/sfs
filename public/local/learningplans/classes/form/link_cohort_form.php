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
 * Form to link a cohort to a learning plan.
 *
 * @package    local_learningplans
 * @copyright  2026
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class link_cohort_form extends \moodleform {
    /**
     * @inheritDoc
     */
    public function definition() {
        $mform = $this->_form;
        $planid = (int)($this->_customdata['planid'] ?? 0);
        $cohorts = (array)($this->_customdata['cohorts'] ?? []);

        $mform->addElement('hidden', 'id', $planid);
        $mform->setType('id', PARAM_INT);

        $options = ['' => get_string('choosedots')] + $cohorts;
        $mform->addElement(
            'autocomplete',
            'cohortid',
            get_string('cohort:add:cohort', 'local_learningplans'),
            $options
        );
        $mform->setType('cohortid', PARAM_INT);
        $mform->addRule('cohortid', null, 'required', null, 'client');

        $this->add_action_buttons(false, get_string('cohort:add:submit', 'local_learningplans'));
    }
}