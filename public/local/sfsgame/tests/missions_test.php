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
 * Tests for the missions registry.
 *
 * @package    local_sfsgame
 * @copyright  2026 SecureFood School
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

declare(strict_types=1);

namespace local_sfsgame;

/**
 * Tests for {@see missions}.
 *
 * @covers \local_sfsgame\missions
 */
final class missions_test extends \basic_testcase {

    public function test_unconfigured_registry_is_empty(): void {
        $this->assertSame([], missions::defaults());
        $this->assertSame([], missions::parse(null));
        $this->assertSame([], missions::parse('invalid json'));
    }

    public function test_parse_normalises_configured_mission(): void {
        $missions = missions::parse(json_encode([[
            'badge' => 'Featured',
            'tags' => ['Data', ''],
            'title' => 'Real mission',
            'text' => 'Use real course content.',
            'duration' => '10 min',
            'xp' => 25,
            'url' => '/course/view.php?id=2',
        ]]));

        $this->assertCount(1, $missions);
        $this->assertSame('Real mission', $missions[0]['title']);
        $this->assertSame([['name' => 'Data']], $missions[0]['tags']);
        $this->assertSame(25, $missions[0]['xp']);
    }
}
