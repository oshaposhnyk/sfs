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
 * Derives XP and level from earned badges, completed courses and completed
 * mission activities.
 *
 * v1: 100 XP per badge, 50 XP per completed plan course, plus administrator-
 * configured mission XP only after the linked Moodle activity is completed.
 * A level is reached every 500 XP. Pure and unit-testable.
 */
final class xp_policy {
    /** @var int */
    public const XP_PER_BADGE = 100;
    /** @var int */
    public const XP_PER_COURSE = 50;
    /** @var int */
    public const XP_PER_LEVEL = 500;

    /**
     * Normalise a configurable rate to a positive int, or fall back.
     *
     * @param int|null $value Configured value.
     * @param int $default Default (the per-* constant).
     * @return int
     */
    private static function rate(?int $value, int $default): int {
        return ($value !== null && $value > 0) ? $value : $default;
    }

    /**
     * Total XP.
     *
     * Rates are optional (admin-configurable, ADR-007); null uses the default
     * constant, so the domain stays pure — callers pass the resolved rates.
     *
     * @param int $badges Earned badge count.
     * @param int $completedcourses Completed course count.
     * @param int $missionxp XP from completed mission activities.
     * @param int|null $perbadge XP per badge (default XP_PER_BADGE).
     * @param int|null $percourse XP per completed course (default XP_PER_COURSE).
     * @return int
     */
    public static function xp(
        int $badges,
        int $completedcourses,
        int $missionxp = 0,
        ?int $perbadge = null,
        ?int $percourse = null
    ): int {
        return max(0, $badges) * self::rate($perbadge, self::XP_PER_BADGE)
            + max(0, $completedcourses) * self::rate($percourse, self::XP_PER_COURSE)
            + max(0, $missionxp);
    }

    /**
     * Level for an XP total (level 1 at 0 XP).
     *
     * @param int $xp XP total.
     * @param int|null $perlevel XP per level (default XP_PER_LEVEL).
     * @return int
     */
    public static function level(int $xp, ?int $perlevel = null): int {
        return intdiv(max(0, $xp), self::rate($perlevel, self::XP_PER_LEVEL)) + 1;
    }

    /**
     * XP still needed for the next level.
     *
     * @param int $xp XP total.
     * @param int|null $perlevel XP per level (default XP_PER_LEVEL).
     * @return int
     */
    public static function to_next_level(int $xp, ?int $perlevel = null): int {
        $perlevel = self::rate($perlevel, self::XP_PER_LEVEL);
        return $perlevel - (max(0, $xp) % $perlevel);
    }

    /**
     * Progress towards the next level, 0–100.
     *
     * @param int $xp XP total.
     * @param int|null $perlevel XP per level (default XP_PER_LEVEL).
     * @return int
     */
    public static function level_progress(int $xp, ?int $perlevel = null): int {
        $perlevel = self::rate($perlevel, self::XP_PER_LEVEL);
        return (int)round((max(0, $xp) % $perlevel) / $perlevel * 100);
    }
}
