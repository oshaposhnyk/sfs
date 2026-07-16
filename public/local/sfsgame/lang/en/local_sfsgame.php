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
 * English strings for local_sfsgame.
 *
 * @package    local_sfsgame
 * @copyright  2026 SecureFood School
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

declare(strict_types=1);

defined('MOODLE_INTERNAL') || die();

$string['pluginname'] = 'Future Food';
$string['nav:manageachievements'] = 'Manage achievements';
$string['futurefood'] = 'Future Food';
$string['kicker'] = 'Adaptive missions';
$string['lede'] = 'Short scenario-based missions that turn theory into practical food-safety actions. Earn XP, unlock badges, and influence the menu of tomorrow.';
$string['level'] = 'Level';
$string['xp'] = 'XP';
$string['xptonext'] = '{$a} XP to the next level';
$string['achievements'] = 'Achievements';
$string['noachievements'] = 'No badges are configured yet. Achievements appear here once badges are set up and earned.';
$string['earned'] = 'Earned';
$string['locked'] = 'Locked';
$string['criteria_preview'] = 'Criteria preview';
$string['privacy:metadata'] = 'The Future Food plugin only displays badge and completion data stored elsewhere.';
$string['settings:pageheading'] = 'Future Food page';
$string['settings:pageheading_desc'] = 'No-code controls for the learner-facing Future Food page.';
$string['settings:heroheading'] = 'Hero section';
$string['settings:heroheading_desc'] = 'Configure the mission hero and the first mission call-to-action.';
$string['settings:decisionheading'] = 'Decision point';
$string['settings:decisionheading_desc'] = 'Configure the scenario decision block and its real Moodle activity links.';
$string['settings:missionsheading'] = 'Mission cards';
$string['settings:missionsheading_desc'] = 'Configure the repeatable video mission cards shown below the achievements.';
$string['showpagehead'] = 'Show page header';
$string['showpagehead_desc'] = 'Display the page kicker, title and intro copy.';
$string['showhero'] = 'Show hero section';
$string['showhero_desc'] = 'Display the mission hero, XP card and daily mission call-to-action.';
$string['showachievements'] = 'Show achievements';
$string['showachievements_desc'] = 'Display the badge-backed achievements section.';
$string['showmissions'] = 'Show mission cards';
$string['showmissions_desc'] = 'Display the configured mission cards section.';
$string['pagefallback_desc'] = 'Leave empty to use the default Future Food copy.';
$string['labelfallback_desc'] = 'Leave empty to use the default label.';
$string['setting:pagekicker'] = 'Page kicker';
$string['setting:pagetitle'] = 'Page title';
$string['setting:pagelede'] = 'Page intro text';
$string['setting:achievementstitle'] = 'Achievements title';
$string['setting:noachievements'] = 'No-achievements message';
$string['setting:missionstitle'] = 'Missions title';
$string['setting:nomissions'] = 'No-missions message';
$string['setting:startdailylabel'] = 'Hero CTA label';
$string['setting:startmissionlabel'] = 'Mission CTA label';
$string['setting:currentranklabel'] = 'Current-rank label';
$string['setting:totalxplabel'] = 'Total-XP label';
$string['setting:decisionkicker'] = 'Decision kicker';
$string['setting:decisiontitle'] = 'Decision title';
$string['setting:decisionempty'] = 'Decision empty-state message';
$string['setting:decisionhint'] = 'Decision hint';
$string['setting:decisionpill'] = 'Decision pill label';
$string['missions'] = 'Mission cards';
$string['missions_desc'] = 'Add, edit or remove video/activity cards. Internal Moodle activity URLs with completion enabled can award the configured XP after completion; external URLs are treated as resources only.';
$string['missionstitle'] = 'Active missions';
$string['nomissions'] = 'No missions are published yet.';
$string['missionrewardxp'] = 'Reward: +{$a} XP';
$string['missionrewardavailable'] = 'Available after Moodle completion: +{$a} XP';
$string['missionrewardawarded'] = 'Awarded: +{$a} XP';
$string['missionrewardnottracked'] = 'No automatic XP for this link. Use a Moodle activity with completion to award +{$a} XP.';
$string['missionchip:completed'] = 'Completed';
$string['missionchip:incomplete'] = 'In progress';
$string['missioncompletion:completed'] = 'Completed · XP awarded';
$string['missioncompletion:incomplete'] = 'Complete the activity to earn XP';
$string['missioncompletion:notcompletable'] = 'Activity completion is not enabled';
$string['missioncompletion:untracked'] = 'External or non-activity link';
$string['startmission'] = 'Start';
$string['currentrank'] = 'Current rank';
$string['totalxp'] = 'Total XP';
$string['progresstolevel'] = 'Progress to Level {$a->next} · {$a->current} / {$a->target} XP';
$string['badgecount'] = '{$a} badges';
$string['startdailymission'] = 'Start daily mission';
$string['showdecision'] = 'Show decision point';
$string['showdecision_desc'] = 'Display the Future Food decision module on the page.';
$string['decisionfallback_desc'] = 'Leave empty for the design copy.';
$string['decisionkicker'] = 'Decision point kicker';
$string['decisiontitle'] = 'Decision point title';
$string['decisionbody'] = 'Decision point body';
$string['decisionbody_desc'] = 'Short scenario text shown above the activity links.';
$string['decisionchoices'] = 'Decision choices';
$string['decisionchoices_desc'] = 'Add, edit or remove real Moodle activity links for the decision point.';
$string['decisionempty'] = 'No real decision activity is configured yet.';
$string['decisionhint'] = 'Each link opens a real Moodle activity. Feedback stays inside that activity.';
$string['decisionpill'] = 'Live activity';
$string['default_decisionkicker'] = 'Scenario path';
$string['default_decisiontitle'] = 'Decision point';
$string['default_decisionbody'] = 'Link each scenario choice to a real Moodle quiz or choice activity. The theme only renders the route; assessment and feedback stay inside Moodle.';
$string['herokicker'] = 'Hero — kicker';
$string['herotitle'] = 'Hero — title';
$string['herotext'] = 'Hero — text';
$string['herofallback_desc'] = 'Leave empty for the design copy.';
$string['default_herokicker'] = 'Agent mode active';
$string['default_herotitle'] = 'Mission: Zero Hunger';
$string['default_herotext'] = 'Collect evidence, run decision simulations, and earn XP toward your next agent rank. Every action you take shapes a real protocol used across the Living Labs.';
$string['settings:addchoice'] = 'Add decision choice';
$string['settings:addmission'] = 'Add mission card';
$string['settings:choiceblock'] = 'Decision choice {$a}';
$string['settings:choiceblocknew'] = 'New decision choice';
$string['settings:choiceicon'] = 'Icon';
$string['settings:choicelabel'] = 'Label';
$string['settings:choicenote'] = 'Note';
$string['settings:choiceurl'] = 'Activity URL';
$string['settings:deletechoice'] = 'Delete this choice';
$string['settings:deletemission'] = 'Delete this mission';
$string['settings:invalidurl'] = 'Use a relative Moodle URL that starts with /, or an http/https URL.';
$string['settings:invalidxp'] = 'XP must be a whole number.';
$string['settings:missionbadge'] = 'Badge label';
$string['settings:missionblock'] = 'Mission card {$a}';
$string['settings:missionblocknew'] = 'New mission card';
$string['settings:missionduration'] = 'Duration';
$string['settings:missionreward'] = 'Reward text';
$string['settings:missionreward_help'] = 'Optional. Leave empty to show “Reward: +XP”.';
$string['settings:missiontags'] = 'Tags';
$string['settings:missiontags_help'] = 'Separate tags with commas.';
$string['settings:missiontext'] = 'Description';
$string['settings:missiontitle'] = 'Title';
$string['settings:missionurl'] = 'Video or activity URL';
$string['settings:missionurl_help'] = 'Use an internal Moodle activity URL such as /mod/page/view.php?id=123 with completion enabled to award XP. External http/https URLs can be shown as video resources, but they do not award XP automatically.';
$string['settings:missionxp'] = 'XP';
$string['settings:requiredlabel'] = 'Each configured decision choice must have a label.';
$string['settings:requiredtitle'] = 'Each configured mission card must have a title.';
$string['settings:requiredurl'] = 'Each configured decision choice must have an activity URL.';
$string['settings:url_help'] = 'Use an internal Moodle path such as /course/view.php?id=2 or a full https URL.';
