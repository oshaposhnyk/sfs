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

use local_learningplans\form\add_course_form;
use local_learningplans\infrastructure\moodle\factory\learning_plan_service_factory;
use local_learningplans\infrastructure\persistence\moodle_course_repository;
use local_learningplans\infrastructure\persistence\moodle_user_repository;

defined('MOODLE_INTERNAL') || die();

/**
 * Controller: learning plan detail page.
 *
 * @package    local_learningplans
 * @copyright  2026
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class view_learning_plan_controller {
    /**
     * Execute page.
     *
     * @return void
     */
    public function execute(): void {
        global $PAGE, $OUTPUT, $USER;

        $service = learning_plan_service_factory::create();
        $courserepository = new moodle_course_repository();
        $userrepository = new moodle_user_repository();

        $planid = required_param('id', PARAM_INT);
        $plan = $service->get_plan($planid);
        $context = \context_system::instance();
        $url = new \moodle_url('/local/learningplans/view.php', ['id' => $planid]);
        $PAGE->set_context($context);
        $PAGE->set_url($url);
        $PAGE->set_pagelayout('standard');
        $PAGE->set_title(format_string($plan->name()));
        $PAGE->set_heading(format_string($plan->name()));

        $action = optional_param('action', '', PARAM_ALPHA);
        if ($action === 'reorder') {
            require_sesskey();
            $orderedraw = trim((string)optional_param('orderedcourseids', '', PARAM_SEQUENCE));
            $stageraw = trim((string)optional_param('stageids', '', PARAM_SEQUENCE));
            $orderedcourseids = [];
            if ($orderedraw !== '') {
                foreach (explode(',', $orderedraw) as $value) {
                    $courseid = (int)$value;
                    if ($courseid > 0) {
                        $orderedcourseids[] = $courseid;
                    }
                }
            }
            $stageids = $stageraw !== '' ? array_map('intval', explode(',', $stageraw)) : [];

            try {
                if ($stageids !== [] && count($stageids) === count($orderedcourseids)) {
                    // Drag-and-drop between stage blocks: order + stage move.
                    $service->restructure_courses($planid, $orderedcourseids, $stageids, (int)$USER->id);
                } else {
                    $service->reorder_courses($planid, $orderedcourseids, (int)$USER->id);
                }
                \core\notification::success(get_string('course:reordered', 'local_learningplans'));
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

        $courseid = optional_param('courseid', 0, PARAM_INT);
        if ($action !== '' && $courseid > 0) {
            require_sesskey();
            try {
                if ($action === 'remove') {
                    $service->remove_course($planid, $courseid, (int)$USER->id);
                    \core\notification::success(get_string('course:removed', 'local_learningplans'));
                } else if ($action === 'moveup') {
                    $service->move_course($planid, $courseid, -1, (int)$USER->id);
                    \core\notification::success(get_string('course:reordered', 'local_learningplans'));
                } else if ($action === 'movedown') {
                    $service->move_course($planid, $courseid, 1, (int)$USER->id);
                    \core\notification::success(get_string('course:reordered', 'local_learningplans'));
                } else if ($action === 'setstage') {
                    $stagename = trim((string)optional_param('stagename', '', PARAM_TEXT));
                    $service->set_course_stage($planid, $courseid, $stagename, (int)$USER->id);
                    \core\notification::success(get_string('course:stageupdated', 'local_learningplans'));
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

        $courses = $service->get_plan_courses($planid);
        $allcourses = $courserepository->list_for_selector();
        $courseoptions = [];
        $existingcourseids = [];
        foreach ($courses as $courseitem) {
            $existingcourseids[$courseitem->course_id()] = true;
        }
        foreach ($allcourses as $course) {
            if (!isset($existingcourseids[(int)$course->id])) {
                $courseoptions[(int)$course->id] = format_string($course->fullname);
            }
        }

        $addcourseform = new add_course_form($url, [
            'planid' => $planid,
            'courses' => $courseoptions,
        ]);
        if ($addcourseform->is_cancelled()) {
            redirect($url);
        }
        if ($data = $addcourseform->get_data()) {
            try {
                $service->add_course($planid, (int)$data->courseid, (int)$USER->id, trim((string)($data->stagename ?? '')));
                \core\notification::success(get_string('course:added', 'local_learningplans'));
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

        $courseids = array_map(static function($courseitem) {
            return $courseitem->course_id();
        }, $courses);
        $courserecords = $courserepository->list_by_ids($courseids);

        $hasstages = $service->get_plan_stages($planid) !== [];

        $courserows = [];
        foreach ($courses as $index => $courseitem) {
            $cid = $courseitem->course_id();
            $coursename = isset($courserecords[$cid]) ? format_string($courserecords[$cid]->fullname) : ('#' . $cid);
            $courserows[] = [
                'courseid' => $cid,
                'name' => $coursename,
                'order' => $index + 1,
                'stageid' => $courseitem->stage_id() ?? 0,
                'stagename' => $courseitem->stage_name(),
                'draghandle' => $OUTPUT->render_from_template('core/drag_handle', [
                    'movetitle' => get_string('movecontent', 'moodle', $coursename),
                    'extraclasses' => 'learning-plan__drag-handle',
                ]),
                'moveupurl' => (new \moodle_url('/local/learningplans/view.php', [
                    'id' => $planid,
                    'action' => 'moveup',
                    'courseid' => $cid,
                    'sesskey' => sesskey(),
                ]))->out(false),
                'moveupicon' => $OUTPUT->pix_icon('t/up', get_string('plan:view:moveup', 'local_learningplans'), 'core'),
                'movedownurl' => (new \moodle_url('/local/learningplans/view.php', [
                    'id' => $planid,
                    'action' => 'movedown',
                    'courseid' => $cid,
                    'sesskey' => sesskey(),
                ]))->out(false),
                'movedownicon' => $OUTPUT->pix_icon('t/down', get_string('plan:view:movedown', 'local_learningplans'), 'core'),
                'removeurl' => (new \moodle_url('/local/learningplans/view.php', [
                    'id' => $planid,
                    'action' => 'remove',
                    'courseid' => $cid,
                    'sesskey' => sesskey(),
                ]))->out(false),
            ];
        }

        // Contiguous stage blocks (the repository invariant guarantees each
        // stage appears exactly once in the sequence).
        $stagegroups = [];
        foreach ($courserows as $row) {
            $last = count($stagegroups) - 1;
            if ($last < 0 || $stagegroups[$last]['stageid'] !== $row['stageid']) {
                $stagegroups[] = [
                    'stageid' => $row['stageid'],
                    'isstage' => $row['stageid'] > 0,
                    'heading' => $row['stageid'] > 0
                        ? $row['stagename']
                        : get_string('plan:view:nostage', 'local_learningplans'),
                    'courses' => [],
                ];
                $last++;
            }
            $stagegroups[$last]['courses'][] = $row;
        }
        $hasunstaged = false;
        foreach ($stagegroups as $index => $group) {
            $stagegroups[$index]['countlabel'] = get_string(
                'plan:view:stagecount', 'local_learningplans', count($group['courses'])
            );
            $hasunstaged = $hasunstaged || !$group['isstage'];
        }
        // With stages present, an (empty) unstaged block stays available as
        // a drop target for pulling courses out of stages.
        if ($hasstages && !$hasunstaged && $courserows !== []) {
            $stagegroups[] = [
                'stageid' => 0,
                'isstage' => false,
                'heading' => get_string('plan:view:nostage', 'local_learningplans'),
                'countlabel' => get_string('plan:view:stagecount', 'local_learningplans', 0),
                'courses' => [],
            ];
        }
        // A plan without stages keeps the plain flat list (no headings).
        $showheadings = $hasstages;

        $memberships = $service->get_plan_memberships($planid, true);
        $userids = array_map(static function($membership) {
            return $membership->user_id();
        }, $memberships);
        $users = $userrepository->list_by_ids($userids);

        $memberrows = [];
        foreach ($memberships as $membership) {
            $progress = $service->get_user_progress($planid, $membership->user_id());
            $nextcourseid = $progress->next_course_id();
            $nextcoursename = '';
            if ($nextcourseid !== null && isset($courserecords[$nextcourseid])) {
                $nextcoursename = format_string($courserecords[$nextcourseid]->fullname);
            }

            $user = $users[$membership->user_id()] ?? null;
            $fullname = $user ? fullname($user) : ('#' . $membership->user_id());
            $userprofileurl = (new \moodle_url('/user/profile.php', ['id' => $membership->user_id()]))->out(false);
            $userpicture = $user ? $OUTPUT->user_picture($user, ['size' => 32, 'link' => false]) : '';
            $nextcourseurl = '';
            if ($nextcourseid !== null && isset($courserecords[$nextcourseid])) {
                $nextcourseurl = (new \moodle_url('/course/view.php', ['id' => $nextcourseid]))->out(false);
            }
            $memberrows[] = [
                'userid' => $membership->user_id(),
                'fullname' => $fullname,
                'userprofileurl' => $userprofileurl,
                'userpicture' => $userpicture,
                'progresspercent' => $progress->percentage(),
                'progresslabel' => get_string('progress:label', 'local_learningplans', $progress->percentage()),
                'completedcourses' => $progress->completed_courses(),
                'inprogresscourses' => $progress->inprogress_courses(),
                'notstartedcourses' => $progress->notstarted_courses(),
                'nextcourse' => $nextcoursename !== '' ? $nextcoursename : '-',
                'nextcourseurl' => $nextcourseurl,
            ];
        }

        $courselistid = 'learning-plan-course-list-' . $planid;
        $reorderformid = 'learning-plan-course-reorder-form-' . $planid;
        $reorderinputid = 'learning-plan-course-reorder-order-' . $planid;
        $stageinputid = 'learning-plan-course-reorder-stages-' . $planid;
        if (count($courserows) > 1) {
            $PAGE->requires->js_call_amd(
                'local_learningplans/view_reorder',
                'init',
                [
                    '#' . $courselistid,
                    '#' . $reorderformid,
                    '#' . $reorderinputid,
                    '#' . $stageinputid,
                ]
            );
        }

        echo $OUTPUT->header();
        echo $OUTPUT->render_from_template('local_learningplans/view_page', [
            'planid' => $planid,
            'title' => format_string($plan->name()),
            'description' => format_text($plan->description(), FORMAT_HTML, ['context' => $context]),
            'iseditable' => true,
            'editurl' => (new \moodle_url('/local/learningplans/edit.php', ['id' => $planid]))->out(false),
            'enrolurl' => (new \moodle_url('/local/learningplans/enrol.php', ['id' => $planid]))->out(false),
            'cohortsurl' => (new \moodle_url('/local/learningplans/cohorts.php', ['id' => $planid]))->out(false),
            'labeledit' => get_string('list:edit', 'local_learningplans'),
            'labelmanageenrolments' => get_string('plan:view:manageenrolments', 'local_learningplans'),
            'labelmanagecohorts' => get_string('plan:view:managecohorts', 'local_learningplans'),
            'labelcourse' => get_string('plan:view:course', 'local_learningplans'),
            'labelorder' => get_string('plan:view:order', 'local_learningplans'),
            'labelactions' => get_string('plan:view:actions', 'local_learningplans'),
            'labelremove' => get_string('plan:view:remove', 'local_learningplans'),
            'labelmoveup' => get_string('plan:view:moveup', 'local_learningplans'),
            'labelmovedown' => get_string('plan:view:movedown', 'local_learningplans'),
            'labelstage' => get_string('course:stagename', 'local_learningplans'),
            'labelsavestage' => get_string('plan:view:savestage', 'local_learningplans'),
            'stageplaceholder' => get_string('plan:view:stageplaceholder', 'local_learningplans'),
            'stageformurl' => $url->out(false),
            'hascourses' => !empty($courserows),
            'courselistid' => $courselistid,
            'hascoursereorder' => count($courserows) > 1,
            'reorderformid' => $reorderformid,
            'reorderinputid' => $reorderinputid,
            'stageinputid' => $stageinputid,
            'reorderurl' => (new \moodle_url('/local/learningplans/view.php', ['id' => $planid]))->out(false),
            'sesskey' => sesskey(),
            'stagegroups' => $stagegroups,
            'showheadings' => $showheadings,
            'nocourses' => get_string('plan:view:nocourses', 'local_learningplans'),
            'labelmemberships' => get_string('plan:view:memberships', 'local_learningplans'),
            'hasmembers' => !empty($memberrows),
            'members' => $memberrows,
            'nomembers' => get_string('plan:view:nomembers', 'local_learningplans'),
            'labeluser' => get_string('plan:view:user', 'local_learningplans'),
            'labelprogress' => get_string('plan:view:progress', 'local_learningplans'),
            'labelnextcourse' => get_string('plan:view:nextcourse', 'local_learningplans'),
            'labelcompletedcourses' => get_string('plan:view:completedcourses', 'local_learningplans'),
            'labelinprogresscourses' => get_string('plan:view:inprogresscourses', 'local_learningplans'),
            'labelnotstartedcourses' => get_string('plan:view:notstartedcourses', 'local_learningplans'),
            'addcourseform' => $addcourseform->render(),
            'addcoursetitle' => get_string('course:add:title', 'local_learningplans'),
        ]);
        echo $OUTPUT->footer();
    }
}
