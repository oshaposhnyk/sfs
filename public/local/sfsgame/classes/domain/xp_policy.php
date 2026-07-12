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
 * XP and level policy (pure domain, ADR-009).
 *
 * @package    local_sfsgame
 * @copyright  2026 SecureFood School
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

declare(strict_types=1);

namespace local_sfsgame\domain;

/**
 * Derives XP and level from earned badges and completed courses.
 *
 * v1: 100 XP per badge, 50 XP per completed plan course; a level every
 * 500 XP. Pure and unit-testable.
 */
final class xp_policy {
    /** @var int */
    public const XP_PER_BADGE = 100;
    /** @var int */
    public const XP_PER_COURSE = 50;
    /** @var int */
    public const XP_PER_LEVEL = 500;

    /**
     * Total XP.
     *
     * @param int $badges Earned badge count.
     * @param int $completedcourses Completed course count.
     * @return int
     */
    public static function xp(int $badges, int $completedcourses): int {
        return max(0, $badges) * self::XP_PER_BADGE + max(0, $completedcourses) * self::XP_PER_COURSE;
    }

    /**
     * Level for an XP total (level 1 at 0 XP).
     *
     * @param int $xp XP total.
     * @return int
     */
    public static function level(int $xp): int {
        return intdiv(max(0, $xp), self::XP_PER_LEVEL) + 1;
    }

    /**
     * XP still needed for the next level.
     *
     * @param int $xp XP total.
     * @return int
     */
    public static function to_next_level(int $xp): int {
        return self::XP_PER_LEVEL - (max(0, $xp) % self::XP_PER_LEVEL);
    }

    /**
     * Progress towards the next level, 0–100.
     *
     * @param int $xp XP total.
     * @return int
     */
    public static function level_progress(int $xp): int {
        return (int)round((max(0, $xp) % self::XP_PER_LEVEL) / self::XP_PER_LEVEL * 100);
    }
}
