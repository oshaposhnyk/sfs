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
