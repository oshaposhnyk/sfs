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

namespace local_learningplans\event;

defined('MOODLE_INTERNAL') || die();

/**
 * Event: cohort unlinked from a learning plan.
 */
final class cohort_unlinked_from_learning_plan extends base_learning_plan_event {
    /**
     * @inheritDoc
     */
    protected function init() {
        parent::init();
        $this->data['crud'] = 'd';
    }

    /**
     * @inheritDoc
     */
    protected function validate_data() {
        parent::validate_data();
        if (!isset($this->other['cohortid'])) {
            throw new \coding_exception('The cohortid must be set in other.');
        }
    }

    /**
     * @inheritDoc
     */
    public static function get_name() {
        return get_string('event:cohortunlinked', 'local_learningplans');
    }

    /**
     * @inheritDoc
     */
    public function get_description() {
        $cohortid = $this->other['cohortid'];
        return "The user with id '{$this->userid}' unlinked cohort '{$cohortid}' from learning plan '{$this->objectid}'.";
    }
}