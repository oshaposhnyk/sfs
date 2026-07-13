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
$string['missions'] = 'Missions (JSON)';
$string['missions_desc'] = 'JSON list, e.g. [{"badge": "Featured", "tags": ["Data"], "title": "…", "text": "…", "duration": "11 min", "xp": 40, "url": "/course/view.php?id=2"}]. Leave empty to show no missions.';
$string['missionstitle'] = 'Active missions';
$string['nomissions'] = 'No missions are published yet.';
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
$string['decisionchoices'] = 'Decision choices (JSON)';
$string['decisionchoices_desc'] = 'JSON list of real activity links, for example [{"label":"Reject & report","icon":"verified_user","note":"Open the real Moodle quiz or choice activity.","url":"/mod/quiz/view.php?id=10"}]. Leave empty to show the empty state.';
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
