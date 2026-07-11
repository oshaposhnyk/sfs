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

namespace local_learningplans\infrastructure\moodle\completion;

use local_learningplans\application\port\completion_reader_interface;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/completionlib.php');

/**
 * Completion API adapter.
 *
 * @package    local_learningplans
 * @copyright  2026
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class moodle_completion_reader implements completion_reader_interface {
    /**
     * @inheritDoc
     */
    public function is_course_complete(int $courseid, int $userid): bool {
        $completion = new \completion_info((object)['id' => $courseid]);
        return (bool)$completion->is_course_complete($userid);
    }

    /**
     * @inheritDoc
     */
    public function get_course_progress_percentage(int $courseid, int $userid): ?float {
        $progress = \core_completion\progress::get_course_progress_percentage((object)['id' => $courseid], $userid);
        if ($progress === null) {
            return null;
        }
        return (float)$progress;
    }
}
