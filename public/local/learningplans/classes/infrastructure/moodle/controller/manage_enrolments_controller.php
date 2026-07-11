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

use local_learningplans\form\enrol_users_form;
use local_learningplans\infrastructure\moodle\factory\learning_plan_service_factory;
use local_learningplans\infrastructure\persistence\moodle_course_repository;
use local_learningplans\infrastructure\persistence\moodle_user_repository;

defined('MOODLE_INTERNAL') || die();

/**
 * Controller: manage plan enrolments.
 *
 * @package    local_learningplans
 * @copyright  2026
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class manage_enrolments_controller {
    /**
     * Execute page.
     *
     * @return void
     */
    public function execute(): void {
        global $PAGE, $OUTPUT, $USER;

        $service = learning_plan_service_factory::create();
        $userrepository = new moodle_user_repository();
        $courserepository = new moodle_course_repository();

        $planid = required_param('id', PARAM_INT);
        $plan = $service->get_plan($planid);
        $url = new \moodle_url('/local/learningplans/enrol.php', ['id' => $planid]);
        $context = \context_system::instance();

        $action = optional_param('action', '', PARAM_ALPHA);
        $userid = optional_param('userid', 0, PARAM_INT);
        if ($action === 'unenrol' && $userid > 0) {
            require_sesskey();
            try {
                $service->unenrol_user($planid, $userid, (int)$USER->id);
                \core\notification::success(get_string('enrol:removed', 'local_learningplans'));
            } catch (\Throwable $exception) {
                $messagekey = (string)$exception->getMessage();
                if (get_string_manager()->string_exists($messagekey, 'local_learningplans')) {
                    \core\notification::error(get_string($messagekey, 'local_learningplans'));
                } else {
                    \core\notification::error(s($messagekey));
                }
            }
            redirect($url);
        }

        $form = new enrol_users_form($url, ['planid' => $planid]);
        if ($form->is_cancelled()) {
            redirect(new \moodle_url('/local/learningplans/view.php', ['id' => $planid]));
        }

        if ($data = $form->get_data()) {
            $userids = [];
            if (!empty($data->selectedusers)) {
                foreach ((array)$data->selectedusers as $selecteduserid) {
                    $userid = (int)$selecteduserid;
                    if ($userid > 0) {
                        $userids[] = $userid;
                    }
                }
            }
            $userids = array_values(array_unique($userids));
            if ($userids === []) {
                \core\notification::error(get_string('error:invaliduserids', 'local_learningplans'));
            } else {
                try {
                    $result = $service->enrol_users($planid, $userids, (int)$USER->id);
                    if (!empty($result['succeeded'])) {
                        \core\notification::success(get_string('enrol:success', 'local_learningplans'));
                    }
                    if (!empty($result['failed'])) {
                        \core\notification::warning(get_string('enrol:failed', 'local_learningplans'));
                    }
                } catch (\Throwable $exception) {
                    $messagekey = (string)$exception->getMessage();
                    if (get_string_manager()->string_exists($messagekey, 'local_learningplans')) {
                        \core\notification::error(get_string($messagekey, 'local_learningplans'));
                    } else {
                        \core\notification::error(s($messagekey));
                    }
                }
                redirect($url);
            }
        }

        $courses = $service->get_plan_courses($planid);
        $coursenames = [];
        foreach ($courserepository->list_by_ids(array_map(static function($courseitem) {
            return $courseitem->course_id();
        }, $courses)) as $courseid => $record) {
            $coursenames[(int)$courseid] = format_string($record->fullname);
        }

        $memberships = $service->get_plan_memberships($planid, true);
        $users = $userrepository->list_by_ids(array_map(static function($membership) {
            return $membership->user_id();
        }, $memberships));

        $rows = [];
        foreach ($memberships as $membership) {
            $progress = $service->get_user_progress($planid, $membership->user_id());
            $nextcoursename = '';
            if ($progress->next_course_id() !== null && isset($coursenames[$progress->next_course_id()])) {
                $nextcoursename = $coursenames[$progress->next_course_id()];
            }

            $user = $users[$membership->user_id()] ?? null;
            $rows[] = [
                'userid' => $membership->user_id(),
                'fullname' => $user ? fullname($user) : ('#' . $membership->user_id()),
                'progresslabel' => get_string('progress:label', 'local_learningplans', $progress->percentage()),
                'nextcourse' => $nextcoursename,
                'unenrolurl' => (new \moodle_url('/local/learningplans/enrol.php', [
                    'id' => $planid,
                    'action' => 'unenrol',
                    'userid' => $membership->user_id(),
                    'sesskey' => sesskey(),
                ]))->out(false),
                'unenrollabel' => get_string('enrol:unenrol', 'local_learningplans'),
            ];
        }

        $PAGE->set_context($context);
        $PAGE->set_url($url);
        $PAGE->set_pagelayout('standard');
        $PAGE->set_title(get_string('enrol:title', 'local_learningplans'));
        $PAGE->set_heading(format_string($plan->name()));

        echo $OUTPUT->header();
        echo $OUTPUT->heading(get_string('enrol:title', 'local_learningplans'));
        $form->display();

        echo $OUTPUT->render_from_template('local_learningplans/enrolments_table', [
            'hasrows' => !empty($rows),
            'rows' => $rows,
            'empty' => get_string('plan:view:nomembers', 'local_learningplans'),
            'labeluser' => get_string('plan:view:user', 'local_learningplans'),
            'labelprogress' => get_string('plan:view:progress', 'local_learningplans'),
            'labelnextcourse' => get_string('plan:view:nextcourse', 'local_learningplans'),
            'labelactions' => get_string('plan:view:actions', 'local_learningplans'),
        ]);
        echo $OUTPUT->footer();
    }
}
