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

use local_learningplans\form\link_cohort_form;
use local_learningplans\infrastructure\moodle\cohort\moodle_cohort_reader;
use local_learningplans\infrastructure\moodle\factory\learning_plan_service_factory;
use local_learningplans\task\reconcile_cohort_plan;

defined('MOODLE_INTERNAL') || die();

/**
 * Controller: manage cohorts linked to a learning plan.
 *
 * @package    local_learningplans
 * @copyright  2026
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class manage_cohorts_controller {
    /**
     * Execute page.
     *
     * @return void
     */
    public function execute(): void {
        global $PAGE, $OUTPUT, $USER;

        $context = \context_system::instance();
        require_login();
        require_capability('local/learningplans:manage', $context);

        $service = learning_plan_service_factory::create();
        $cohortreader = new moodle_cohort_reader();

        $planid = required_param('id', PARAM_INT);
        $plan = $service->get_plan($planid);
        $url = new \moodle_url('/local/learningplans/cohorts.php', ['id' => $planid]);

        $action = optional_param('action', '', PARAM_ALPHA);
        $cohortid = optional_param('cohortid', 0, PARAM_INT);
        if ($action === 'unlink' && $cohortid > 0) {
            require_sesskey();
            try {
                $service->unlink_cohort($planid, $cohortid, (int)$USER->id);
                reconcile_cohort_plan::queue($planid);
                \core\notification::success(get_string('cohort:unlinked', 'local_learningplans'));
            } catch (\Throwable $exception) {
                $this->notify_error($exception);
            }
            redirect($url);
        }

        $linkedids = $service->get_plan_cohort_ids($planid);
        $linkedmap = array_fill_keys(array_map('intval', $linkedids), true);

        // Offer only cohorts that are not already linked.
        $available = [];
        foreach ($cohortreader->list_for_selector() as $id => $name) {
            if (!isset($linkedmap[(int)$id])) {
                $available[(int)$id] = $name;
            }
        }

        $form = new link_cohort_form($url, ['planid' => $planid, 'cohorts' => $available]);
        if ($form->is_cancelled()) {
            redirect(new \moodle_url('/local/learningplans/view.php', ['id' => $planid]));
        }
        if ($data = $form->get_data()) {
            require_sesskey();
            try {
                $created = $service->link_cohort($planid, (int)$data->cohortid, (int)$USER->id);
                if ($created) {
                    reconcile_cohort_plan::queue($planid);
                    \core\notification::success(get_string('cohort:linked', 'local_learningplans'));
                } else {
                    \core\notification::info(get_string('cohort:alreadylinked', 'local_learningplans'));
                }
            } catch (\Throwable $exception) {
                $this->notify_error($exception);
            }
            redirect($url);
        }

        $rows = [];
        foreach ($linkedids as $id) {
            $id = (int)$id;
            $name = $cohortreader->get_name($id);
            $rows[] = [
                'cohortid' => $id,
                'name' => $name !== null ? format_string($name) : ('#' . $id),
                'missing' => $name === null,
                'unlinkurl' => (new \moodle_url('/local/learningplans/cohorts.php', [
                    'id' => $planid,
                    'action' => 'unlink',
                    'cohortid' => $id,
                    'sesskey' => sesskey(),
                ]))->out(false),
            ];
        }

        $PAGE->set_context($context);
        $PAGE->set_url($url);
        $PAGE->set_pagelayout('standard');
        $PAGE->set_title(get_string('cohort:title', 'local_learningplans'));
        $PAGE->set_heading(format_string($plan->name()));
        $PAGE->navbar->add(get_string('nav:manage', 'local_learningplans'),
            new \moodle_url('/local/learningplans/index.php'));
        $PAGE->navbar->add(format_string($plan->name()),
            new \moodle_url('/local/learningplans/view.php', ['id' => $planid]));
        $PAGE->navbar->add(get_string('cohort:title', 'local_learningplans'));

        echo $OUTPUT->header();
        echo $OUTPUT->heading(get_string('cohort:title', 'local_learningplans'));
        echo $OUTPUT->render_from_template('local_learningplans/cohorts_page', [
            'intro' => get_string('cohort:intro', 'local_learningplans'),
            'backurl' => (new \moodle_url('/local/learningplans/view.php', ['id' => $planid]))->out(false),
            'backlabel' => get_string('cohort:back', 'local_learningplans'),
            'addtitle' => get_string('cohort:add:title', 'local_learningplans'),
            'addform' => $form->render(),
            'hasrows' => !empty($rows),
            'rows' => $rows,
            'empty' => get_string('cohort:none', 'local_learningplans'),
            'labelcohort' => get_string('cohort:name', 'local_learningplans'),
            'labelactions' => get_string('plan:view:actions', 'local_learningplans'),
            'labelunlink' => get_string('cohort:unlink', 'local_learningplans'),
            'labelmissing' => get_string('cohort:missing', 'local_learningplans'),
        ]);
        echo $OUTPUT->footer();
    }

    /**
     * Render a domain or generic error as a notification.
     *
     * @param \Throwable $exception Exception.
     * @return void
     */
    private function notify_error(\Throwable $exception): void {
        $messagekey = (string)$exception->getMessage();
        if (get_string_manager()->string_exists($messagekey, 'local_learningplans')) {
            \core\notification::error(get_string($messagekey, 'local_learningplans'));
        } else {
            \core\notification::error(s($messagekey));
        }
    }
}