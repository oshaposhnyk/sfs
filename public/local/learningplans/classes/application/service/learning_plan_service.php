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

namespace local_learningplans\application\service;

use local_learningplans\application\port\clock_interface;
use local_learningplans\application\port\cohort_link_repository_interface;
use local_learningplans\application\port\cohort_reader_interface;
use local_learningplans\application\port\completion_reader_interface;
use local_learningplans\application\port\course_repository_interface;
use local_learningplans\application\port\event_dispatcher_interface;
use local_learningplans\application\port\learning_plan_enrolment_link_repository_interface;
use local_learningplans\application\port\learning_plan_enrolment_service_interface;
use local_learningplans\application\port\learning_plan_membership_repository_interface;
use local_learningplans\application\port\learning_plan_progress_repository_interface;
use local_learningplans\application\port\learning_plan_repository_interface;
use local_learningplans\application\port\permission_checker_interface;
use local_learningplans\application\port\transaction_manager_interface;
use local_learningplans\application\port\user_repository_interface;
use local_learningplans\domain\entity\learning_plan;
use local_learningplans\domain\entity\learning_plan_membership;
use local_learningplans\domain\exception\domain_exception;
use local_learningplans\domain\policy\progress_calculation_policy;
use local_learningplans\domain\value\enrolment_mode;
use local_learningplans\domain\value\membership_source;
use local_learningplans\domain\value\membership_status;
use local_learningplans\domain\value\progress;

defined('MOODLE_INTERNAL') || die();

