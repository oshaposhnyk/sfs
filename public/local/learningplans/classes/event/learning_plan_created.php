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
 * Event: learning plan created.
 */
final class learning_plan_created extends base_learning_plan_event {
    /**
     * @inheritDoc
     */
    protected function init() {
        parent::init();
        $this->data['crud'] = 'c';
    }

    /**
     * @inheritDoc
     */
    public static function get_name() {
        return get_string('event:learningplancreated', 'local_learningplans');
    }

    /**
     * @inheritDoc
     */
    public function get_description() {
        return "The user with id '{$this->userid}' created learning plan '{$this->objectid}'.";
    }
}

