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
 * English strings for local_learningplans.
 *
 * @package    local_learningplans
 * @copyright  2026
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['pluginname'] = 'Learning plans';
$string['nav:manage'] = 'Manage learning plans';
$string['privacy:metadata'] = 'The Learning plans plugin stores user memberships, linked enrolments, and progress snapshots.';

$string['settings:title'] = 'Learning plans settings';
$string['settings:unenrolpolicy'] = 'Plan unenrolment policy';
$string['settings:unenrolpolicy_desc'] = 'Defines what happens to course enrolments created by learning plan membership when a user is unenrolled from a plan.';
$string['settings:unenrolpolicy:remove'] = 'Unenrol users from course enrolments managed by the plan';
$string['settings:unenrolpolicy:keep'] = 'Keep course enrolments and only remove plan membership';
$string['settings:defaultroleid'] = 'Default course role';
$string['settings:defaultroleid_desc'] = 'Role assigned when users are enrolled to courses through a learning plan.';
$string['settings:cohortsync'] = 'Enable cohort synchronisation';
$string['settings:cohortsync_desc'] = 'When enabled, adding a user to a cohort linked to a learning plan enrols them into that plan automatically, and removing them unenrols them (subject to the unenrolment policy). Disable to pause all automatic cohort-driven enrolment without removing existing links.';
$string['settings:studentlabheading'] = 'Student Lab page';
$string['settings:studentlabheading_desc'] = 'No-code controls for the learner-facing Student Lab page. Empty text settings use the design defaults.';
$string['settings:showstudentlabheader'] = 'Show Student Lab header';
$string['settings:showstudentlabheader_desc'] = 'Display the kicker, title and intro copy at the top of Student Lab.';
$string['settings:showstudentlabcontinue'] = 'Show Continue learning button';
$string['settings:showstudentlabcontinue_desc'] = 'Display the Continue learning call-to-action when the learner has an available next course.';
$string['settings:showstudentlabplanbar'] = 'Show active plan bar';
$string['settings:showstudentlabplanbar_desc'] = 'Display the active learning-plan selector, counters and progress bar.';
$string['settings:showstudentlabstages'] = 'Show course stages';
$string['settings:showstudentlabstages_desc'] = 'Display the stage and course-card grid.';
$string['settings:studentlabkicker'] = 'Student Lab kicker';
$string['settings:studentlabtitle'] = 'Student Lab title';
$string['settings:studentlablede'] = 'Student Lab intro text';
$string['settings:studentlabempty'] = 'Student Lab empty-plan message';
$string['settings:studentlabnocourses'] = 'Student Lab no-courses message';
$string['settings:studentlabfallback_desc'] = 'Leave empty to use the default Student Lab copy.';

$string['learningplans:view'] = 'View learning plans';
$string['learningplans:manage'] = 'Manage learning plans';
$string['learningplans:enrolusers'] = 'Enrol users into learning plans';
$string['learningplans:unenrolusers'] = 'Unenrol users from learning plans';
$string['learningplans:viewprogress'] = 'View learning plan progress';
$string['learningplans:viewallprogress'] = 'View other users learning plan progress';
$string['learningplans:configure'] = 'Configure learning plans plugin';

$string['list:title'] = 'Learning plans';
$string['list:mytitle'] = 'My learning plans';
$string['list:empty'] = 'No learning plans found.';
$string['list:create'] = 'Create learning plan';
$string['list:view'] = 'View';
$string['list:edit'] = 'Edit';
$string['list:archive'] = 'Archive';
$string['list:activate'] = 'Activate';
$string['list:delete'] = 'Delete';
$string['list:courses'] = 'Courses';
$string['list:members'] = 'Members';
$string['list:status'] = 'Status';
$string['list:enabled'] = 'Enabled';
$string['list:disabled'] = 'Archived';

