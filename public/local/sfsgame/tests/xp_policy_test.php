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
 * Unit tests for the XP policy.
 *
 * @package    local_sfsgame
 * @copyright  2026 SecureFood School
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

declare(strict_types=1);

namespace local_sfsgame;

use local_sfsgame\domain\xp_policy;

/**
 * Tests for {@see xp_policy}.
 *
 * @covers \local_sfsgame\domain\xp_policy
 */
final class xp_policy_test extends \basic_testcase {

    public function test_xp_totals(): void {
        $this->assertSame(0, xp_policy::xp(0, 0));
        $this->assertSame(100, xp_policy::xp(1, 0));
        $this->assertSame(250, xp_policy::xp(2, 1));
        $this->assertSame(275, xp_policy::xp(2, 1, 25));
        $this->assertSame(0, xp_policy::xp(-3, -1, -25));
    }

    public function test_levels(): void {
        $this->assertSame(1, xp_policy::level(0));
        $this->assertSame(1, xp_policy::level(499));
        $this->assertSame(2, xp_policy::level(500));
        $this->assertSame(3, xp_policy::level(1249));
    }

    public function test_next_level_progress(): void {
        $this->assertSame(500, xp_policy::to_next_level(0));
        $this->assertSame(1, xp_policy::to_next_level(499));
        $this->assertSame(0, xp_policy::level_progress(0));
        $this->assertSame(50, xp_policy::level_progress(250));
    }
}