/**
 * Application service coordinating use cases for learning plans.
 *
 * @package    local_learningplans
 * @copyright  2026
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class learning_plan_service {
    /** @var learning_plan_repository_interface */
    private learning_plan_repository_interface $planrepository;

    /** @var learning_plan_membership_repository_interface */
    private learning_plan_membership_repository_interface $membershiprepository;

    /** @var learning_plan_enrolment_link_repository_interface */
    private learning_plan_enrolment_link_repository_interface $linkrepository;

    /** @var learning_plan_progress_repository_interface */
    private learning_plan_progress_repository_interface $progressrepository;

    /** @var cohort_link_repository_interface */
    private cohort_link_repository_interface $cohortlinkrepository;

    /** @var learning_plan_enrolment_service_interface */
    private learning_plan_enrolment_service_interface $enrolmentservice;

    /** @var course_repository_interface */
    private course_repository_interface $courserepository;

    /** @var user_repository_interface */
    private user_repository_interface $userrepository;

    /** @var cohort_reader_interface */
    private cohort_reader_interface $cohortreader;

    /** @var completion_reader_interface */
    private completion_reader_interface $completionreader;

    /** @var permission_checker_interface */
    private permission_checker_interface $permissionchecker;

    /** @var event_dispatcher_interface */
    private event_dispatcher_interface $eventdispatcher;

    /** @var transaction_manager_interface */
    private transaction_manager_interface $transactionmanager;

    /** @var clock_interface */
    private clock_interface $clock;

    /** @var progress_calculation_policy */
    private progress_calculation_policy $progresspolicy;

    /**
     * Constructor.
     *
     * @param learning_plan_repository_interface $planrepository Plan repository.
     * @param learning_plan_membership_repository_interface $membershiprepository Membership repository.
     * @param learning_plan_enrolment_link_repository_interface $linkrepository Enrolment link repository.
     * @param learning_plan_progress_repository_interface $progressrepository Progress repository.
     * @param cohort_link_repository_interface $cohortlinkrepository Cohort link repository.
     * @param learning_plan_enrolment_service_interface $enrolmentservice Enrolment service.
     * @param course_repository_interface $courserepository Course repository.
     * @param user_repository_interface $userrepository User repository.
     * @param cohort_reader_interface $cohortreader Cohort reader.
     * @param completion_reader_interface $completionreader Completion reader.
     * @param permission_checker_interface $permissionchecker Permission checker.
     * @param event_dispatcher_interface $eventdispatcher Event dispatcher.
     * @param transaction_manager_interface $transactionmanager Transaction manager.
     * @param clock_interface $clock Clock.
     * @param progress_calculation_policy $progresspolicy Progress policy.
     */
    public function __construct(
        learning_plan_repository_interface $planrepository,
        learning_plan_membership_repository_interface $membershiprepository,
        learning_plan_enrolment_link_repository_interface $linkrepository,
        learning_plan_progress_repository_interface $progressrepository,
        cohort_link_repository_interface $cohortlinkrepository,
        learning_plan_enrolment_service_interface $enrolmentservice,
        course_repository_interface $courserepository,
        user_repository_interface $userrepository,
        cohort_reader_interface $cohortreader,
        completion_reader_interface $completionreader,
        permission_checker_interface $permissionchecker,
        event_dispatcher_interface $eventdispatcher,
        transaction_manager_interface $transactionmanager,
        clock_interface $clock,
        progress_calculation_policy $progresspolicy
    ) {
        $this->planrepository = $planrepository;
        $this->membershiprepository = $membershiprepository;
        $this->linkrepository = $linkrepository;
        $this->progressrepository = $progressrepository;
        $this->cohortlinkrepository = $cohortlinkrepository;
        $this->enrolmentservice = $enrolmentservice;
        $this->courserepository = $courserepository;
        $this->userrepository = $userrepository;
        $this->cohortreader = $cohortreader;
        $this->completionreader = $completionreader;
        $this->permissionchecker = $permissionchecker;
        $this->eventdispatcher = $eventdispatcher;
        $this->transactionmanager = $transactionmanager;
        $this->clock = $clock;
        $this->progresspolicy = $progresspolicy;
    }

    /**
     * List plans.
     *
     * @return array<int, learning_plan>
     */
    public function list_plans(): array {
        $this->permissionchecker->require_view();
        return $this->planrepository->list_all();
    }

    /**
     * Get plan.
     *
     * @param int $planid Plan id.
     * @return learning_plan
     */
    public function get_plan(int $planid): learning_plan {
        $this->permissionchecker->require_view();
        $plan = $this->planrepository->find_by_id($planid);
        if ($plan === null) {
            throw new domain_exception('error:plannotfound');
        }
        return $plan;
    }

    /**
     * Create plan.
     *
     * @param string $name Name.
     * @param string $description Description.
     * @param bool $enabled Enabled.
     * @param bool $sequentialmode Sequential mode.
     * @param string $enrolmentmode Enrolment mode.
     * @param int $actorid Actor id.
     * @return learning_plan
     */
    public function create_plan(
        string $name,
        string $description,
        bool $enabled,
        bool $sequentialmode,
        string $enrolmentmode,
        int $actorid
    ): learning_plan {
        $this->permissionchecker->require_manage();

        $now = $this->clock->now();
        $plan = new learning_plan(
            null,
            $name,
            $description,
            $enabled,
            $sequentialmode,
            $enrolmentmode,
            $actorid,
            $now,
            $now
        );

        $plan = $this->planrepository->create($plan);

        $this->eventdispatcher->dispatch('learning_plan_created', [
            'objectid' => (int)$plan->id(),
            'userid' => $actorid,
        ]);

        return $plan;
    }

    /**
     * Update plan.
     *
     * @param int $planid Plan id.
     * @param string $name Name.
     * @param string $description Description.
     * @param bool $enabled Enabled.
     * @param bool $sequentialmode Sequential mode.
     * @param string $enrolmentmode Enrolment mode.
     * @param int $actorid Actor id.
     * @return learning_plan
     */
    public function update_plan(
        int $planid,
        string $name,
        string $description,
        bool $enabled,
        bool $sequentialmode,
        string $enrolmentmode,
        int $actorid
    ): learning_plan {
        $this->permissionchecker->require_manage();
        $current = $this->require_plan($planid);

        $plan = new learning_plan(
            $planid,
            $name,
            $description,
            $enabled,
            $sequentialmode,
            $enrolmentmode,
            $current->created_by(),
            $current->time_created(),
            $this->clock->now()
        );
        $plan = $this->planrepository->update($plan);

        $this->eventdispatcher->dispatch('learning_plan_updated', [
            'objectid' => $planid,
            'userid' => $actorid,
        ]);

        return $plan;
    }

    /**
     * Archive or activate plan.
     *
     * @param int $planid Plan id.
     * @param bool $enabled Enabled flag.
     * @param int $actorid Actor id.
     * @return learning_plan
     */
    public function set_plan_enabled(int $planid, bool $enabled, int $actorid): learning_plan {
        $this->permissionchecker->require_manage();
        $plan = $this->require_plan($planid);
        $updated = new learning_plan(
            $planid,
            $plan->name(),
            $plan->description(),
            $enabled,
            $plan->sequential_mode(),
            $plan->enrolment_mode(),
            $plan->created_by(),
            $plan->time_created(),
            $this->clock->now()
        );
        $updated = $this->planrepository->update($updated);

        $this->eventdispatcher->dispatch('learning_plan_archived', [
            'objectid' => $planid,
            'userid' => $actorid,
            'other' => [
                'enabled' => $enabled ? 1 : 0,
            ],
        ]);

        return $updated;
    }

    /**
     * Delete plan when safe.
     *
     * @param int $planid Plan id.
     * @param int $actorid Actor id.
     * @return void
     */
    public function delete_plan(int $planid, int $actorid): void {
        $this->permissionchecker->require_manage();
        $this->require_plan($planid);

        if ($this->membershiprepository->count_active_by_plan($planid) > 0) {
            throw new domain_exception('error:cannotdeletewithactivememberships');
        }

        $this->transactionmanager->run(function() use ($planid) {
            $this->progressrepository->delete_by_plan($planid);
            $this->linkrepository->delete_by_plan($planid);
            $this->cohortlinkrepository->delete_by_plan($planid);
            $this->membershiprepository->delete_by_plan($planid);
            $this->planrepository->delete($planid);
        });

        $this->eventdispatcher->dispatch('learning_plan_deleted', [
            'objectid' => $planid,
            'userid' => $actorid,
        ]);
    }

    /**
     * Add course to plan.
     *
     * @param int $planid Plan id.
     * @param int $courseid Course id.
     * @param int $actorid Actor id.
     * @return void
     */
    public function add_course(int $planid, int $courseid, int $actorid, string $stagename = ''): void {
        $this->permissionchecker->require_manage();
        $this->require_plan($planid);
        $this->require_course($courseid);

        $this->planrepository->add_course($planid, $courseid, $stagename);
        $this->progressrepository->invalidate_plan($planid);

        $this->eventdispatcher->dispatch('course_added', [
            'objectid' => $planid,
            'userid' => $actorid,
            'courseid' => $courseid,
        ]);

        $this->recalculate_plan_members($planid, $actorid);
    }

    /**
     * Remove course from plan.
     *
     * @param int $planid Plan id.
     * @param int $courseid Course id.
     * @param int $actorid Actor id.
     * @return void
     */
    public function remove_course(int $planid, int $courseid, int $actorid): void {
        $this->permissionchecker->require_manage();
        $this->require_plan($planid);
        $this->planrepository->remove_course($planid, $courseid);
        $this->progressrepository->invalidate_plan($planid);

        $this->eventdispatcher->dispatch('course_removed', [
            'objectid' => $planid,
            'userid' => $actorid,
            'courseid' => $courseid,
        ]);

        $this->recalculate_plan_members($planid, $actorid);
    }

    /**
     * Rename the stage a plan course belongs to ('' clears the stage).
     *
     * Presentation-level grouping only: enrolments and progress are not
     * affected, so no members recalculation and no event.
     *
     * @param int $planid Plan id.
     * @param int $courseid Course id.
     * @param string $stagename New stage name.
     * @param int $actorid Actor id (permission check context).
     * @return void
     */
    public function set_course_stage(int $planid, int $courseid, string $stagename, int $actorid): void {
        $this->permissionchecker->require_manage();
        $this->require_plan($planid);
        $this->planrepository->set_course_stage($planid, $courseid, $stagename);
        $this->progressrepository->invalidate_plan($planid);
    }

    /**
     * Move course in plan.
     *
     * @param int $planid Plan id.
     * @param int $courseid Course id.
     * @param int $direction Direction.
     * @param int $actorid Actor id.
     * @return void
     */
    public function move_course(int $planid, int $courseid, int $direction, int $actorid): void {
        $this->permissionchecker->require_manage();
        $this->require_plan($planid);
        $this->planrepository->move_course($planid, $courseid, $direction);
        $this->progressrepository->invalidate_plan($planid);

        $this->eventdispatcher->dispatch('courses_reordered', [
            'objectid' => $planid,
            'userid' => $actorid,
        ]);

        $this->recalculate_plan_members($planid, $actorid);
    }

    /**
     * Reorder all courses in plan.
     *
     * @param int $planid Plan id.
     * @param array<int, int> $orderedcourseids Ordered course ids.
     * @param int $actorid Actor id.
     * @return void
     */
    public function reorder_courses(int $planid, array $orderedcourseids, int $actorid): void {
        $this->permissionchecker->require_manage();
        $this->require_plan($planid);

        $existingcourses = $this->planrepository->get_courses($planid);
        if (count($existingcourses) <= 1) {
            return;
        }

        $existingids = array_map(static function($courseitem): int {
            return (int)$courseitem->course_id();
        }, $existingcourses);
        $expected = $existingids;
        sort($expected);

        $normalized = [];
        foreach ($orderedcourseids as $courseid) {
            $value = (int)$courseid;
            if ($value > 0) {
                $normalized[] = $value;
            }
        }
        $normalized = array_values(array_unique($normalized));
        $actual = $normalized;
        sort($actual);

        if ($actual !== $expected) {
            throw new domain_exception('error:invalidcourseorder');
        }

        $this->planrepository->reorder_courses($planid, $normalized);
        $this->progressrepository->invalidate_plan($planid);

        $this->eventdispatcher->dispatch('courses_reordered', [
            'objectid' => $planid,
            'userid' => $actorid,
        ]);

        $this->recalculate_plan_members($planid, $actorid);
    }

    /**
     * Enrol users into plan.
     *
     * @param int $planid Plan id.
     * @param array<int, int> $userids User ids.
     * @param int $actorid Actor id.
     * @return array<string, array<int, int>>
     */
    public function enrol_users(int $planid, array $userids, int $actorid): array {
        $this->permissionchecker->require_enrol_users();
        $plan = $this->require_plan($planid);
        if ($this->planrepository->count_courses($planid) === 0) {
            throw new domain_exception('error:nocoursesinplan');
        }

        foreach ($this->planrepository->get_courses($planid) as $courseitem) {
            $this->permissionchecker->require_course_enrol_permission($courseitem->course_id());
        }

        $roleid = (int)get_config('local_learningplans', 'defaultroleid');
        if ($roleid <= 0) {
            $roleid = 5;
        }

        $succeeded = [];
        $failed = [];
        foreach ($userids as $userid) {
            if (!$this->userrepository->exists($userid)) {
                $failed[] = $userid;
                continue;
            }

            try {
                $membership = $this->transactionmanager->run(function() use ($planid, $userid, $actorid) {
                    return $this->membershiprepository->upsert_active(
                        $planid,
                        $userid,
                        $actorid,
                        $this->clock->now(),
                        membership_source::MANUAL
                    );
                });

                $this->eventdispatcher->dispatch('user_enrolled', [
                    'objectid' => $planid,
                    'userid' => $actorid,
                    'relateduserid' => $userid,
                ]);

                $this->sync_membership_enrolments($plan, $membership, $roleid, $actorid);
                $this->recalculate_progress($planid, $userid, $actorid);
                $succeeded[] = $userid;
            } catch (\Throwable $exception) {
                $failed[] = $userid;
                $this->eventdispatcher->dispatch('enrolment_failed', [
                    'objectid' => $planid,
                    'userid' => $actorid,
                    'relateduserid' => $userid,
                ]);
            }
        }

        return [
            'succeeded' => $succeeded,
            'failed' => $failed,
        ];
    }

    /**
     * Unenrol user from plan.
     *
     * @param int $planid Plan id.
     * @param int $userid User id.
     * @param int $actorid Actor id.
     * @return void
     */
    public function unenrol_user(int $planid, int $userid, int $actorid): void {
        $this->permissionchecker->require_unenrol_users();
        $this->require_plan($planid);

        $membership = $this->membershiprepository->find_by_plan_and_user($planid, $userid);
        if ($membership === null) {
            throw new domain_exception('error:membershipnotfound');
        }

        $this->do_unenrol($planid, $userid, $membership, $actorid);
    }

    /**
     * Deactivate a membership and apply the course-enrolment unenrol policy.
     *
     * Shared by manual unenrol and automatic cohort removal so that both honour
     * the configured policy and emit the same lifecycle events.
     *
     * @param int $planid Plan id.
     * @param int $userid User id.
     * @param learning_plan_membership $membership Membership being removed.
     * @param int $actorid Actor id (0 for system/cohort).
     * @return void
     */
    private function do_unenrol(
        int $planid,
        int $userid,
        learning_plan_membership $membership,
        int $actorid
    ): void {
        $policy = (string)get_config('local_learningplans', 'unenrolpolicy');
        if ($policy === '') {
            $policy = 'remove';
        }

        $this->transactionmanager->run(function() use ($planid, $userid, $membership, $policy, $actorid) {
            if ($policy === 'remove') {
                $links = $this->linkrepository->list_by_membership($membership->id(), true);
                foreach ($links as $link) {
                    $this->enrolmentservice->unenrol_user($planid, (int)$link->courseid, $userid);
                    $this->linkrepository->mark_removed($membership->id(), (int)$link->courseid, $this->clock->now());
                    $this->eventdispatcher->dispatch('course_enrolment_removed', [
                        'objectid' => $planid,
                        'userid' => $actorid,
                        'relateduserid' => $userid,
                        'courseid' => (int)$link->courseid,
                    ]);
                }
            }

            $this->membershiprepository->deactivate($planid, $userid, $this->clock->now());
            $this->progressrepository->invalidate($planid, $userid);
        });

        $this->eventdispatcher->dispatch('user_unenrolled', [
            'objectid' => $planid,
            'userid' => $actorid,
            'relateduserid' => $userid,
        ]);
    }

    /**
     * Recalculate progress for one plan member.
     *
     * @param int $planid Plan id.
     * @param int $userid User id.
     * @param int $actorid Actor id.
     * @return progress
     */
    public function recalculate_progress(int $planid, int $userid, int $actorid = 0): progress {
        $plan = $this->require_plan($planid);
        $membership = $this->membershiprepository->find_by_plan_and_user($planid, $userid);
        if ($membership === null || !$membership->is_active()) {
            return new progress(0, 0, 0, 0, 0.0, null);
        }

        $progress = $this->calculate_progress($plan, $userid);
        $this->progressrepository->save($planid, $userid, $progress, $this->clock->now());

        $this->eventdispatcher->dispatch('progress_recalculated', [
            'objectid' => $planid,
            'userid' => $actorid,
            'relateduserid' => $userid,
            'other' => [
                'progress' => $progress->percentage(),
                'completed' => $progress->completed_courses(),
                'total' => $progress->total_courses(),
                'nextcourseid' => $progress->next_course_id(),
            ],
        ]);

        if ($progress->is_completed() && $membership->time_completed() === null) {
            $this->membershiprepository->mark_completed($membership->id(), $this->clock->now());
            $this->eventdispatcher->dispatch('user_completed', [
                'objectid' => $planid,
                'userid' => $actorid,
                'relateduserid' => $userid,
            ]);
        } else if (!$progress->is_completed() && $membership->time_completed() !== null) {
            $this->membershiprepository->clear_completed($membership->id());
        }

        if ($plan->sequential_mode() && $plan->enrolment_mode() === enrolment_mode::SEQUENTIAL_RELEASE) {
            $roleid = (int)get_config('local_learningplans', 'defaultroleid');
            if ($roleid <= 0) {
                $roleid = 5;
            }
            $this->sync_membership_enrolments($plan, $membership, $roleid, $actorid);
        }

        return $progress;
    }

    /**
     * Recalculate all active members in one plan.
     *
     * @param int $planid Plan id.
     * @param int $actorid Actor id.
     * @return void
     */
    public function recalculate_plan_members(int $planid, int $actorid = 0): void {
        $memberships = $this->membershiprepository->list_by_plan($planid, true);
        foreach ($memberships as $membership) {
            $this->recalculate_progress($planid, $membership->user_id(), $actorid);
        }
    }

    /**
     * Membership observer action for course completion.
     *
     * @param int $courseid Course id.
     * @param int $userid User id.
     * @return void
     */
    public function handle_course_completed(int $courseid, int $userid): void {
        $planids = $this->planrepository->get_plan_ids_by_course($courseid);
        foreach ($planids as $planid) {
            $this->recalculate_progress((int)$planid, $userid, 0);
        }
    }

    /**
     * Observer action for course deletion.
     *
     * @param int $courseid Course id.
     * @return void
     */
    public function handle_course_deleted(int $courseid): void {
        $planids = $this->planrepository->get_plan_ids_by_course($courseid);
        foreach ($planids as $planid) {
            $this->planrepository->remove_course((int)$planid, $courseid);
            $this->progressrepository->invalidate_plan((int)$planid);
            $this->recalculate_plan_members((int)$planid, 0);
        }
    }

    /**
     * Observer action for user deletion.
     *
     * @param int $userid User id.
     * @return void
     */
    public function handle_user_deleted(int $userid): void {
        $memberships = $this->membershiprepository->list_active_by_user($userid);
        foreach ($memberships as $membership) {
            $this->membershiprepository->deactivate($membership->plan_id(), $userid, $this->clock->now());
            $this->progressrepository->invalidate($membership->plan_id(), $userid);
        }
    }

    /**
     * Observer action for user_enrolment_deleted.
     *
     * @param int $userenrolmentid User enrolment id.
     * @return void
     */
    public function handle_user_enrolment_deleted(int $userenrolmentid): void {
        $link = $this->linkrepository->find_by_userenrolmentid($userenrolmentid);
        if (!$link) {
            return;
        }

        $this->linkrepository->mark_removed_by_userenrolmentid($userenrolmentid, $this->clock->now());
        $this->progressrepository->invalidate((int)$link->planid, (int)$link->userid);
    }

    /**
     * Link a cohort to a plan.
     *
     * Membership reconciliation is deferred to a background task so that linking
     * a large cohort never blocks the request.
     *
     * @param int $planid Plan id.
     * @param int $cohortid Cohort id.
     * @param int $actorid Actor id.
     * @return bool True when a new link was created.
     */
    public function link_cohort(int $planid, int $cohortid, int $actorid): bool {
        $this->permissionchecker->require_manage();
        $this->require_plan($planid);
        if (!$this->cohortreader->exists($cohortid)) {
            throw new domain_exception('error:cohortnotfound');
        }

        $created = $this->cohortlinkrepository->link($planid, $cohortid, $actorid, $this->clock->now());
        if ($created) {
            $this->eventdispatcher->dispatch('cohort_linked', [
                'objectid' => $planid,
                'userid' => $actorid,
                'other' => ['cohortid' => $cohortid],
            ]);
        }
        return $created;
    }

    /**
     * Remove a cohort link from a plan.
     *
     * @param int $planid Plan id.
     * @param int $cohortid Cohort id.
     * @param int $actorid Actor id.
     * @return void
     */
    public function unlink_cohort(int $planid, int $cohortid, int $actorid): void {
        $this->permissionchecker->require_manage();
        $this->require_plan($planid);
        if (!$this->cohortlinkrepository->exists($planid, $cohortid)) {
            return;
        }

        $this->cohortlinkrepository->unlink($planid, $cohortid);
        $this->eventdispatcher->dispatch('cohort_unlinked', [
            'objectid' => $planid,
            'userid' => $actorid,
            'other' => ['cohortid' => $cohortid],
        ]);
    }

    /**
     * Cohort ids linked to a plan.
     *
     * @param int $planid Plan id.
     * @return array<int, int>
     */
    public function get_plan_cohort_ids(int $planid): array {
        $this->permissionchecker->require_view();
        return $this->cohortlinkrepository->get_cohort_ids_by_plan($planid);
    }

    /**
     * Plan ids linked to a cohort. Used by observers to decide what to reconcile.
     *
     * @param int $cohortid Cohort id.
     * @return array<int, int>
     */
    public function get_plan_ids_for_cohort(int $cohortid): array {
        return $this->cohortlinkrepository->get_plan_ids_by_cohort($cohortid);
    }

    /**
     * Observer action for cohort deletion: drop links and report affected plans.
     *
     * @param int $cohortid Cohort id.
     * @return array<int, int> Plan ids that were linked to the cohort.
     */
    public function handle_cohort_deleted(int $cohortid): array {
        $planids = $this->cohortlinkrepository->get_plan_ids_by_cohort($cohortid);
        $this->cohortlinkrepository->delete_by_cohort($cohortid);
        return $planids;
    }

    /**
     * Reconcile cohort-driven memberships for every plan that has a cohort link.
     *
     * @return void
     */
    public function reconcile_all(): void {
        foreach ($this->cohortlinkrepository->get_linked_plan_ids() as $planid) {
            $this->reconcile_plan((int)$planid);
        }
    }

    /**
     * Reconcile cohort-driven memberships of a single plan against its cohorts.
     *
     * Idempotent and self-healing: the desired member set is the union of all
     * linked cohorts. Manual memberships are never created or removed here.
     *
     * @param int $planid Plan id.
     * @return void
     */
    public function reconcile_plan(int $planid): void {
        if (!$this->cohort_sync_enabled()) {
            return;
        }

        $plan = $this->planrepository->find_by_id($planid);
        if ($plan === null || !$plan->enabled()) {
            // Archived or missing plans are skipped; the scheduled task picks
            // them up again once they are re-enabled.
            return;
        }

        $cohortids = $this->cohortlinkrepository->get_cohort_ids_by_plan($planid);

        $desired = [];
        foreach ($cohortids as $cohortid) {
            foreach ($this->cohortreader->list_member_ids((int)$cohortid) as $userid) {
                $desired[(int)$userid] = true;
            }
        }

        foreach (array_keys($desired) as $userid) {
            $this->cohort_enrol_user($plan, (int)$userid);
        }

        // Remove cohort-sourced members that no longer belong to any linked cohort.
        $current = $this->membershiprepository->list_active_by_plan_and_source($planid, membership_source::COHORT);
        foreach ($current as $membership) {
            if (!isset($desired[$membership->user_id()])) {
                $this->do_unenrol($planid, $membership->user_id(), $membership, 0);
            }
        }
    }

    /**
     * Get courses in plan.
     *
     * @param int $planid Plan id.
     * @return array<int, \local_learningplans\domain\entity\learning_plan_course>
     */
    public function get_plan_courses(int $planid): array {
        $this->permissionchecker->require_view();
        return $this->planrepository->get_courses($planid);
    }

    /**
     * Get plan memberships.
     *
     * @param int $planid Plan id.
     * @param bool $activeonly Active only.
     * @return array<int, learning_plan_membership>
     */
    public function get_plan_memberships(int $planid, bool $activeonly = false): array {
        $this->permissionchecker->require_view_progress();
        return $this->membershiprepository->list_by_plan($planid, $activeonly);
    }

    /**
     * List active memberships for one user.
     *
     * @param int $userid User id.
     * @return array<int, learning_plan_membership>
     */
    public function get_user_memberships(int $userid): array {
        $this->permissionchecker->require_view_progress();
        return $this->membershiprepository->list_active_by_user($userid);
    }

    /**
     * Get user progress from cache or compute.
     *
     * @param int $planid Plan id.
     * @param int $userid User id.
     * @return progress
     */
    public function get_user_progress(int $planid, int $userid): progress {
        $this->permissionchecker->require_view_progress();
        $cached = $this->progressrepository->get($planid, $userid);
        if ($cached !== null) {
            return $cached;
        }
        return $this->recalculate_progress($planid, $userid, 0);
    }

    /**
     * Force stale progress refresh.
     *
     * @param int $olderthan Older-than timestamp.
     * @param int $limit Limit.
     * @return int
     */
    public function refresh_stale_progress(int $olderthan, int $limit = 1000): int {
        $rows = $this->progressrepository->find_stale($olderthan, $limit);
        foreach ($rows as $row) {
            $this->recalculate_progress((int)$row->planid, (int)$row->userid, 0);
        }
        return count($rows);
    }

    /**
     * Require existing plan.
     *
     * @param int $planid Plan id.
     * @return learning_plan
     */
    private function require_plan(int $planid): learning_plan {
        $plan = $this->planrepository->find_by_id($planid);
        if ($plan === null) {
            throw new domain_exception('error:plannotfound');
        }
        return $plan;
    }

    /**
     * Require existing course.
     *
     * @param int $courseid Course id.
     * @return \stdClass
     */
    private function require_course(int $courseid): \stdClass {
        $course = $this->courserepository->find_by_id($courseid);
        if (!$course) {
            throw new domain_exception('error:coursenotfound');
        }
        return $course;
    }

    /**
     * Calculate progress by policy.
     *
     * @param learning_plan $plan Plan.
     * @param int $userid User id.
     * @return progress
     */
    private function calculate_progress(learning_plan $plan, int $userid): progress {
        $courses = $this->planrepository->get_courses((int)$plan->id());
        $completion = [];
        $enrolment = [];
        foreach ($courses as $courseitem) {
            $courseid = $courseitem->course_id();
            $completion[$courseid] = $this->completionreader->is_course_complete($courseid, $userid);
            $enrolment[$courseid] = $this->courserepository->is_user_enrolled($courseid, $userid);
        }

        return $this->progresspolicy->calculate($courses, $completion, $enrolment, $plan->sequential_mode());
    }

    /**
     * Enrol membership into plan courses following current policy.
     *
     * @param learning_plan $plan Plan.
     * @param learning_plan_membership $membership Membership.
     * @param int $roleid Role id.
     * @param int $actorid Actor id.
     * @return void
     */
    private function sync_membership_enrolments(
        learning_plan $plan,
        learning_plan_membership $membership,
        int $roleid,
        int $actorid
    ): void {
        $courses = $this->planrepository->get_courses((int)$plan->id());
        $targetcourseids = [];

        if (!$plan->sequential_mode() || $plan->enrolment_mode() === enrolment_mode::IMMEDIATE) {
            foreach ($courses as $courseitem) {
                $targetcourseids[] = $courseitem->course_id();
            }
        } else {
            foreach ($courses as $courseitem) {
                $courseid = $courseitem->course_id();
                if (!$this->completionreader->is_course_complete($courseid, $membership->user_id())) {
                    $targetcourseids[] = $courseid;
                    break;
                }
            }
        }

        foreach ($targetcourseids as $courseid) {
            $result = $this->enrolmentservice->enrol_user((int)$plan->id(), $courseid, $membership->user_id(), $roleid);
            $this->linkrepository->upsert_active(
                $membership->id(),
                (int)$plan->id(),
                $courseid,
                $membership->user_id(),
                (int)$result['enrolid'],
                isset($result['userenrolmentid']) ? (int)$result['userenrolmentid'] : null,
                $this->clock->now()
            );

            $this->eventdispatcher->dispatch('course_enrolment_created', [
                'objectid' => (int)$plan->id(),
                'userid' => $actorid,
                'relateduserid' => $membership->user_id(),
                'courseid' => $courseid,
                'other' => [
                    'enrolid' => $result['enrolid'],
                    'userenrolmentid' => $result['userenrolmentid'],
                    'membershipid' => $membership->id(),
                ],
            ]);
        }
    }

    /**
     * Create or reactivate a cohort-driven membership and sync its enrolments.
     *
     * System-driven (actor 0). Skips users who are missing, and never downgrades
     * a manual membership. Failures are isolated per user so one bad record does
     * not abort a whole cohort reconciliation.
     *
     * @param learning_plan $plan Plan.
     * @param int $userid User id.
     * @return void
     */
    private function cohort_enrol_user(learning_plan $plan, int $userid): void {
        $planid = (int)$plan->id();
        if (!$this->userrepository->exists($userid)) {
            return;
        }

        $existing = $this->membershiprepository->find_by_plan_and_user($planid, $userid);
        $wasactive = $existing !== null && $existing->is_active();

        try {
            $membership = $this->transactionmanager->run(function() use ($planid, $userid) {
                return $this->membershiprepository->upsert_active(
                    $planid,
                    $userid,
                    0,
                    $this->clock->now(),
                    membership_source::COHORT
                );
            });
        } catch (\Throwable $exception) {
            $this->eventdispatcher->dispatch('enrolment_failed', [
                'objectid' => $planid,
                'userid' => 0,
                'relateduserid' => $userid,
            ]);
            return;
        }

        if (!$wasactive) {
            $this->eventdispatcher->dispatch('user_enrolled', [
                'objectid' => $planid,
                'userid' => 0,
                'relateduserid' => $userid,
            ]);
            if ($this->planrepository->count_courses($planid) > 0) {
                $this->sync_membership_enrolments($plan, $membership, $this->default_role_id(), 0);
            }
        }

        $this->recalculate_progress($planid, $userid, 0);
    }

    /**
     * Whether automatic cohort-driven enrolment is enabled. Defaults to on.
     *
     * @return bool
     */
    private function cohort_sync_enabled(): bool {
        $value = get_config('local_learningplans', 'cohortsync');
        return $value === false || (string)$value === '1';
    }

    /**
     * Resolve the configured default course role, falling back to student (5).
     *
     * @return int
     */
    private function default_role_id(): int {
        $roleid = (int)get_config('local_learningplans', 'defaultroleid');
        return $roleid > 0 ? $roleid : 5;
    }
}