$string['plan:create:title'] = 'Create learning plan';
$string['plan:edit:title'] = 'Edit learning plan';
$string['plan:name'] = 'Learning plan name';
$string['plan:description'] = 'Description';
$string['plan:enabled'] = 'Enabled';
$string['plan:sequentialmode'] = 'Require sequential completion';
$string['plan:enrolmentmode'] = 'Course enrolment mode';
$string['plan:enrolmentmode:immediate'] = 'Enrol in all plan courses immediately';
$string['plan:enrolmentmode:sequentialrelease'] = 'Enrol only in currently available sequential course(s)';
$string['plan:save'] = 'Save changes';
$string['plan:created'] = 'Learning plan created.';
$string['plan:updated'] = 'Learning plan updated.';
$string['plan:deleted'] = 'Learning plan deleted.';
$string['plan:archived'] = 'Learning plan archived.';
$string['plan:activated'] = 'Learning plan activated.';
$string['plan:delete:confirm'] = 'Delete learning plan "{$a}"?';

$string['plan:view:title'] = 'Learning plan';
$string['plan:view:manageenrolments'] = 'Manage enrolments';
$string['plan:view:managecohorts'] = 'Manage cohorts';
$string['plan:view:addcourse'] = 'Add course';
$string['plan:view:nocourses'] = 'No courses added yet.';
$string['plan:view:course'] = 'Course';
$string['plan:view:order'] = 'Order';
$string['plan:view:actions'] = 'Actions';
$string['plan:view:remove'] = 'Remove';
$string['plan:view:moveup'] = 'Move up';
$string['plan:view:movedown'] = 'Move down';
$string['plan:view:memberships'] = 'Plan members';
$string['plan:view:nomembers'] = 'No members enrolled yet.';
$string['plan:view:user'] = 'User';
$string['plan:view:progress'] = 'Progress';
$string['plan:view:nextcourse'] = 'Next required course';
$string['plan:view:completedcourses'] = 'Completed';
$string['plan:view:inprogresscourses'] = 'In progress';
$string['plan:view:notstartedcourses'] = 'Not started';

$string['course:add:title'] = 'Add course to learning plan';
$string['course:add:courseid'] = 'Course';
$string['course:add:submit'] = 'Add course';
$string['course:added'] = 'Course added to learning plan.';
$string['course:removed'] = 'Course removed from learning plan.';
$string['course:reordered'] = 'Learning plan courses reordered.';
$string['course:duplicate'] = 'The course already belongs to this learning plan.';

$string['cohort:title'] = 'Linked cohorts';
$string['cohort:intro'] = 'Users who belong to a linked cohort are enrolled into this learning plan automatically. Removing a user from every linked cohort unenrols them from the plan, unless they were also enrolled manually. Manual enrolments are never removed by cohort synchronisation.';
$string['cohort:back'] = 'Back to learning plan';
$string['cohort:name'] = 'Cohort';
$string['cohort:none'] = 'No cohorts are linked to this learning plan yet.';
$string['cohort:add:title'] = 'Link a cohort';
$string['cohort:add:cohort'] = 'Cohort';
$string['cohort:add:submit'] = 'Link cohort';
$string['cohort:linked'] = 'Cohort linked. Members will be enrolled in the background shortly.';
$string['cohort:alreadylinked'] = 'That cohort is already linked to this learning plan.';
$string['cohort:unlink'] = 'Unlink';
$string['cohort:unlinked'] = 'Cohort unlinked. Affected members will be updated in the background shortly.';
$string['cohort:missing'] = 'deleted';

$string['enrol:title'] = 'Manage plan enrolments';
$string['enrol:selectusers'] = 'Users';
$string['enrol:selectusers_help'] = 'Search and select one or more users to enrol into this learning plan.';
$string['enrol:submit'] = 'Enrol user(s)';
$string['enrol:success'] = 'User(s) enrolled into learning plan.';
$string['enrol:unenrol'] = 'Unenrol';
$string['enrol:unenrolconfirm'] = 'Unenrol user "{$a}" from learning plan?';
$string['enrol:removed'] = 'User unenrolled from learning plan.';
$string['enrol:failed'] = 'Unable to create course enrolments for this membership.';

$string['progress:label'] = '{$a}% complete';
$string['progress:completed'] = 'Completed';
$string['progress:inprogress'] = 'In progress';
$string['progress:notstarted'] = 'Not started';
$string['progress:total'] = 'Total courses';
$string['progress:none'] = 'No progress data yet.';
$string['progress:usercompletedplan'] = 'User completed learning plan.';
$string['my:empty'] = 'You are not enrolled in any learning plan yet.';

