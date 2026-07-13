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
 * Missions registry: administrator-curated JSON only (ADR-009).
 *
 * @package    local_sfsgame
 * @copyright  2026 SecureFood School
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

declare(strict_types=1);

namespace local_sfsgame;

/**
 * Parses the missions setting; pure apart from the caller-supplied JSON.
 */
final class missions {
    /**
     * Safe default: no missions until an administrator configures real links.
     *
     * @return array[]
     */
    public static function defaults(): array {
        return [];
    }

    /**
     * Parse the admin JSON, falling back to an empty safe state.
     *
     * @param string|null $json Setting value.
     * @return array[] Normalised missions.
     */
    public static function parse(?string $json): array {
        $items = json_decode((string)$json, true);
        if (!is_array($items) || $items === []) {
            $items = self::defaults();
        }
        $out = [];
        foreach ($items as $item) {
            if (!is_array($item) || empty($item['title'])) {
                continue;
            }
            $tags = [];
            foreach ((array)($item['tags'] ?? []) as $tag) {
                if (is_string($tag) && trim($tag) !== '') {
                    $tags[] = ['name' => trim($tag)];
                }
            }
            $out[] = [
                'badge' => (string)($item['badge'] ?? ''),
                'title' => (string)$item['title'],
                'text' => (string)($item['text'] ?? ''),
                'duration' => (string)($item['duration'] ?? ''),
                'xp' => (int)($item['xp'] ?? 0),
                'url' => (string)($item['url'] ?? ''),
                'reward' => (string)($item['reward'] ?? ''),
                'tags' => $tags,
            ];
        }
        return $out !== [] ? $out : self::defaults();
    }
}
