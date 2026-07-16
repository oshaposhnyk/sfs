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
 * Mission completion reader for Future Food cards.
 *
 * @package    local_sfsgame
 * @copyright  2026 SecureFood School
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

declare(strict_types=1);

namespace local_sfsgame;

defined('MOODLE_INTERNAL') || die();

/**
 * Reads completion state for a mission linked to a Moodle activity.
 *
 * This class intentionally supports only standard `/mod/.../view.php?id=CMID`
 * Moodle activity URLs. External videos remain plain resources; the plugin
 * does not fake watch tracking for providers it cannot verify.
 */
final class mission_completion {
    /** @var string URL is external or not a Moodle activity. */
    public const STATE_UNTRACKED = 'untracked';

    /** @var string Moodle activity exists but completion is not enabled. */
    public const STATE_NOT_COMPLETABLE = 'notcompletable';

    /** @var string Moodle activity completion is enabled but incomplete. */
    public const STATE_INCOMPLETE = 'incomplete';

    /** @var string Moodle activity completion is complete/pass. */
    public const STATE_COMPLETED = 'completed';

    /**
     * Completion state for a mission URL.
     *
     * @param string $url Mission URL.
     * @param int $userid Viewing user id.
     * @return array State context.
     */
    public static function state_for_url(string $url, int $userid): array {
        global $CFG;

        require_once($CFG->libdir . '/completionlib.php');

        $cmid = self::cmid_from_url($url);
        if ($cmid === null) {
            return self::state(self::STATE_UNTRACKED);
        }

        $record = get_coursemodule_from_id('', $cmid, 0, false, IGNORE_MISSING);
        if (!$record) {
            return self::state(self::STATE_UNTRACKED);
        }

        try {
            $modinfo = get_fast_modinfo((int)$record->course, $userid);
            $cm = $modinfo->get_cm($cmid);
        } catch (\Throwable $exception) {
            return self::state(self::STATE_UNTRACKED, $cmid);
        }

        if (!$cm->uservisible) {
            return self::state(self::STATE_UNTRACKED, $cmid);
        }

        $completion = new \completion_info((object)['id' => (int)$record->course]);
        if ($completion->is_enabled($cm) == COMPLETION_TRACKING_NONE) {
            return self::state(self::STATE_NOT_COMPLETABLE, $cmid);
        }

        $data = $completion->get_data($cm, true, $userid);
        $state = (int)$data->completionstate;
        $completed = $state === COMPLETION_COMPLETE || $state === COMPLETION_COMPLETE_PASS;

        return self::state($completed ? self::STATE_COMPLETED : self::STATE_INCOMPLETE, $cmid);
    }

    /**
     * Whether the learner can actually reach a mission target (P6).
     *
     * External links are always "reachable" (they open elsewhere). For a
     * local activity the course-module must be uservisible; for a course link
     * the learner must be able to access the course. Anything we cannot
     * resolve defaults to reachable so a mission is never hidden by mistake.
     *
     * @param string $url Raw configured URL.
     * @param int $userid User id.
     * @return bool
     */
    public static function is_accessible(string $url, int $userid): bool {
        $url = trim($url);
        if ($url === '') {
            return false;
        }

        // External URL: reachable (opens in a new tab).
        try {
            $moodleurl = new \moodle_url($url);
        } catch (\Throwable $exception) {
            return false;
        }
        if (preg_match('~^https?://~i', $url) === 1 && !$moodleurl->is_local_url()) {
            return true;
        }

        // Local activity link: the course-module must be visible to the user.
        $cmid = self::cmid_from_url($url);
        if ($cmid !== null) {
            $record = get_coursemodule_from_id('', $cmid, 0, false, IGNORE_MISSING);
            if (!$record) {
                return false;
            }
            try {
                $modinfo = get_fast_modinfo((int)$record->course, $userid);
                return $modinfo->get_cm($cmid)->uservisible;
            } catch (\Throwable $exception) {
                return false;
            }
        }

        // Local course link: the learner must be able to access the course.
        if ($moodleurl->get_path(false) === '/course/view.php') {
            $courseid = (int)$moodleurl->get_param('id');
            if ($courseid > 0) {
                $course = get_course($courseid, false);
                return $course && can_access_course($course, \core_user::get_user($userid) ?: null);
            }
        }

        // Any other internal route: don't second-guess it.
        return true;
    }

    /**
     * Extract a course-module id from a Moodle activity URL.
     *
     * @param string $url Raw configured URL.
     * @return int|null Course-module id, or null when not a local activity URL.
     */
    private static function cmid_from_url(string $url): ?int {
        $url = trim($url);
        if ($url === '') {
            return null;
        }

        try {
            $moodleurl = new \moodle_url($url);
        } catch (\Throwable $exception) {
            return null;
        }

        if (preg_match('~^https?://~i', $url) === 1 && !$moodleurl->is_local_url()) {
            return null;
        }

        if (preg_match('~^/mod/[a-z][a-z0-9_]*/view\.php$~', $moodleurl->get_path(false)) !== 1) {
            return null;
        }

        $id = $moodleurl->get_param('id');
        if (is_array($id) || (int)$id <= 0) {
            return null;
        }

        return (int)$id;
    }

    /**
     * Normalised state response.
     *
     * @param string $state State constant.
     * @param int|null $cmid Course-module id.
     * @return array
     */
    private static function state(string $state, ?int $cmid = null): array {
        return [
            'state' => $state,
            'cmid' => $cmid,
            'completed' => $state === self::STATE_COMPLETED,
            'tracked' => in_array($state, [self::STATE_COMPLETED, self::STATE_INCOMPLETE], true),
            'completable' => in_array(
                $state,
                [self::STATE_COMPLETED, self::STATE_INCOMPLETE, self::STATE_NOT_COMPLETABLE],
                true
            ),
        ];
    }
}
