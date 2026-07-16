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
$get = static function(string $name, string $default): string {
    $value = trim((string)get_config('local_sfsgame', $name));

    return $value !== '' ? $value : $default;
};
$enabled = static function(string $name, bool $default = true): bool {
    $value = get_config('local_sfsgame', $name);
    if ($value === false || $value === null || $value === '') {
        return $default;
    }

    return (int)$value === 1;
};
$pagetitle = $get('pagetitle', get_string('futurefood', 'local_sfsgame'));

$PAGE->set_context($context);
$PAGE->set_url(new moodle_url('/local/sfsgame/index.php'));
$PAGE->set_pagelayout('standard');
$PAGE->set_title(format_string($pagetitle));
$PAGE->set_heading(format_string($pagetitle));

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

// Completed plan courses feed the XP total. Counted over the DISTINCT set of
// courses across every plan the user belongs to — a course shared by two
// plans must only be counted once (P8), so we resolve real per-course
// completion rather than summing each plan's cached completed count.
$completed = 0;
if (class_exists('\local_learningplans\infrastructure\moodle\factory\learning_plan_service_factory')) {
    require_once($CFG->libdir . '/completionlib.php');
    $service = \local_learningplans\infrastructure\moodle\factory\learning_plan_service_factory::create();
    $seencourses = [];
    foreach ($service->get_user_memberships($userid) as $membership) {
        foreach ($service->get_plan_courses($membership->plan_id()) as $plancourse) {
            $cid = $plancourse->course_id();
            if (isset($seencourses[$cid])) {
                continue;
            }
            $seencourses[$cid] = true;
            $course = get_course($cid, false);
            if ($course && (new completion_info($course))->is_course_complete($userid)) {
                $completed++;
            }
        }
    }
}

$missionlink = static function(string $url): ?array {
    $url = trim($url);
    if ($url === '' || str_starts_with($url, '//')
            || (!str_starts_with($url, '/') && !preg_match('~^https?://~i', $url))) {
        return null;
    }

    try {
        $moodleurl = new moodle_url($url);
        return [
            'url' => $moodleurl->out(false),
            'external' => preg_match('~^https?://~i', $url) === 1 && !$moodleurl->is_local_url(),
        ];
    } catch (Throwable $exception) {
        return null;
    }
};
$missionvariants = ['aqua', 'amber', 'teal', 'green'];
$missions = [];
$missionxp = 0;
foreach (\local_sfsgame\missions::parse((string)get_config('local_sfsgame', 'missions')) as $i => $mission) {
    $reward = trim((string)$mission['reward']);
    $completion = \local_sfsgame\mission_completion::state_for_url((string)$mission['url'], $userid);
    $missioncompleted = !empty($completion['completed']);
    if ($missioncompleted && $mission['xp'] > 0) {
        $missionxp += $mission['xp'];
    }
    $completionlabel = '';
    if ($mission['xp'] > 0) {
        $completionlabel = get_string('missioncompletion:' . $completion['state'], 'local_sfsgame');
    }
    if ($reward === '' && $mission['xp'] > 0) {
        if ($missioncompleted) {
            $reward = get_string('missionrewardawarded', 'local_sfsgame', $mission['xp']);
        } else if ($completion['state'] === \local_sfsgame\mission_completion::STATE_INCOMPLETE) {
            $reward = get_string('missionrewardavailable', 'local_sfsgame', $mission['xp']);
        } else {
            $reward = get_string('missionrewardnottracked', 'local_sfsgame', $mission['xp']);
        }
    }
    $link = $missionlink($mission['url']);
    // Can the learner actually reach the target? (P6) Hide Start when a
    // mission points at a course/activity they cannot access.
    $canstart = $link !== null
        && \local_sfsgame\mission_completion::is_accessible((string)$mission['url'], $userid);
    $noaccess = $link !== null && !$canstart;

    // Thumb chip: when the mission is a real tracked activity, show the
    // learner's actual state (P5) so it can't contradict their progress;
    // otherwise fall back to the decorative admin-set badge label.
    if ($completion['state'] === \local_sfsgame\mission_completion::STATE_COMPLETED) {
        $chip = get_string('missionchip:completed', 'local_sfsgame');
        $chipstate = 'completed';
    } else if ($completion['state'] === \local_sfsgame\mission_completion::STATE_INCOMPLETE) {
        $chip = get_string('missionchip:incomplete', 'local_sfsgame');
        $chipstate = 'incomplete';
    } else {
        $chip = format_string($mission['badge']);
        $chipstate = 'label';
    }

    // Only advertise "+N XP" when the mission can actually award it — i.e. it
    // links to a completion-tracked activity (P4). A course/external link
    // never awards, so the promise is hidden; the reward note still explains.
    $trackable = in_array($completion['state'], [
        \local_sfsgame\mission_completion::STATE_INCOMPLETE,
        \local_sfsgame\mission_completion::STATE_COMPLETED,
    ], true);

    $missions[] = [
        'completed' => $missioncompleted,
        'chip' => $chip,
        'chipstate' => $chipstate,
        'badge' => format_string($mission['badge']),
        'title' => format_string($mission['title']),
        'text' => format_string($mission['text']),
        'duration' => format_string($mission['duration']),
        'xp' => $mission['xp'],
        'hasxp' => $mission['xp'] > 0,
        'showxp' => $mission['xp'] > 0 && $trackable,
        'url' => $link['url'] ?? null,
        'canstart' => $canstart,
        'noaccess' => $noaccess,
        'external' => $link['external'] ?? false,
        'reward' => format_string($reward),
        'hasrewardmeta' => $mission['xp'] > 0 || $reward !== '',
        'completionstate' => $completion['state'],
        'completionlabel' => $completionlabel,
        'tags' => array_map(static fn($t) => ['name' => format_string($t['name'])], $mission['tags']),
        'variant' => $missionvariants[$i % count($missionvariants)],
    ];
}

