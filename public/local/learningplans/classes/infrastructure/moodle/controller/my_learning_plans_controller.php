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
use local_learningplans\infrastructure\persistence\moodle_course_repository;

defined('MOODLE_INTERNAL') || die();

/**
 * Controller: current user's learning plans.
 *
 * @package    local_learningplans
 * @copyright  2026
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class my_learning_plans_controller {
    /**
     * Execute page.
     *
     * @return void
     */
    public function execute(): void {
        global $PAGE, $OUTPUT, $USER;

        $service = learning_plan_service_factory::create();
        $courserepository = new moodle_course_repository();
        $context = \context_system::instance();
        $url = new \moodle_url('/local/learningplans/my.php');

        $rows = [];
        foreach ($service->get_user_memberships((int)$USER->id) as $membership) {
            $plan = $service->get_plan($membership->plan_id());
            $progress = $service->get_user_progress($membership->plan_id(), (int)$USER->id);

            $nextcourse = '';
            $nextcourseid = $progress->next_course_id();
            if ($nextcourseid !== null) {
                $course = $courserepository->find_by_id($nextcourseid);
                if ($course) {
                    $nextcourse = format_string($course->fullname);
                }
            }

            $rows[] = [
                'planname' => format_string($plan->name()),
                'progresslabel' => get_string('progress:label', 'local_learningplans', $progress->percentage()),
                'nextcourse' => $nextcourse,
                'viewurl' => (new \moodle_url('/local/learningplans/view.php', ['id' => $membership->plan_id()]))->out(false),
                'viewlabel' => get_string('list:view', 'local_learningplans'),
            ];
        }

        $PAGE->set_context($context);
        $PAGE->set_url($url);
        $PAGE->set_pagelayout('standard');
        $PAGE->set_title(get_string('list:mytitle', 'local_learningplans'));
        $PAGE->set_heading(get_string('list:mytitle', 'local_learningplans'));

        echo $OUTPUT->header();
        echo $OUTPUT->render_from_template('local_learningplans/my_page', [
            'title' => get_string('list:mytitle', 'local_learningplans'),
            'hasrows' => !empty($rows),
            'rows' => $rows,
            'empty' => get_string('my:empty', 'local_learningplans'),
            'labelprogress' => get_string('plan:view:progress', 'local_learningplans'),
            'labelnextcourse' => get_string('plan:view:nextcourse', 'local_learningplans'),
        ]);
        echo $OUTPUT->footer();
    }
}

