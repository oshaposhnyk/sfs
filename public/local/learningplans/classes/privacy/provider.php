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

namespace local_learningplans\privacy;

use core_privacy\local\metadata\collection;
use core_privacy\local\request\approved_contextlist;
use core_privacy\local\request\approved_userlist;
use core_privacy\local\request\contextlist;
use core_privacy\local\request\core_userlist_provider;
use core_privacy\local\request\plugin\provider as plugin_provider;
use core_privacy\local\request\userlist;
use core_privacy\local\request\writer;

defined('MOODLE_INTERNAL') || die();

/**
 * Privacy provider for local_learningplans.
 *
 * @package    local_learningplans
 * @copyright  2026
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class provider implements
    \core_privacy\local\metadata\provider,
    \core_privacy\local\request\user_preference_provider,
    core_userlist_provider,
    plugin_provider {

    /**
     * Export the plugin's user preferences.
     *
     * @param int $userid The user id.
     * @return void
     */
    public static function export_user_preferences(int $userid): void {
        $activeplan = get_user_preferences('local_learningplans_activeplan', null, $userid);
        if ($activeplan !== null) {
            writer::export_user_preference(
                'local_learningplans',
                'local_learningplans_activeplan',
                (string)$activeplan,
                get_string('privacy:metadata:preference:activeplan', 'local_learningplans')
            );
        }
    }

    /**
     * @inheritDoc
     */
    public static function get_metadata(collection $collection): collection {
        $collection->add_user_preference(
            'local_learningplans_activeplan',
            'privacy:metadata:preference:activeplan'
        );
        $collection->add_database_table('local_learningplans_plan', [
            'name' => 'privacy:metadata:local_learningplans_plan:name',
            'description' => 'privacy:metadata:local_learningplans_plan:description',
            'createdby' => 'privacy:metadata:local_learningplans_plan:createdby',
            'timecreated' => 'privacy:metadata:local_learningplans_plan:timecreated',
            'timemodified' => 'privacy:metadata:local_learningplans_plan:timemodified',
        ], 'privacy:metadata:local_learningplans_plan');

        $collection->add_database_table('local_learningplans_mem', [
            'planid' => 'privacy:metadata:local_learningplans_mem:planid',
            'userid' => 'privacy:metadata:local_learningplans_mem:userid',
            'status' => 'privacy:metadata:local_learningplans_mem:status',
            'source' => 'privacy:metadata:local_learningplans_mem:source',
            'enrolledby' => 'privacy:metadata:local_learningplans_mem:enrolledby',
            'timecreated' => 'privacy:metadata:local_learningplans_mem:timecreated',
            'timemodified' => 'privacy:metadata:local_learningplans_mem:timemodified',
            'timecompleted' => 'privacy:metadata:local_learningplans_mem:timecompleted',
        ], 'privacy:metadata:local_learningplans_mem');

        $collection->add_database_table('local_learningplans_enrl', [
            'membershipid' => 'privacy:metadata:local_learningplans_enrl:membershipid',
            'planid' => 'privacy:metadata:local_learningplans_enrl:planid',
            'courseid' => 'privacy:metadata:local_learningplans_enrl:courseid',
            'userid' => 'privacy:metadata:local_learningplans_enrl:userid',
            'enrolid' => 'privacy:metadata:local_learningplans_enrl:enrolid',
            'userenrolmentid' => 'privacy:metadata:local_learningplans_enrl:userenrolmentid',
            'status' => 'privacy:metadata:local_learningplans_enrl:status',
            'timecreated' => 'privacy:metadata:local_learningplans_enrl:timecreated',
            'timemodified' => 'privacy:metadata:local_learningplans_enrl:timemodified',
        ], 'privacy:metadata:local_learningplans_enrl');

        $collection->add_database_table('local_learningplans_prog', [
            'planid' => 'privacy:metadata:local_learningplans_prog:planid',
            'userid' => 'privacy:metadata:local_learningplans_prog:userid',
            'totalcourses' => 'privacy:metadata:local_learningplans_prog:totalcourses',
            'completedcourses' => 'privacy:metadata:local_learningplans_prog:completedcourses',
            'inprogresscourses' => 'privacy:metadata:local_learningplans_prog:inprogresscourses',
            'notstartedcourses' => 'privacy:metadata:local_learningplans_prog:notstartedcourses',
            'progresspercent' => 'privacy:metadata:local_learningplans_prog:progresspercent',
            'nextcourseid' => 'privacy:metadata:local_learningplans_prog:nextcourseid',
            'calculatedat' => 'privacy:metadata:local_learningplans_prog:calculatedat',
        ], 'privacy:metadata:local_learningplans_prog');

        $collection->add_database_table('local_learningplans_coh', [
            'planid' => 'privacy:metadata:local_learningplans_coh:planid',
            'cohortid' => 'privacy:metadata:local_learningplans_coh:cohortid',
            'createdby' => 'privacy:metadata:local_learningplans_coh:createdby',
            'timecreated' => 'privacy:metadata:local_learningplans_coh:timecreated',
        ], 'privacy:metadata:local_learningplans_coh');

        return $collection;
    }

    /**
     * @inheritDoc
     */
    public static function get_contexts_for_userid(int $userid): contextlist {
        global $DB;
        $contextlist = new contextlist();
        if ($DB->record_exists_select(
            'local_learningplans_mem',
            'userid = :userid OR enrolledby = :userid',
            ['userid' => $userid]
        ) || $DB->record_exists('local_learningplans_enrl', ['userid' => $userid]) ||
            $DB->record_exists('local_learningplans_prog', ['userid' => $userid]) ||
            $DB->record_exists('local_learningplans_plan', ['createdby' => $userid]) ||
            $DB->record_exists('local_learningplans_coh', ['createdby' => $userid])) {
            $contextlist->add_system_context();
        }
        return $contextlist;
    }

    /**
     * @inheritDoc
     */
    public static function get_users_in_context(userlist $userlist) {
        $context = $userlist->get_context();
        if ((int)$context->id !== SYSCONTEXTID) {
            return;
        }

        $sql = "SELECT userid
                  FROM {local_learningplans_mem}
                UNION
                SELECT enrolledby AS userid
                  FROM {local_learningplans_mem}
                 WHERE enrolledby IS NOT NULL
                UNION
                SELECT userid
                  FROM {local_learningplans_enrl}
                UNION
                SELECT userid
                  FROM {local_learningplans_prog}
                UNION
                SELECT createdby AS userid
                  FROM {local_learningplans_plan}
                 WHERE createdby IS NOT NULL
                UNION
                SELECT createdby AS userid
                  FROM {local_learningplans_coh}
                 WHERE createdby IS NOT NULL";
        $userlist->add_from_sql('userid', $sql, []);
    }

    /**
     * @inheritDoc
     */
    public static function export_user_data(approved_contextlist $contextlist) {
        global $DB;
        if (!$contextlist->count()) {
            return;
        }

        $userid = (int)$contextlist->get_user()->id;
        foreach ($contextlist->get_contexts() as $context) {
            if ((int)$context->id !== SYSCONTEXTID) {
                continue;
            }

            $memberships = $DB->get_records('local_learningplans_mem', ['userid' => $userid], 'id ASC');
            $progress = $DB->get_records('local_learningplans_prog', ['userid' => $userid], 'id ASC');

            $membershipids = array_map(static function($record) {
                return (int)$record->id;
            }, array_values($memberships));
            $links = [];
            if ($membershipids !== []) {
                [$insql, $params] = $DB->get_in_or_equal($membershipids, SQL_PARAMS_NAMED);
                $links = $DB->get_records_select('local_learningplans_enrl', "membershipid $insql", $params, 'id ASC');
            }

            $data = (object)[
                'memberships' => array_values(array_map(static function($record): array {
                    return [
                        'planid' => (int)$record->planid,
                        'status' => (int)$record->status,
                        'source' => (string)($record->source ?? 'manual'),
                        'enrolledby' => (int)($record->enrolledby ?? 0),
                        'timecreated' => \core_privacy\local\request\transform::datetime((int)$record->timecreated),
                        'timemodified' => \core_privacy\local\request\transform::datetime((int)$record->timemodified),
                        'timecompleted' => empty($record->timecompleted)
                            ? null
                            : \core_privacy\local\request\transform::datetime((int)$record->timecompleted),
                    ];
                }, $memberships)),
                'courseenrolments' => array_values(array_map(static function($record): array {
                    return [
                        'membershipid' => (int)$record->membershipid,
                        'planid' => (int)$record->planid,
                        'courseid' => (int)$record->courseid,
                        'status' => (int)$record->status,
                        'enrolid' => (int)($record->enrolid ?? 0),
                        'userenrolmentid' => (int)($record->userenrolmentid ?? 0),
                        'timecreated' => \core_privacy\local\request\transform::datetime((int)$record->timecreated),
                        'timemodified' => \core_privacy\local\request\transform::datetime((int)$record->timemodified),
                    ];
                }, $links)),
                'progress' => array_values(array_map(static function($record): array {
                    return [
                        'planid' => (int)$record->planid,
                        'totalcourses' => (int)$record->totalcourses,
                        'completedcourses' => (int)$record->completedcourses,
                        'inprogresscourses' => (int)$record->inprogresscourses,
                        'notstartedcourses' => (int)$record->notstartedcourses,
                        'progresspercent' => (float)$record->progresspercent,
                        'nextcourseid' => (int)($record->nextcourseid ?? 0),
                        'calculatedat' => \core_privacy\local\request\transform::datetime((int)$record->calculatedat),
                    ];
                }, $progress)),
            ];

            writer::with_context($context)->export_data([get_string('pluginname', 'local_learningplans')], $data);
        }
    }

    /**
     * @inheritDoc
     */
    public static function delete_data_for_all_users_in_context(\context $context) {
        global $DB;
        if ((int)$context->id !== SYSCONTEXTID) {
            return;
        }

        $DB->delete_records('local_learningplans_enrl', []);
        $DB->delete_records('local_learningplans_prog', []);
        $DB->delete_records('local_learningplans_mem', []);
        $DB->execute("UPDATE {local_learningplans_plan} SET createdby = NULL");
        $DB->execute("UPDATE {local_learningplans_coh} SET createdby = NULL");
    }

    /**
     * @inheritDoc
     */
    public static function delete_data_for_user(approved_contextlist $contextlist) {
        global $DB;
        if (!$contextlist->count()) {
            return;
        }

        $hascontext = false;
        foreach ($contextlist->get_contexts() as $context) {
            if ((int)$context->id === SYSCONTEXTID) {
                $hascontext = true;
                break;
            }
        }
        if (!$hascontext) {
            return;
        }

        $userid = (int)$contextlist->get_user()->id;
        $membershipids = $DB->get_fieldset_select('local_learningplans_mem', 'id', 'userid = :userid', ['userid' => $userid]);
        if ($membershipids !== []) {
            [$insql, $params] = $DB->get_in_or_equal($membershipids, SQL_PARAMS_NAMED);
            $DB->delete_records_select('local_learningplans_enrl', "membershipid $insql", $params);
        }

        $DB->delete_records('local_learningplans_mem', ['userid' => $userid]);
        $DB->delete_records('local_learningplans_prog', ['userid' => $userid]);
        $DB->delete_records('local_learningplans_enrl', ['userid' => $userid]);
        $DB->execute("UPDATE {local_learningplans_mem} SET enrolledby = NULL WHERE enrolledby = :userid", ['userid' => $userid]);
        $DB->execute("UPDATE {local_learningplans_plan} SET createdby = NULL WHERE createdby = :userid", ['userid' => $userid]);
        $DB->execute("UPDATE {local_learningplans_coh} SET createdby = NULL WHERE createdby = :userid", ['userid' => $userid]);
    }

    /**
     * @inheritDoc
     */
    public static function delete_data_for_users(approved_userlist $userlist) {
        global $DB;
        if ((int)$userlist->get_context()->id !== SYSCONTEXTID) {
            return;
        }

        $userids = $userlist->get_userids();
        if ($userids === []) {
            return;
        }

        [$insql, $params] = $DB->get_in_or_equal($userids, SQL_PARAMS_NAMED);
        $membershipids = $DB->get_fieldset_select('local_learningplans_mem', 'id', "userid $insql", $params);
        if ($membershipids !== []) {
            [$msql, $mparams] = $DB->get_in_or_equal($membershipids, SQL_PARAMS_NAMED);
            $DB->delete_records_select('local_learningplans_enrl', "membershipid $msql", $mparams);
        }

        $DB->delete_records_select('local_learningplans_mem', "userid $insql", $params);
        $DB->delete_records_select('local_learningplans_enrl', "userid $insql", $params);
        $DB->delete_records_select('local_learningplans_prog', "userid $insql", $params);
        $DB->execute("UPDATE {local_learningplans_coh} SET createdby = NULL WHERE createdby $insql", $params);
    }
}