$firstmissionurl = null;
$showmissions = $enabled('showmissions');
if (!$showmissions) {
    $missions = [];
    $missionxp = 0;
} else {
    foreach ($missions as $mission) {
        if (!empty($mission['url'])) {
            $firstmissionurl = $mission['url'];
            break;
        }
    }
}

$xp = \local_sfsgame\domain\xp_policy::xp(count($earnedids), $completed, $missionxp);
$level = \local_sfsgame\domain\xp_policy::level($xp);
$decisionchoices = \local_sfsgame\decision::parse((string)get_config('local_sfsgame', 'decisionchoices'));

echo $OUTPUT->header();
echo $OUTPUT->render_from_template('local_sfsgame/futurefood_page', [
    'showpagehead' => $enabled('showpagehead'),
    'showhero' => $enabled('showhero'),
    'showachievements' => $enabled('showachievements'),
    'showmissions' => $showmissions,
    'kicker' => format_string($get('pagekicker',
        get_string('kicker', 'local_sfsgame')), true, ['escape' => false]),
    'title' => format_string($get('pagetitle',
        get_string('futurefood', 'local_sfsgame')), true, ['escape' => false]),
    'lede' => format_string($get('pagelede',
        get_string('lede', 'local_sfsgame')), true, ['escape' => false]),
    'herokicker' => format_string($get('herokicker',
        get_string('default_herokicker', 'local_sfsgame')), true, ['escape' => false]),
    'herotitle' => format_string($get('herotitle',
        get_string('default_herotitle', 'local_sfsgame')), true, ['escape' => false]),
    'herotext' => format_string($get('herotext',
        get_string('default_herotext', 'local_sfsgame')), true, ['escape' => false]),
    'starturl' => $firstmissionurl,
    'startdailylabel' => format_string($get('startdailylabel',
        get_string('startdailymission', 'local_sfsgame')), true, ['escape' => false]),
    'startmissionlabel' => format_string($get('startmissionlabel',
        get_string('startmission', 'local_sfsgame')), true, ['escape' => false]),
    'currentranklabel' => format_string($get('currentranklabel',
        get_string('currentrank', 'local_sfsgame')), true, ['escape' => false]),
    'totalxplabel' => format_string($get('totalxplabel',
        get_string('totalxp', 'local_sfsgame')), true, ['escape' => false]),
    'level' => $level,
    'xp' => $xp,
    'levelprogress' => \local_sfsgame\domain\xp_policy::level_progress($xp),
    'xptonext' => get_string('progresstolevel', 'local_sfsgame', [
        'next' => $level + 1,
        'current' => $xp % \local_sfsgame\domain\xp_policy::XP_PER_LEVEL,
        'target' => \local_sfsgame\domain\xp_policy::XP_PER_LEVEL,
    ]),
    'badgecount' => get_string('badgecount', 'local_sfsgame', count($earnedids)),
    'achievementstitle' => format_string($get('achievementstitle',
        get_string('achievements', 'local_sfsgame')), true, ['escape' => false]),
    'noachievements' => format_string($get('noachievements',
        get_string('noachievements', 'local_sfsgame')), true, ['escape' => false]),
    'achievements' => $achievements,
    'hasachievements' => $achievements !== [],
    'missionstitle' => format_string($get('missionstitle',
        get_string('missionstitle', 'local_sfsgame')), true, ['escape' => false]),
    'nomissions' => format_string($get('nomissions',
        get_string('nomissions', 'local_sfsgame')), true, ['escape' => false]),
    'missions' => $missions,
    'hasmissions' => $missions !== [],
    'showdecision' => $enabled('showdecision'),
    'decisionkicker' => format_string($get('decisionkicker',
        get_string('default_decisionkicker', 'local_sfsgame')), true, ['escape' => false]),
    'decisiontitle' => format_string($get('decisiontitle',
        get_string('default_decisiontitle', 'local_sfsgame')), true, ['escape' => false]),
    'decisionbody' => format_string($get('decisionbody',
        get_string('default_decisionbody', 'local_sfsgame')), true, ['escape' => false]),
    'decisionchoices' => $decisionchoices,
    'hasdecisionchoices' => $decisionchoices !== [],
    'decisionempty' => format_string($get('decisionempty',
        get_string('decisionempty', 'local_sfsgame')), true, ['escape' => false]),
    'decisionhint' => format_string($get('decisionhint',
        get_string('decisionhint', 'local_sfsgame')), true, ['escape' => false]),
    'decisionpill' => format_string($get('decisionpill',
        get_string('decisionpill', 'local_sfsgame')), true, ['escape' => false]),
]);
echo $OUTPUT->footer();
