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

use local_learningplans\form\learning_plan_form;
use local_learningplans\infrastructure\moodle\factory\learning_plan_service_factory;

defined('MOODLE_INTERNAL') || die();

/**
 * Controller: create/edit learning plan.
 *
 * @package    local_learningplans
 * @copyright  2026
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class edit_learning_plan_controller {
    /**
     * Execute page.
     *
     * @return void
     */
    public function execute(): void {
        global $PAGE, $OUTPUT, $USER;

        $service = learning_plan_service_factory::create();
        $id = optional_param('id', 0, PARAM_INT);
        $context = \context_system::instance();
        $url = new \moodle_url('/local/learningplans/edit.php', ['id' => $id]);
        $listurl = new \moodle_url('/local/learningplans/index.php');
        $form = new learning_plan_form($url);

        if ($form->is_cancelled()) {
            redirect(new \moodle_url('/local/learningplans/index.php'));
        }

        if ($data = $form->get_data()) {
            $description = (string)($data->description_editor['text'] ?? '');
            try {
                if (!empty($data->id)) {
                    $plan = $service->update_plan(
                        (int)$data->id,
                        (string)$data->name,
                        $description,
                        !empty($data->enabled),
                        !empty($data->sequentialmode),
                        (string)$data->enrolmentmode,
                        (int)$USER->id
                    );
                    \core\notification::success(get_string('plan:updated', 'local_learningplans'));
                } else {
                    $plan = $service->create_plan(
                        (string)$data->name,
                        $description,
                        !empty($data->enabled),
                        !empty($data->sequentialmode),
                        (string)$data->enrolmentmode,
                        (int)$USER->id
                    );
                    \core\notification::success(get_string('plan:created', 'local_learningplans'));
                }
                redirect(new \moodle_url('/local/learningplans/view.php', ['id' => $plan->id()]));
            } catch (\Throwable $exception) {
                $messagekey = (string)$exception->getMessage();
                if (get_string_manager()->string_exists($messagekey, 'local_learningplans')) {
                    \core\notification::error(get_string($messagekey, 'local_learningplans'));
                } else {
                    \core\notification::error(s($messagekey));
                }
            }
        }

        if ($id > 0) {
            $plan = $service->get_plan($id);
            $form->set_data((object)[
                'id' => $plan->id(),
                'name' => $plan->name(),
                'description_editor' => [
                    'text' => $plan->description(),
                    'format' => FORMAT_HTML,
                ],
                'enabled' => $plan->enabled() ? 1 : 0,
                'sequentialmode' => $plan->sequential_mode() ? 1 : 0,
                'enrolmentmode' => $plan->enrolment_mode(),
            ]);
        }

        $title = $id > 0 ? get_string('plan:edit:title', 'local_learningplans') : get_string(
            'plan:create:title',
            'local_learningplans'
        );

        $PAGE->set_context($context);
        $PAGE->set_url($url);
        $PAGE->set_pagelayout('standard');
        $PAGE->set_title($title);
        $PAGE->set_heading($title);
        $PAGE->navbar->add(get_string('nav:manage', 'local_learningplans'), $listurl);
        $PAGE->navbar->add($title);

        echo $OUTPUT->header();
        $form->display();
        echo $OUTPUT->footer();
    }
}
