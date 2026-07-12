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
$string['herokicker'] = 'Hero — kicker';
$string['herotitle'] = 'Hero — title';
$string['herotext'] = 'Hero — text';
$string['herofallback_desc'] = 'Leave empty for the design copy.';
$string['default_herokicker'] = 'Agent mode active';
$string['default_herotitle'] = 'Mission: Zero Hunger';
$string['default_herotext'] = 'Collect evidence, run decision simulations, and earn XP toward your next agent rank. Every action you take shapes a real protocol used across the Living Labs.';