$string['error:plannotfound'] = 'Learning plan not found.';
$string['error:membershipnotfound'] = 'Learning plan membership not found.';
$string['error:cannotdeletewithactivememberships'] = 'Cannot delete a learning plan with active memberships.';
$string['error:coursenotfound'] = 'Course not found.';
$string['error:usernotfound'] = 'User not found.';
$string['error:invaliduserids'] = 'Select at least one valid user.';
$string['error:invalidcourseorder'] = 'Invalid course order payload.';
$string['error:nocoursesinplan'] = 'No courses found in this learning plan.';
$string['error:insufficientcapability'] = 'You do not have permission to perform this action.';
$string['error:cohortnotfound'] = 'Cohort not found.';

$string['event:learningplancreated'] = 'Learning plan created';
$string['event:learningplanupdated'] = 'Learning plan updated';
$string['event:learningplandeleted'] = 'Learning plan deleted';
$string['event:learningplanarchived'] = 'Learning plan archived';
$string['event:courseadded'] = 'Course added to learning plan';
$string['event:courseremoved'] = 'Course removed from learning plan';
$string['event:coursesreordered'] = 'Learning plan courses reordered';
$string['event:userenrolled'] = 'User enrolled into learning plan';
$string['event:userunenrolled'] = 'User unenrolled from learning plan';
$string['event:usercompleted'] = 'User completed learning plan';
$string['event:progressrecalculated'] = 'Learning plan progress recalculated';
$string['event:enrolmentfailed'] = 'Learning plan enrolment failed';
$string['event:courseenrolmentcreated'] = 'Course enrolment created from learning plan';
$string['event:courseenrolmentremoved'] = 'Course enrolment removed from learning plan';
$string['event:cohortlinked'] = 'Cohort linked to learning plan';
$string['event:cohortunlinked'] = 'Cohort unlinked from learning plan';

$string['task:refreshprogress'] = 'Refresh learning plan progress consistency';
$string['task:reconcilecohorts'] = 'Reconcile learning plan cohort memberships';

$string['privacy:metadata:local_learningplans_plan'] = 'Stores learning plan definitions.';
$string['privacy:metadata:local_learningplans_plan:name'] = 'Learning plan name.';
$string['privacy:metadata:local_learningplans_plan:description'] = 'Learning plan description.';
$string['privacy:metadata:local_learningplans_plan:createdby'] = 'User who created the plan.';
$string['privacy:metadata:local_learningplans_plan:timecreated'] = 'When the plan was created.';
$string['privacy:metadata:local_learningplans_plan:timemodified'] = 'When the plan was last modified.';

$string['privacy:metadata:local_learningplans_mem'] = 'Stores user memberships in learning plans.';
$string['privacy:metadata:local_learningplans_mem:planid'] = 'Learning plan id.';
$string['privacy:metadata:local_learningplans_mem:userid'] = 'User id enrolled in the plan.';
$string['privacy:metadata:local_learningplans_mem:status'] = 'Membership status.';
$string['privacy:metadata:local_learningplans_mem:source'] = 'How the membership was created (manual or cohort).';
$string['privacy:metadata:local_learningplans_mem:enrolledby'] = 'User who enrolled the member.';
$string['privacy:metadata:local_learningplans_mem:timecreated'] = 'When membership was created.';
$string['privacy:metadata:local_learningplans_mem:timemodified'] = 'When membership was last modified.';
$string['privacy:metadata:local_learningplans_mem:timecompleted'] = 'When plan completion was recorded.';

$string['privacy:metadata:local_learningplans_enrl'] = 'Stores course enrolments created from learning plan memberships.';
$string['privacy:metadata:local_learningplans_enrl:membershipid'] = 'Learning plan membership id.';
$string['privacy:metadata:local_learningplans_enrl:planid'] = 'Learning plan id.';
$string['privacy:metadata:local_learningplans_enrl:courseid'] = 'Course id.';
$string['privacy:metadata:local_learningplans_enrl:userid'] = 'User id.';
$string['privacy:metadata:local_learningplans_enrl:enrolid'] = 'Course enrol instance id.';
$string['privacy:metadata:local_learningplans_enrl:userenrolmentid'] = 'User enrolment id.';
$string['privacy:metadata:local_learningplans_enrl:status'] = 'Linked enrolment status.';
$string['privacy:metadata:local_learningplans_enrl:timecreated'] = 'When link was created.';
$string['privacy:metadata:local_learningplans_enrl:timemodified'] = 'When link was last modified.';

