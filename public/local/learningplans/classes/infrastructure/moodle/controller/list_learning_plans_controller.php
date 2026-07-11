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
 * Controller: learning plan list page.
 *
 * @package    local_learningplans
 * @copyright  2026
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class list_learning_plans_controller {
    /**
     * Execute page.
     *
     * @return void
     */
    public function execute(): void {
        global $PAGE, $OUTPUT, $USER;

        $service = learning_plan_service_factory::create();
        $context = \context_system::instance();
        $listurl = new \moodle_url('/local/learningplans/index.php');

        $action = optional_param('action', '', PARAM_ALPHA);
        $planid = optional_param('id', 0, PARAM_INT);
        $page = max(0, optional_param('page', 0, PARAM_INT));
        $perpage = 20;

        if ($action !== '' && $planid > 0) {
            require_sesskey();
            try {
                if ($action === 'archive') {
                    $service->set_plan_enabled($planid, false, (int)$USER->id);
                    \core\notification::success(get_string('plan:archived', 'local_learningplans'));
                } else if ($action === 'activate') {
                    $service->set_plan_enabled($planid, true, (int)$USER->id);
                    \core\notification::success(get_string('plan:activated', 'local_learningplans'));
                } else if ($action === 'delete') {
                    $service->delete_plan($planid, (int)$USER->id);
                    \core\notification::success(get_string('plan:deleted', 'local_learningplans'));
                }
            } catch (\Throwable $exception) {
                $messagekey = (string)$exception->getMessage();
                if (get_string_manager()->string_exists($messagekey, 'local_learningplans')) {
                    \core\notification::error(get_string($messagekey, 'local_learningplans'));
                } else {
                    \core\notification::error(s($messagekey));
                }
            }
            redirect(new \moodle_url('/local/learningplans/index.php', ['page' => $page]));
        }

        $plans = $service->list_plans();
        $totalplans = count($plans);
        $maxpage = max(0, (int)ceil($totalplans / $perpage) - 1);
        if ($page > $maxpage) {
            $page = $maxpage;
        }
        $offset = $page * $perpage;
        $plansslice = array_slice($plans, $offset, $perpage);
        $rows = [];
        foreach ($plansslice as $plan) {
            $id = (int)$plan->id();
            $enabled = $plan->enabled();
            $statuslabel = $enabled
                ? get_string('list:enabled', 'local_learningplans')
                : get_string('list:disabled', 'local_learningplans');
            $statusicon = $enabled
                ? $OUTPUT->pix_icon('i/checkedcircle', $statuslabel, 'core')
                : $OUTPUT->pix_icon('i/hide', $statuslabel, 'core');

            $rows[] = [
                'id' => $id,
                'name' => format_string($plan->name()),
                'statuslabel' => $statuslabel,
                'statusicon' => $statusicon,
                'statusclass' => $enabled
                    ? 'learning-plan__status learning-plan__status--enabled'
                    : 'learning-plan__status learning-plan__status--disabled',
                'coursescount' => count($service->get_plan_courses($id)),
                'memberscount' => count($service->get_plan_memberships($id, true)),
                'viewurl' => (new \moodle_url('/local/learningplans/view.php', ['id' => $id]))->out(false),
                'viewicon' => $OUTPUT->pix_icon('i/preview', get_string('list:view', 'local_learningplans'), 'core'),
                'editurl' => (new \moodle_url('/local/learningplans/edit.php', ['id' => $id]))->out(false),
                'editicon' => $OUTPUT->pix_icon('t/edit', get_string('list:edit', 'local_learningplans'), 'core'),
                'archiveurl' => (new \moodle_url('/local/learningplans/index.php', [
                    'action' => $enabled ? 'archive' : 'activate',
                    'id' => $id,
                    'page' => $page,
                    'sesskey' => sesskey(),
                ]))->out(false),
                'archiveactionlabel' => $enabled
                    ? get_string('list:archive', 'local_learningplans')
                    : get_string('list:activate', 'local_learningplans'),
                'archiveicon' => $enabled
                    ? $OUTPUT->pix_icon('i/hide', get_string('list:archive', 'local_learningplans'), 'core')
                    : $OUTPUT->pix_icon('i/show', get_string('list:activate', 'local_learningplans'), 'core'),
                'deleteurl' => (new \moodle_url('/local/learningplans/index.php', [
                    'action' => 'delete',
                    'id' => $id,
                    'page' => $page,
                    'sesskey' => sesskey(),
                ]))->out(false),
                'deleteicon' => $OUTPUT->pix_icon('t/delete', get_string('list:delete', 'local_learningplans'), 'core'),
            ];
        }

        $PAGE->set_context($context);
        $PAGE->set_url(new \moodle_url('/local/learningplans/index.php', ['page' => $page]));
        $PAGE->set_pagelayout('standard');
        $PAGE->set_title(get_string('list:title', 'local_learningplans'));
        $PAGE->set_heading(get_string('list:title', 'local_learningplans'));

        $pagingbar = '';
        if ($totalplans > $perpage) {
            $pagingbar = $OUTPUT->render(new \paging_bar($totalplans, $page, $perpage, $listurl));
        }

        echo $OUTPUT->header();
        echo $OUTPUT->render_from_template('local_learningplans/list_page', [
            'title' => get_string('list:title', 'local_learningplans'),
            'createurl' => (new \moodle_url('/local/learningplans/edit.php'))->out(false),
            'createlabel' => get_string('list:create', 'local_learningplans'),
            'createicon' => $OUTPUT->pix_icon('t/add', get_string('list:create', 'local_learningplans'), 'core'),
            'hasrows' => !empty($rows),
            'rows' => $rows,
            'empty' => get_string('list:empty', 'local_learningplans'),
            'labelview' => get_string('list:view', 'local_learningplans'),
            'labeledit' => get_string('list:edit', 'local_learningplans'),
            'labeldelete' => get_string('list:delete', 'local_learningplans'),
            'labelplanname' => get_string('plan:name', 'local_learningplans'),
            'labelcourses' => get_string('list:courses', 'local_learningplans'),
            'labelmembers' => get_string('list:members', 'local_learningplans'),
            'labelstatus' => get_string('list:status', 'local_learningplans'),
            'labelactions' => get_string('plan:view:actions', 'local_learningplans'),
            'haspagination' => ($pagingbar !== ''),
            'pagination' => $pagingbar,
        ]);
        echo $OUTPUT->footer();
    }
}
