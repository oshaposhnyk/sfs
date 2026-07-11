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

use core_user;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/formslib.php');

/**
 * Enrol users into learning plan form.
 *
 * @package    local_learningplans
 * @copyright  2026
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class enrol_users_form extends \moodleform {
    /**
     * @inheritDoc
     */
    public function definition() {
        $mform = $this->_form;
        $planid = (int)($this->_customdata['planid'] ?? 0);

        $mform->addElement('hidden', 'planid', $planid);
        $mform->setType('planid', PARAM_INT);

        $useroptions = [
            'ajax' => 'core_user/form_user_selector',
            'multiple' => true,
            'noselectionstring' => get_string('choosedots'),
            'valuehtmlcallback' => [self::class, 'user_selector_value_html'],
        ];
        $mform->addElement('autocomplete', 'selectedusers', get_string('enrol:selectusers', 'local_learningplans'), [], $useroptions);
        $mform->addHelpButton('selectedusers', 'enrol:selectusers', 'local_learningplans');

        $this->add_action_buttons(false, get_string('enrol:submit', 'local_learningplans'));
    }

    /**
     * Render selected autocomplete user values.
     *
     * @param int|string $userid Moodle user id.
     * @return string
     */
    public static function user_selector_value_html($userid): string {
        global $OUTPUT;

        $context = \context_system::instance();
        $fields = \core_user\fields::for_name()->with_identity($context, false);
        $record = core_user::get_user((int)$userid, 'id ' . $fields->get_sql()->selects, IGNORE_MISSING);
        if (!$record) {
            return s(get_string('deleteduser'));
        }

        $user = (object)[
            'id' => $record->id,
            'fullname' => fullname($record, has_capability('moodle/site:viewfullnames', $context)),
            'extrafields' => [],
        ];

        foreach ($fields->get_required_fields([\core_user\fields::PURPOSE_IDENTITY]) as $extrafield) {
            $value = trim((string)($record->{$extrafield} ?? ''));
            if ($value === '') {
                continue;
            }

            $user->extrafields[] = (object)[
                'name' => $extrafield,
                'value' => s($value),
            ];
        }

        return $OUTPUT->render_from_template('core_user/form_user_selector_suggestion', $user);
    }
}