$string['privacy:metadata:local_learningplans_prog'] = 'Stores cached progress snapshots.';
$string['privacy:metadata:local_learningplans_prog:planid'] = 'Learning plan id.';
$string['privacy:metadata:local_learningplans_prog:userid'] = 'User id.';
$string['privacy:metadata:local_learningplans_prog:totalcourses'] = 'Total courses in plan.';
$string['privacy:metadata:local_learningplans_prog:completedcourses'] = 'Completed courses in plan.';
$string['privacy:metadata:local_learningplans_prog:inprogresscourses'] = 'In-progress courses in plan.';
$string['privacy:metadata:local_learningplans_prog:notstartedcourses'] = 'Not-started courses in plan.';
$string['privacy:metadata:local_learningplans_prog:progresspercent'] = 'Completion percentage.';
$string['privacy:metadata:local_learningplans_prog:nextcourseid'] = 'Next required course id.';
$string['privacy:metadata:local_learningplans_prog:calculatedat'] = 'When the snapshot was calculated.';

$string['privacy:metadata:local_learningplans_coh'] = 'Stores which cohorts are linked to learning plans.';
$string['privacy:metadata:local_learningplans_coh:planid'] = 'Learning plan id.';
$string['privacy:metadata:local_learningplans_coh:cohortid'] = 'Linked cohort id.';
$string['privacy:metadata:local_learningplans_coh:createdby'] = 'User who linked the cohort.';
$string['privacy:metadata:local_learningplans_coh:timecreated'] = 'When the cohort link was created.';

// Student Lab (ADR-008).
$string['studentlab:title'] = 'Student Lab';
$string['studentlab:kicker'] = 'My learning';
$string['studentlab:lede'] = 'Your active courses, organised by your learning plan. Complete each course to unlock the next step of your pathway.';
$string['studentlab:activeplan'] = 'Active learning plan';
$string['studentlab:continue'] = 'Continue learning';
$string['studentlab:courses'] = 'Courses';
$string['studentlab:complete'] = 'Complete';
$string['studentlab:planprogress'] = 'Learning plan progress';
$string['studentlab:planmeta'] = '{$a->completed} of {$a->total} courses';
$string['studentlab:coursenumber'] = 'Course {$a}';
$string['studentlab:percentcomplete'] = '{$a}% complete';
$string['studentlab:lockedhint'] = 'Complete the previous course to unlock';
$string['studentlab:nocourses'] = 'This learning plan has no courses yet.';
$string['studentlab:status:done'] = 'Done';
$string['studentlab:status:active'] = 'Active';
$string['studentlab:status:upnext'] = 'Up next';
$string['studentlab:status:locked'] = 'Locked';
$string['studentlab:action:review'] = 'Review';
$string['studentlab:action:resume'] = 'Resume';
$string['studentlab:action:start'] = 'Start';
$string['privacy:metadata:preference:activeplan'] = 'The learning plan the user has chosen as their active one.';
$string['course:stagename'] = 'Stage name';
$string['course:stageupdated'] = 'Stage updated.';
$string['course:notinplan'] = 'This course is not part of the plan.';
$string['plan:view:savestage'] = 'Save stage';
$string['plan:view:nostage'] = 'No stage';
$string['plan:view:stagecount'] = '{$a} course(s)';
$string['plan:view:stageplaceholder'] = 'e.g. Stage 1 · Foundations';
$string['course:stagename_help'] = 'Optional. Consecutive courses sharing the same stage name are shown as one stage on the Student Lab page (e.g. "Stage 1 · Foundations"). Leave empty for an unnamed stage.';
$string['studentlab:stagedefault'] = 'Stage {$a}';
$string['studentlab:stageoftotal'] = 'Stage {$a->current} of {$a->total}';
$string['studentlab:modulecount'] = '{$a} modules';
