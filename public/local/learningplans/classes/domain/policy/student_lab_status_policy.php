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
 * Course tile status policy for the Student Lab page.
 *
 * Pure domain policy — no Moodle dependencies (kept guard-free so it can be
 * unit-tested in isolation).
 *
 * @package    local_learningplans
 * @copyright  2026
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

declare(strict_types=1);

namespace local_learningplans\domain\policy;

/**
 * Derives the presentation status and action of each plan course for a user.
 *
 * Statuses: done · active · upnext · locked.
 * Actions:  review · resume · start · none.
 */
final class student_lab_status_policy {
    /** @var string */
    public const STATUS_DONE = 'done';
    /** @var string */
    public const STATUS_ACTIVE = 'active';
    /** @var string */
    public const STATUS_UPNEXT = 'upnext';
    /** @var string */
    public const STATUS_LOCKED = 'locked';

    /** @var string */
    public const ACTION_REVIEW = 'review';
    /** @var string */
    public const ACTION_RESUME = 'resume';
    /** @var string */
    public const ACTION_START = 'start';
    /** @var string */
    public const ACTION_NONE = 'none';

    /**
     * Resolve statuses for an ordered list of plan courses.
     *
     * The first incomplete course the user can access is "active"; later
     * accessible incomplete courses are "up next"; inaccessible courses
     * (sequential release not reached) are "locked".
     *
     * @param array<int, array{complete: bool, enrolled: bool, percentage: float|null}> $courses
     *        Ordered course states.
     * @return array<int, array{status: string, action: string}> Parallel list.
     */
    public static function resolve(array $courses): array {
        $result = [];
        $activefound = false;
        foreach ($courses as $state) {
            if ($state['complete']) {
                $result[] = ['status' => self::STATUS_DONE, 'action' => self::ACTION_REVIEW];
                continue;
            }
            if (!$state['enrolled']) {
                $result[] = ['status' => self::STATUS_LOCKED, 'action' => self::ACTION_NONE];
                continue;
            }
            $started = ($state['percentage'] ?? 0.0) > 0.0;
            if (!$activefound) {
                $activefound = true;
                $result[] = [
                    'status' => self::STATUS_ACTIVE,
                    'action' => $started ? self::ACTION_RESUME : self::ACTION_START,
                ];
                continue;
            }
            $result[] = ['status' => self::STATUS_UPNEXT, 'action' => self::ACTION_START];
        }
        return $result;
    }

    /**
     * Group an ordered course list into stages.
     *
     * Consecutive courses sharing a stage name form one stage; an empty name
     * starts (or continues) an unnamed stage. Stage status: done when every
     * course is done, locked when every course is locked, otherwise active.
     *
     * @param string[] $stagenames Ordered stage names (parallel to statuses).
     * @param array<int, array{status: string}> $resolved Output of resolve().
     * @return array<int, array{name: string, indexes: int[], status: string,
     *                          completed: int, total: int}>
     */
    public static function group_stages(array $stagenames, array $resolved): array {
        $stages = [];
        $current = null;
        foreach (array_values($stagenames) as $index => $name) {
            $name = trim((string)$name);
            if ($current === null || $name !== $current['name']) {
                if ($current !== null) {
                    $stages[] = $current;
                }
                $current = ['name' => $name, 'indexes' => [], 'status' => '', 'completed' => 0, 'total' => 0];
            }
            $current['indexes'][] = $index;
        }
        if ($current !== null) {
            $stages[] = $current;
        }

        foreach ($stages as $i => $stage) {
            $statuses = array_map(
                static fn(int $index): string => $resolved[$index]['status'] ?? self::STATUS_LOCKED,
                $stage['indexes']
            );
            $done = count(array_filter($statuses, static fn(string $s): bool => $s === self::STATUS_DONE));
            $locked = count(array_filter($statuses, static fn(string $s): bool => $s === self::STATUS_LOCKED));
            $total = count($statuses);
            $stages[$i]['completed'] = $done;
            $stages[$i]['total'] = $total;
            if ($done === $total && $total > 0) {
                $stages[$i]['status'] = self::STATUS_DONE;
            } else if ($locked === $total && $total > 0) {
                $stages[$i]['status'] = self::STATUS_LOCKED;
            } else {
                $stages[$i]['status'] = self::STATUS_ACTIVE;
            }
        }
        return $stages;
    }

    /**
     * Index of the recommended continue-learning target, or null.
     *
     * Preference order: the active course → first up-next → first done
     * (review). Locked courses are never returned.
     *
     * @param array<int, array{status: string}> $resolved Output of resolve().
     * @return int|null
     */
    public static function continue_index(array $resolved): ?int {
        foreach ([self::STATUS_ACTIVE, self::STATUS_UPNEXT, self::STATUS_DONE] as $wanted) {
            foreach ($resolved as $index => $item) {
                if ($item['status'] === $wanted) {
                    return $index;
                }
            }
        }
        return null;
    }
}
