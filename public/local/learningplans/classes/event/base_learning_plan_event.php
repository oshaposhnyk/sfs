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
 * Base event class for learning plan events.
 *
 * @package    local_learningplans
 * @copyright  2026
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class base_learning_plan_event extends \core\event\base {
    /**
     * @inheritDoc
     */
    protected function init() {
        $this->data['objecttable'] = 'local_learningplans_plan';
        $this->data['edulevel'] = self::LEVEL_OTHER;
    }

    /**
     * @inheritDoc
     */
    public function get_url() {
        return new \moodle_url('/local/learningplans/view.php', ['id' => $this->objectid]);
    }

    /**
     * @inheritDoc
     */
    protected function validate_data() {
        parent::validate_data();
        if (empty($this->objectid)) {
            throw new \coding_exception('The objectid must be set to learning plan id.');
        }
    }

    /**
     * @inheritDoc
     */
    public static function get_objectid_mapping() {
        return ['db' => 'local_learningplans_plan', 'restore' => \core\event\base::NOT_MAPPED];
    }

    /**
     * @inheritDoc
     */
    public static function get_other_mapping() {
        return false;
    }
}

