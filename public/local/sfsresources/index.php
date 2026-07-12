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
 * Resources & Standards library page (v1: curated links, no file storage).
 *
 * @package    local_sfsresources
 * @copyright  2026 SecureFood School
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

declare(strict_types=1);

require(__DIR__ . '/../../config.php');

require_login(null, false);
if (isguestuser()) {
    throw new moodle_exception('noguest');
}

$context = context_system::instance();
$PAGE->set_context($context);
$PAGE->set_url(new moodle_url('/local/sfsresources/index.php'));
$PAGE->set_pagelayout('standard');
$PAGE->set_title(get_string('resources', 'local_sfsresources'));
$PAGE->set_heading(get_string('resources', 'local_sfsresources'));

$kindfilter = optional_param('kind', '', PARAM_ALPHA);

$alldocs = \local_sfsresources\documents::parse(
    (string)get_config('local_sfsresources', 'documents')
);

// Type filter pills (server-side — works without JS).
$kinds = array_values(array_unique(array_column($alldocs, 'kind')));
$filters = [[
    'label' => get_string('all'),
    'url' => (new moodle_url('/local/sfsresources/index.php'))->out(false),
    'active' => $kindfilter === '',
]];
foreach ($kinds as $kind) {
    $filters[] = [
        'label' => strtoupper($kind === 'link' ? 'www' : $kind),
        'url' => (new moodle_url('/local/sfsresources/index.php', ['kind' => $kind]))->out(false),
        'active' => $kindfilter === $kind,
    ];
}

$documents = [];
foreach ($alldocs as $doc) {
    if ($kindfilter !== '' && $doc['kind'] !== $kindfilter) {
        continue;
    }
    $documents[] = [
        'title' => format_string($doc['title'], true, ['escape' => false]),
        'sub' => format_string($doc['sub'], true, ['escape' => false]),
        'audience' => format_string($doc['audience'], true, ['escape' => false]),
        'kind' => $doc['kind'],
        'kindlabel' => strtoupper($doc['kind'] === 'link' ? 'www' : $doc['kind']),
        'updated' => format_string($doc['updated'], true, ['escape' => false]),
        'url' => $doc['url'] !== '' ? (new moodle_url($doc['url']))->out(false) : null,
    ];
}

// Management tools — only for staff who can manage learning plans.
$tools = [];
if (has_capability('local/learningplans:manage', $context)) {
    $tools = [
        ['title' => get_string('tool_plans', 'local_sfsresources'),
            'desc' => get_string('tool_plans_desc', 'local_sfsresources'),
            'url' => (new moodle_url('/local/learningplans/index.php'))->out(false)],
        ['title' => get_string('tool_cohorts', 'local_sfsresources'),
            'desc' => get_string('tool_cohorts_desc', 'local_sfsresources'),
            'url' => (new moodle_url('/cohort/index.php'))->out(false)],
        ['title' => get_string('tool_badges', 'local_sfsresources'),
            'desc' => get_string('tool_badges_desc', 'local_sfsresources'),
            'url' => (new moodle_url('/badges/index.php', ['type' => 1]))->out(false)],
        ['title' => get_string('tool_courses', 'local_sfsresources'),
            'desc' => get_string('tool_courses_desc', 'local_sfsresources'),
            'url' => (new moodle_url('/course/management.php'))->out(false)],
    ];
}

echo $OUTPUT->header();
echo $OUTPUT->render_from_template('local_sfsresources/resources_page', [
    'kicker' => get_string('kicker', 'local_sfsresources'),
    'title' => get_string('resources', 'local_sfsresources'),
    'lede' => get_string('lede', 'local_sfsresources'),
    'library' => get_string('library', 'local_sfsresources'),
    'documents' => $documents,
    'hasdocuments' => $documents !== [],
    'filters' => $filters,
    'tools' => $tools,
    'hastools' => $tools !== [],
]);
echo $OUTPUT->footer();
