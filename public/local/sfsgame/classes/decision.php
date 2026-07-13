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
 * Future Food decision module registry.
 *
 * @package    local_sfsgame
 * @copyright  2026 SecureFood School
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

declare(strict_types=1);

namespace local_sfsgame;

/**
 * Parses the decision activity configuration.
 */
final class decision {

    /**
     * Safe default: no configured decision links.
     *
     * @return array[]
     */
    public static function defaults(): array {
        return [];
    }

    /**
     * Parse the admin JSON into normalised choice links.
     *
     * @param string|null $json Setting value.
     * @return array[]
     */
    public static function parse(?string $json): array {
        $items = json_decode((string)$json, true);
        if (!is_array($items) || $items === []) {
            return self::defaults();
        }

        $out = [];
        foreach ($items as $item) {
            if (!is_array($item)) {
                continue;
            }

            $label = trim((string)($item['label'] ?? ''));
            $url = trim((string)($item['url'] ?? ''));
            if ($label === '' || $url === '') {
                continue;
            }

            if (!str_starts_with($url, '/') && !preg_match('~^https?://~i', $url)) {
                continue;
            }

            try {
                new \moodle_url($url);
            } catch (\Throwable $e) {
                continue;
            }

            $icon = trim((string)($item['icon'] ?? ''));
            $note = trim((string)($item['note'] ?? ''));

            $out[] = [
                'label' => $label,
                'url' => $url,
                'icon' => $icon,
                'note' => $note,
            ];
        }

        return $out !== [] ? $out : self::defaults();
    }
}
