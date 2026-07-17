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
$get = static function(string $name, string $default): string {
    $value = trim((string)get_config('local_sfsresources', $name));

    return $value !== '' ? $value : $default;
};
$enabled = static function(string $name, bool $default = true): bool {
    $value = get_config('local_sfsresources', $name);
    if ($value === false || $value === null || $value === '') {
        return $default;
    }

    return (int)$value === 1;
};
$pagetitle = $get('resourcestitle', get_string('resources', 'local_sfsresources'));

$PAGE->set_context($context);
$PAGE->set_url(new moodle_url('/local/sfsresources/index.php'));
$PAGE->set_pagelayout('standard');
$PAGE->set_title(format_string($pagetitle));
$PAGE->set_heading(format_string($pagetitle));

$kindfilter = optional_param('kind', '', PARAM_ALPHA);
$audfilter = strtolower(optional_param('aud', '', PARAM_ALPHANUMEXT));

$alldocs = \local_sfsresources\documents::parse(
    (string)get_config('local_sfsresources', 'documents')
);

// Audience filter pills (server-side — works without JS), as designed.
$audiences = [];
foreach ($alldocs as $doc) {
    if (trim($doc['audience']) !== '') {
        $audiences[strtolower($doc['audience'])] = $doc['audience'];
    }
}
$filters = [[
    // Own string: core get_string('all') renders "Вибрати все" (Select all)
    // in uk, which is wrong for a "show all" filter pill.
    'label' => get_string('filter:all', 'local_sfsresources'),
    'url' => (new moodle_url('/local/sfsresources/index.php'))->out(false),
    'active' => $audfilter === '',
]];
foreach ($audiences as $audkey => $audlabel) {
    $filters[] = [
        'label' => format_string($audlabel, true, ['escape' => false]),
        'url' => (new moodle_url('/local/sfsresources/index.php', ['aud' => $audkey]))->out(false),
        'active' => $audfilter === $audkey,
    ];
}

$documents = [];

// Uploaded library files (Phase 6.4): newest first, served via pluginfile
// with a login re-check.
$storedfiles = get_file_storage()->get_area_files(
    $context->id, 'local_sfsresources', 'documents', 0, 'timemodified DESC, id DESC', false
);
foreach ($storedfiles as $file) {
    if ($audfilter !== '') {
        // Uploaded files carry no audience tag: they only appear in the All view.
        continue;
    }
    $kind = \local_sfsresources\documents::kind_from_filename($file->get_filename());
    if ($kindfilter !== '' && $kind !== $kindfilter) {
        continue;
    }
    $documents[] = [
        'title' => format_string($file->get_filename(), true, ['escape' => false]),
        'sub' => display_size($file->get_filesize()),
        'audience' => '',
        'kind' => $kind,
        'kindlabel' => strtoupper($kind === 'link' ? 'file' : $kind),
        'updated' => userdate((int)$file->get_timemodified(), get_string('strftimedate', 'langconfig')),
        'url' => moodle_url::make_pluginfile_url(
            $context->id, 'local_sfsresources', 'documents', 0,
            $file->get_filepath(), $file->get_filename(), true
        )->out(false),
    ];
}

foreach ($alldocs as $doc) {
    if ($kindfilter !== '' && $doc['kind'] !== $kindfilter) {
        continue;
    }
    if ($audfilter !== '' && strtolower($doc['audience']) !== $audfilter) {
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

// KPI stat cards are administrator-curated. Prototype metrics are mock data
// and must never appear as live production facts when this setting is empty.
$statitems = $enabled('showresourcesstats') ? json_decode((string)get_config('local_sfsresources', 'stats'), true) : [];
if (!is_array($statitems) || $statitems === []) {
    $statitems = [];
}
$stats = [];
foreach ($statitems as $item) {
    if (!is_array($item) || trim((string)($item['label'] ?? '')) === '') {
        continue;
    }
    $stats[] = [
        'label' => format_string((string)$item['label'], true, ['escape' => false]),
        'value' => format_string((string)($item['value'] ?? ''), true, ['escape' => false]),
        'suffix' => format_string((string)($item['suffix'] ?? ''), true, ['escape' => false]),
        'percent' => max(0, min(100, (int)($item['percent'] ?? 0))),
        'sub' => format_string((string)($item['sub'] ?? ''), true, ['escape' => false]),
        'variant' => in_array($item['variant'] ?? '', ['teal', 'amber', 'deep'], true)
            ? $item['variant'] : 'teal',
    ];
}

// Management tools — only for staff who can manage learning plans.
$tools = [];
if ($enabled('showresourcestools') && has_capability('local/learningplans:manage', $context)) {
    $tools = [
        ['key' => 'plans', 'title' => get_string('tool_plans', 'local_sfsresources'),
            'desc' => get_string('tool_plans_desc', 'local_sfsresources'),
            'count' => $DB->count_records('local_learningplans_plan'),
            'countlabel' => get_string('tool_count_active', 'local_sfsresources'),
            'url' => (new moodle_url('/local/learningplans/index.php'))->out(false)],
        ['key' => 'cohorts', 'title' => get_string('tool_cohorts', 'local_sfsresources'),
            'desc' => get_string('tool_cohorts_desc', 'local_sfsresources'),
            'count' => $DB->count_records('cohort'),
            'countlabel' => get_string('tool_count_active', 'local_sfsresources'),
            'url' => (new moodle_url('/cohort/index.php'))->out(false)],
        ['key' => 'badges', 'title' => get_string('tool_badges', 'local_sfsresources'),
            'desc' => get_string('tool_badges_desc', 'local_sfsresources'),
            'count' => $DB->count_records('badge'),
            'countlabel' => get_string('tool_count_configured', 'local_sfsresources'),
            'url' => (new moodle_url('/badges/index.php', ['type' => 1]))->out(false)],
        ['key' => 'courses', 'title' => get_string('tool_courses', 'local_sfsresources'),
            'desc' => get_string('tool_courses_desc', 'local_sfsresources'),
            'count' => max(0, $DB->count_records('course') - 1),
            'countlabel' => get_string('tool_count_active', 'local_sfsresources'),
            'url' => (new moodle_url('/course/management.php'))->out(false)],
    ];
}

echo $OUTPUT->header();
echo $OUTPUT->render_from_template('local_sfsresources/resources_page', [
    'showheader' => $enabled('showresourcesheader'),
    'showfilters' => $enabled('showresourcesfilters'),
    'showlibrary' => $enabled('showresourceslibrary'),
    'kicker' => format_string($get('resourceskicker',
        get_string('kicker', 'local_sfsresources')), true, ['escape' => false]),
    'title' => format_string($get('resourcestitle',
        get_string('resources', 'local_sfsresources')), true, ['escape' => false]),
    'lede' => format_string($get('resourceslede',
        get_string('lede', 'local_sfsresources')), true, ['escape' => false]),
    'library' => format_string($get('resourceslibrarytitle',
        get_string('library', 'local_sfsresources')), true, ['escape' => false]),
    'nodocuments' => format_string($get('resourcesempty',
        get_string('nodocuments', 'local_sfsresources')), true, ['escape' => false]),
    'documents' => $documents,
    'hasdocuments' => $documents !== [],
    'filters' => $filters,
    'stats' => $stats,
    'hasstats' => $stats !== [],
    'tools' => $tools,
    'hastools' => $tools !== [],
]);
echo $OUTPUT->footer();
