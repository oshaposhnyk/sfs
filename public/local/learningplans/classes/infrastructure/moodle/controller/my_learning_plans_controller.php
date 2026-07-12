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

namespace local_learningplans\infrastructure\moodle\controller;

use local_learningplans\infrastructure\moodle\factory\learning_plan_service_factory;

defined('MOODLE_INTERNAL') || die();

/**
 * Controller: Student Lab — the learner's learning-plan dashboard (ADR-008).
 *
 * Thin controller: params → use cases → template context. Plan switching is a
 * sesskey-guarded post/redirect/get action so the page works without JS.
 *
 * @package    local_learningplans
 * @copyright  2026
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class my_learning_plans_controller {
    /** @var string[] Cover variant modifier cycle for course tiles. */
    private const COVER_VARIANTS = ['teal', 'aqua', 'amber', 'green', 'deep'];

    /**
     * Execute page.
     *
     * @return void
     */
    public function execute(): void {
        global $PAGE, $OUTPUT, $USER;

        require_login(null, false);
        if (isguestuser()) {
            throw new \moodle_exception('noguest');
        }

        $url = new \moodle_url('/local/learningplans/my.php');
        $userid = (int)$USER->id;

        // Plan switch action (PRG, no-JS friendly).
        $setplan = optional_param('setplan', 0, PARAM_INT);
        if ($setplan > 0) {
            require_sesskey();
            learning_plan_service_factory::set_active_plan()->execute($userid, $setplan);
            redirect($url);
        }

        $overview = learning_plan_service_factory::student_lab_overview()->execute($userid);

        $context = \context_system::instance();
        $PAGE->set_context($context);
        $PAGE->set_url($url);
        $PAGE->set_pagelayout('standard');
        $PAGE->set_title(get_string('studentlab:title', 'local_learningplans'));
        $PAGE->set_heading(get_string('studentlab:title', 'local_learningplans'));

        echo $OUTPUT->header();
        echo $OUTPUT->render_from_template(
            'local_learningplans/studentlab_page',
            $this->template_context($overview, $url)
        );
        echo $OUTPUT->footer();
    }

    /**
     * Shape the overview read model into template context.
     *
     * @param array $overview Read model from get_student_lab_overview.
     * @param \moodle_url $pageurl This page's URL.
     * @return array
     */
    private function template_context(array $overview, \moodle_url $pageurl): array {
        if (!$overview['hasplans']) {
            return [
                'hasplans' => false,
                'title' => get_string('studentlab:title', 'local_learningplans'),
                'lede' => get_string('studentlab:lede', 'local_learningplans'),
                'empty' => get_string('my:empty', 'local_learningplans'),
            ];
        }

        $plans = [];
        foreach ($overview['plans'] as $plan) {
            $plans[] = [
                'planid' => $plan['planid'],
                'name' => format_string($plan['name']),
                'active' => $plan['active'],
                'switchurl' => (new \moodle_url($pageurl, [
                    'setplan' => $plan['planid'],
                    'sesskey' => sesskey(),
                ]))->out(false),
                'meta' => get_string('studentlab:planmeta', 'local_learningplans', [
                    'completed' => $plan['completed'],
                    'total' => $plan['total'],
                ]),
            ];
        }

        $continueurl = null;
        $continueindex = $overview['continueindex'];

        $courses = [];
        foreach ($overview['courses'] as $index => $course) {
            $status = $course['status'];
            $locked = $status === 'locked';
            $courseurl = $locked
                ? null
                : (new \moodle_url('/course/view.php', ['id' => $course['courseid']]))->out(false);
            if ($continueindex !== null && $index === $continueindex) {
                $continueurl = $courseurl;
            }

            $percentage = (int)round($course['percentage']);
            $courses[] = [
                'fullname' => format_string($course['fullname']),
                'summary' => shorten_text(
                    html_to_text(format_text($course['summary'], FORMAT_HTML, ['filter' => false]), 0, false),
                    140
                ),
                'positionlabel' => get_string('studentlab:coursenumber', 'local_learningplans',
                    sprintf('%02d', $course['position'])),
                'courseurl' => $courseurl,
                'locked' => $locked,
                'status' => $status,
                'statuslabel' => get_string('studentlab:status:' . $status, 'local_learningplans'),
                'statetext' => $locked
                    ? get_string('studentlab:lockedhint', 'local_learningplans')
                    : get_string('studentlab:percentcomplete', 'local_learningplans', $percentage),
                'actionlabel' => $course['action'] === 'none'
                    ? null
                    : get_string('studentlab:action:' . $course['action'], 'local_learningplans'),
                'percentage' => $percentage,
                'cover' => self::COVER_VARIANTS[$index % count(self::COVER_VARIANTS)],
            ];
        }

        $stages = [];
        foreach ($overview['stages'] ?? [] as $i => $stage) {
            $stagecourses = [];
            foreach ($stage['indexes'] as $index) {
                if (isset($courses[$index])) {
                    $stagecourses[] = $courses[$index];
                }
            }
            $stages[] = [
                'number' => $i + 1,
                'name' => $stage['name'] !== ''
                    ? format_string($stage['name'])
                    : get_string('studentlab:stagedefault', 'local_learningplans', $i + 1),
                'status' => $stage['status'],
                'statuslabel' => get_string('studentlab:status:' . $stage['status'], 'local_learningplans'),
                'meta' => get_string('studentlab:planmeta', 'local_learningplans', [
                    'completed' => $stage['completed'],
                    'total' => $stage['total'],
                ]),
                'done' => $stage['status'] === 'done',
                'locked' => $stage['status'] === 'locked',
                'courses' => $stagecourses,
            ];
        }

        $progress = $overview['progress'];
        return [
            'hasplans' => true,
            'title' => get_string('studentlab:title', 'local_learningplans'),
            'lede' => get_string('studentlab:lede', 'local_learningplans'),
            'activeplanname' => format_string($overview['activeplan']['name']),
            'plans' => $plans,
            'hasmultipleplans' => count($plans) > 1,
            'completedcount' => $progress['completed'],
            'totalcount' => $progress['total'],
            'percentage' => $progress['percentage'],
            'continueurl' => $continueurl,
            'stages' => $stages,
            'hascourses' => $courses !== [],
            'nocourses' => get_string('studentlab:nocourses', 'local_learningplans'),
        ];
    }
}
