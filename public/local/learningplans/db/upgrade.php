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
 * Upgrade script for local_learningplans.
 *
 * @package    local_learningplans
 * @copyright  2026
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Upgrade hook.
 *
 * @param int $oldversion Previous version.
 * @return bool
 */
function xmldb_local_learningplans_upgrade(int $oldversion): bool {
    global $DB;

    $dbman = $DB->get_manager();

    if ($oldversion < 2026060100) {
        // Add the membership provenance field.
        $table = new xmldb_table('local_learningplans_mem');
        $field = new xmldb_field('source', XMLDB_TYPE_CHAR, '20', null, XMLDB_NOTNULL, null, 'manual', 'status');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Composite index used by cohort reconciliation lookups.
        $index = new xmldb_index('plan_status_source_ix', XMLDB_INDEX_NOTUNIQUE, ['planid', 'status', 'source']);
        if (!$dbman->index_exists($table, $index)) {
            $dbman->add_index($table, $index);
        }

        // Create the plan-to-cohort link table.
        $cohorttable = new xmldb_table('local_learningplans_coh');
        if (!$dbman->table_exists($cohorttable)) {
            $cohorttable->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
            $cohorttable->add_field('planid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
            $cohorttable->add_field('cohortid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
            $cohorttable->add_field('createdby', XMLDB_TYPE_INTEGER, '10', null, null, null, null);
            $cohorttable->add_field('timecreated', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');

            $cohorttable->add_key('primary', XMLDB_KEY_PRIMARY, ['id']);
            $cohorttable->add_key('planid_fk', XMLDB_KEY_FOREIGN, ['planid'], 'local_learningplans_plan', ['id']);
            $cohorttable->add_key('cohortid_fk', XMLDB_KEY_FOREIGN, ['cohortid'], 'cohort', ['id']);
            $cohorttable->add_key('createdby_fk', XMLDB_KEY_FOREIGN, ['createdby'], 'user', ['id']);

            $cohorttable->add_index('plan_cohort_uix', XMLDB_INDEX_UNIQUE, ['planid', 'cohortid']);

            $dbman->create_table($cohorttable);
        }

        upgrade_plugin_savepoint(true, 2026060100, 'local', 'learningplans');
    }

    if ($oldversion < 2026071201) {
        // Archetype defaults in db/access.php only apply to new installs.
        // Existing sites must grant the read capabilities to the
        // authenticated-user role explicitly, otherwise learners cannot open
        // their own Student Lab (the student role is assigned at course
        // context and does not apply at system context). Existing explicit
        // permissions are respected (no overwrite).
        $systemcontext = context_system::instance();
        $userroles = $DB->get_records('role', ['archetype' => 'user']);
        foreach ($userroles as $role) {
            foreach (['local/learningplans:view', 'local/learningplans:viewprogress'] as $capability) {
                assign_capability($capability, CAP_ALLOW, $role->id, $systemcontext->id, false);
            }
        }
        $systemcontext->mark_dirty();

        upgrade_plugin_savepoint(true, 2026071201, 'local', 'learningplans');
    }

    return true;
}

