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
foreach ($earned as $badge) {
    $earnedids[(int)$badge->id] = true;
    $achievements[] = [
        'name' => format_string($badge->name),
        'earned' => true,
    ];
}
foreach (badges_get_badges(BADGE_TYPE_SITE) as $badge) {
    if (!isset($earnedids[(int)$badge->id]) && (int)$badge->status === BADGE_STATUS_ACTIVE) {
        $achievements[] = [
            'name' => format_string($badge->name),
            'earned' => false,
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

echo $OUTPUT->header();
echo $OUTPUT->render_from_template('local_sfsgame/futurefood_page', [
    'kicker' => get_string('kicker', 'local_sfsgame'),
    'title' => get_string('futurefood', 'local_sfsgame'),
    'lede' => get_string('lede', 'local_sfsgame'),
    'level' => \local_sfsgame\domain\xp_policy::level($xp),
    'xp' => $xp,
    'levelprogress' => \local_sfsgame\domain\xp_policy::level_progress($xp),
    'xptonext' => get_string('xptonext', 'local_sfsgame',
        \local_sfsgame\domain\xp_policy::to_next_level($xp)),
    'achievements' => $achievements,
    'hasachievements' => $achievements !== [],
    'missions' => $missions,
]);
echo $OUTPUT->footer();
