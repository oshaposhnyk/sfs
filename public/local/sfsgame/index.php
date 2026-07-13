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
 * Future Food page: badges as achievements, derived XP/level (ADR-009).
 *
 * @package    local_sfsgame
 * @copyright  2026 SecureFood School
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

declare(strict_types=1);

require(__DIR__ . '/../../config.php');
require_once($CFG->libdir . '/badgeslib.php');

require_login(null, false);
if (isguestuser()) {
    throw new moodle_exception('noguest');
}

$userid = (int)$USER->id;
$context = context_system::instance();
$PAGE->set_context($context);
$PAGE->set_url(new moodle_url('/local/sfsgame/index.php'));
$PAGE->set_pagelayout('standard');
$PAGE->set_title(get_string('futurefood', 'local_sfsgame'));
$PAGE->set_heading(get_string('futurefood', 'local_sfsgame'));

// Earned + available site badges.
$earned = badges_get_user_badges($userid);
$earnedids = [];
$achievements = [];
$badgeimage = static function(int $badgeid) use ($context): string {
    return moodle_url::make_pluginfile_url(
        $context->id, 'badges', 'badgeimage', $badgeid, '/', 'f1', false
    )->out(false);
};
$criteriapreview = static function(\core_badges\badge $badge): string {
    $criteria = $badge->criteria;
    unset($criteria[BADGE_CRITERIA_TYPE_OVERALL]);
    if ($criteria === []) {
        return '';
    }

    $aggregation = $badge->get_aggregation_methods();
    $context = $badge->get_context();
    $items = [];
    foreach ($criteria as $type => $criterion) {
        if (empty($criterion->params)) {
            continue;
        }

        $description = count($criterion->params) === 1
            ? get_string('criteria_descr_single_' . $type, 'badges')
            : get_string('criteria_descr_' . $type, 'badges',
                core_text::strtoupper($aggregation[$badge->get_aggregation_method($type)]));
        $html = format_text($description . ' ' . $criterion->get_details(true), FORMAT_HTML, [
            'context' => $context,
            'filter' => true,
        ]);
        $text = html_entity_decode(strip_tags($html), ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $text = preg_replace('/\s+/', ' ', $text);
        if (is_string($text) && trim($text) !== '') {
            $items[] = trim($text);
        }
    }

    return implode(' ', $items);
};
foreach ($earned as $badge) {
    $earnedids[(int)$badge->id] = true;
    $achievements[] = [
        'name' => format_string($badge->name),
        'earned' => true,
        'imageurl' => $badgeimage((int)$badge->id),
        'xplabel' => '+' . \local_sfsgame\domain\xp_policy::XP_PER_BADGE . ' XP',
    ];
}
foreach (badges_get_badges(BADGE_TYPE_SITE) as $badge) {
    if (!isset($earnedids[(int)$badge->id]) && (int)$badge->status === BADGE_STATUS_ACTIVE) {
        $lockedbadge = new \core_badges\badge((int)$badge->id);
        $achievements[] = [
            'name' => format_string($badge->name),
            'earned' => false,
            'imageurl' => $badgeimage((int)$badge->id),
            'criteria' => $criteriapreview($lockedbadge),
        ];
    }
}

// Completed plan courses feed the XP total — summed across every plan the
// user belongs to, via the MUC-cached progress records (no per-course
// completion queries; Phase 6.5).
$completed = 0;
if (class_exists('\local_learningplans\infrastructure\moodle\factory\learning_plan_service_factory')) {
    $service = \local_learningplans\infrastructure\moodle\factory\learning_plan_service_factory::create();
    foreach ($service->get_user_memberships($userid) as $membership) {
        $completed += $service->get_user_progress($membership->plan_id(), $userid)->completed_courses();
    }
}

$xp = \local_sfsgame\domain\xp_policy::xp(count($earnedids), $completed);

$missionvariants = ['aqua', 'amber', 'teal', 'green'];
$missions = [];
foreach (\local_sfsgame\missions::parse((string)get_config('local_sfsgame', 'missions')) as $i => $mission) {
    $missions[] = [
        'badge' => format_string($mission['badge']),
        'title' => format_string($mission['title']),
        'text' => format_string($mission['text']),
        'duration' => format_string($mission['duration']),
        'xp' => $mission['xp'],
        'url' => $mission['url'] !== '' ? (new moodle_url($mission['url']))->out(false) : null,
        'tags' => array_map(static fn($t) => ['name' => format_string($t['name'])], $mission['tags']),
        'variant' => $missionvariants[$i % count($missionvariants)],
    ];
}

$get = static function(string $name, string $default): string {
    $value = trim((string)get_config('local_sfsgame', $name));
    return $value !== '' ? $value : $default;
};
$level = \local_sfsgame\domain\xp_policy::level($xp);
$decisionchoices = \local_sfsgame\decision::parse((string)get_config('local_sfsgame', 'decisionchoices'));
$firstmissionurl = null;
foreach ($missions as $mission) {
    if (!empty($mission['url'])) {
        $firstmissionurl = $mission['url'];
        break;
    }
}

echo $OUTPUT->header();
echo $OUTPUT->render_from_template('local_sfsgame/futurefood_page', [
    'kicker' => get_string('kicker', 'local_sfsgame'),
    'title' => get_string('futurefood', 'local_sfsgame'),
    'lede' => get_string('lede', 'local_sfsgame'),
    'herokicker' => format_string($get('herokicker',
        get_string('default_herokicker', 'local_sfsgame')), true, ['escape' => false]),
    'herotitle' => format_string($get('herotitle',
        get_string('default_herotitle', 'local_sfsgame')), true, ['escape' => false]),
    'herotext' => format_string($get('herotext',
        get_string('default_herotext', 'local_sfsgame')), true, ['escape' => false]),
    'starturl' => $firstmissionurl,
    'level' => $level,
    'xp' => $xp,
    'levelprogress' => \local_sfsgame\domain\xp_policy::level_progress($xp),
    'xptonext' => get_string('progresstolevel', 'local_sfsgame', [
        'next' => $level + 1,
        'current' => $xp % \local_sfsgame\domain\xp_policy::XP_PER_LEVEL,
        'target' => \local_sfsgame\domain\xp_policy::XP_PER_LEVEL,
    ]),
    'badgecount' => get_string('badgecount', 'local_sfsgame', count($earnedids)),
    'achievements' => $achievements,
    'hasachievements' => $achievements !== [],
    'missions' => $missions,
    'hasmissions' => $missions !== [],
    'showdecision' => (int)(get_config('local_sfsgame', 'showdecision') ?? 1) === 1,
    'decisionkicker' => format_string($get('decisionkicker',
        get_string('default_decisionkicker', 'local_sfsgame')), true, ['escape' => false]),
    'decisiontitle' => format_string($get('decisiontitle',
        get_string('default_decisiontitle', 'local_sfsgame')), true, ['escape' => false]),
    'decisionbody' => format_string($get('decisionbody',
        get_string('default_decisionbody', 'local_sfsgame')), true, ['escape' => false]),
    'decisionchoices' => $decisionchoices,
    'hasdecisionchoices' => $decisionchoices !== [],
    'decisionempty' => get_string('decisionempty', 'local_sfsgame'),
    'decisionhint' => get_string('decisionhint', 'local_sfsgame'),
]);
echo $OUTPUT->footer();
