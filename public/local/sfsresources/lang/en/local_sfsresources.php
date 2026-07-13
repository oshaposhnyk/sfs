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
 * English strings for local_sfsresources.
 *
 * @package    local_sfsresources
 * @copyright  2026 SecureFood School
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

declare(strict_types=1);

defined('MOODLE_INTERNAL') || die();

$string['pluginname'] = 'Resources & Standards';
$string['resources'] = 'Resources & Standards';
$string['kicker'] = 'Governance hub';
$string['lede'] = 'All protocols, contracts and reference documents in one place — searchable, role-tagged, and ready to share with school staff and suppliers.';
$string['library'] = 'Resource library';
$string['open'] = 'Open';
$string['nodocuments'] = 'No documents are published yet.';
$string['documents'] = 'Documents (JSON)';
$string['documents_desc'] = 'JSON list, e.g. [{"title": "HACCP Protocol", "sub": "Cold chain", "audience": "Director", "kind": "pdf", "updated": "2 days ago", "url": "https://…"}]. "kind" is pdf, doc, xls, zip or link. Leave empty to show only uploaded library files.';
$string['privacy:metadata'] = 'The Resources & Standards plugin only displays administrator-curated links.';
$string['tool_plans'] = 'Learning plans';
$string['tool_plans_desc'] = 'Create plans, order courses, enrol learners.';
$string['tool_cohorts'] = 'Cohorts';
$string['tool_cohorts_desc'] = 'Manage cohorts linked to learning plans.';
$string['tool_badges'] = 'Badges';
$string['tool_badges_desc'] = 'Configure the achievements shown in Future Food.';
$string['tool_courses'] = 'Courses';
$string['tool_courses_desc'] = 'Manage course structure and categories.';
$string['tool_count_active'] = 'Active';
$string['tool_count_configured'] = 'Configured';
$string['stats'] = 'KPI cards (JSON)';
$string['stats_desc'] = 'JSON list, e.g. [{"label": "Staff trained", "value": "145", "suffix": "", "percent": 72, "sub": "L4C courses completed", "variant": "teal"}]. "variant" is teal, amber or deep. Leave empty to hide KPI cards.';
$string['documentfiles'] = 'Library files';
$string['documentfiles_desc'] = 'Upload the actual documents here. They appear at the top of the library; downloads are available to logged-in users only.';
$string['settings:pageheading'] = 'Resources page';
$string['settings:pageheading_desc'] = 'No-code controls for the learner-facing Resources & Standards page.';
$string['settings:showresourcesheader'] = 'Show page header';
$string['settings:showresourcesheader_desc'] = 'Display the kicker, title and intro copy above the resources page.';
$string['settings:showresourcesstats'] = 'Show KPI cards';
$string['settings:showresourcesstats_desc'] = 'Display administrator-curated KPI cards when KPI JSON is configured.';
$string['settings:showresourcestools'] = 'Show management tools';
$string['settings:showresourcestools_desc'] = 'Display staff-only shortcuts to learning plans, cohorts, badges and courses.';
$string['settings:showresourcesfilters'] = 'Show audience filters';
$string['settings:showresourcesfilters_desc'] = 'Display filter pills above the document library.';
$string['settings:showresourceslibrary'] = 'Show resource library';
$string['settings:showresourceslibrary_desc'] = 'Display the document library card.';
$string['settings:resourceskicker'] = 'Resources kicker';
$string['settings:resourcestitle'] = 'Resources title';
$string['settings:resourceslede'] = 'Resources intro text';
$string['settings:resourceslibrarytitle'] = 'Library title';
$string['settings:resourcesempty'] = 'No-documents message';
$string['settings:fallback_desc'] = 'Leave empty to use the default Resources copy.';
