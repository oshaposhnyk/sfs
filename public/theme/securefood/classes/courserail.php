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
 * Course right-rail context builder (Phase 8 audit C1/C2).
 *
 * @package    theme_securefood
 * @copyright  2026 SecureFood School
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

declare(strict_types=1);

namespace theme_securefood;

defined('MOODLE_INTERNAL') || die();

/**
 * Shapes the course-page right rail (spec: course.html) from real course data.
 *
 * Everything is derived from the viewer's own completion state via core
 * APIs; nothing here is stored by the theme.
 */
class courserail {

    /** @var int Cap on the teacher list, matching the prototype card. */
    private const MAX_TEACHERS = 4;

    /**
     * Build the rail template context plus per-section progress data.
     *
     * @param \stdClass $course The course record.
     * @param int $userid The viewing user.
     * @return array ['rail' => array, 'sections' => array] — 'sections' feeds
     *               the sectionprogress AMD module (C2) and may be empty when
     *               completion is disabled.
     */
    public static function context(\stdClass $course, int $userid): array {
        $modinfo = get_fast_modinfo($course, $userid);
        $completion = new \completion_info($course);
        $completionenabled = $completion->is_enabled() != COMPLETION_DISABLED;

        $nextup = null;
        $sections = [];
        $activitycount = 0;
        $sectioncount = 0;

        foreach ($modinfo->get_section_info_all() as $section) {
            if (!$section->uservisible) {
                continue;
            }
            if ($section->section > 0) {
                $sectioncount++;
            }
            $done = 0;
            $total = 0;
            foreach ($modinfo->sections[$section->section] ?? [] as $cmid) {
                $cm = $modinfo->cms[$cmid];
                if (!$cm->uservisible || $cm->deletioninprogress) {
                    continue;
                }
                $activitycount++;
                if (!$completionenabled
                        || $completion->is_enabled($cm) == COMPLETION_TRACKING_NONE) {
                    continue;
                }
                $total++;
                $state = (int)$completion->get_data($cm, true, $userid)->completionstate;
                if ($state === COMPLETION_COMPLETE || $state === COMPLETION_COMPLETE_PASS) {
                    $done++;
                } else if ($nextup === null && $cm->url !== null) {
                    $nextup = [
                        'title' => format_string($cm->name),
                        'meta' => $cm->modfullname . ' · ' . get_section_name($course, $section),
                        'url' => $cm->url->out(false),
                    ];
                }
            }
            if ($total > 0) {
                $sections[] = [
                    'number' => $section->section,
                    'frac' => $done . ' / ' . $total,
                    'label' => $done === $total
                        ? get_string('rail_complete', 'theme_securefood')
                        : ($done > 0
                            ? get_string('rail_inprogress', 'theme_securefood')
                            : get_string('rail_upnext', 'theme_securefood')),
                ];
            }
        }

        $percentage = null;
        if ($completionenabled) {
            $percentage = \core_completion\progress::get_course_progress_percentage($course, $userid);
        }

        $info = [
            ['label' => get_string('rail_sections', 'theme_securefood'), 'value' => (string)$sectioncount],
            ['label' => get_string('rail_activities', 'theme_securefood'), 'value' => (string)$activitycount],
        ];
        if ($percentage !== null) {
            $info[] = [
                'label' => get_string('rail_progress', 'theme_securefood'),
                'value' => (int)round($percentage) . '%',
            ];
        }
        $languages = get_string_manager()->get_list_of_translations();
        $courselang = $course->lang !== '' ? $course->lang : current_language();
        if (isset($languages[$courselang])) {
            $info[] = [
                'label' => get_string('rail_language', 'theme_securefood'),
                'value' => $languages[$courselang],
            ];
        }
        $info[] = [
            'label' => get_string('rail_format', 'theme_securefood'),
            'value' => get_string('pluginname', 'format_' . $course->format),
        ];

        return [
            'rail' => [
                'nextup' => $nextup,
                'info' => $info,
                'teachers' => self::teachers($course),
                'hasteachers' => self::teachers($course) !== [],
            ],
            'sections' => $sections,
        ];
    }

    /**
     * Course teachers (editing teachers first), capped for the rail card.
     *
     * @param \stdClass $course The course record.
     * @return array[] fullname/initials/subtitle rows.
     */
    private static function teachers(\stdClass $course): array {
        static $cache = [];
        if (isset($cache[$course->id])) {
            return $cache[$course->id];
        }

        $context = \context_course::instance($course->id);
        $roles = get_archetype_roles('editingteacher') + get_archetype_roles('teacher');
        if ($roles === []) {
            return $cache[$course->id] = [];
        }

        $users = get_role_users(
            array_keys($roles),
            $context,
            false,
            'u.id, u.firstname, u.lastname, u.firstnamephonetic, u.lastnamephonetic, u.middlename, u.alternatename',
            'u.lastname ASC, u.firstname ASC'
        );

        $teachers = [];
        foreach ($users as $user) {
            if (isset($teachers[$user->id]) || count($teachers) >= self::MAX_TEACHERS) {
                continue;
            }
            $teachers[$user->id] = [
                'fullname' => fullname($user),
                'initials' => mb_strtoupper(
                    mb_substr($user->firstname, 0, 1) . mb_substr($user->lastname, 0, 1)
                ),
                'subtitle' => get_string('rail_teacher', 'theme_securefood'),
            ];
        }
        return $cache[$course->id] = array_values($teachers);
    }
}
