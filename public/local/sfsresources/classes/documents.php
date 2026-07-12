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
 * Document registry: administrator-curated JSON only.
 *
 * @package    local_sfsresources
 * @copyright  2026 SecureFood School
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

declare(strict_types=1);

namespace local_sfsresources;

/**
 * Parses the documents setting (v1: curated links, no file storage).
 */
final class documents {
    /** @var string[] Recognised type badges. */
    private const KINDS = ['pdf', 'doc', 'xls', 'zip', 'link'];

    /**
     * Safe default: no curated documents until an administrator publishes them.
     *
     * @return array[]
     */
    public static function defaults(): array {
        return [];
    }

    /**
     * Map a file name to a library type badge.
     *
     * @param string $filename File name.
     * @return string One of pdf/doc/xls/zip/link.
     */
    public static function kind_from_filename(string $filename): string {
        $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        $map = [
            'pdf' => 'pdf',
            'doc' => 'doc', 'docx' => 'doc', 'odt' => 'doc', 'rtf' => 'doc',
            'xls' => 'xls', 'xlsx' => 'xls', 'ods' => 'xls', 'csv' => 'xls',
            'zip' => 'zip', 'gz' => 'zip', '7z' => 'zip', 'rar' => 'zip',
        ];
        return $map[$extension] ?? 'link';
    }

    /**
     * Parse the admin JSON, falling back to an empty safe state.
     *
     * @param string|null $json Setting value.
     * @return array[]
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
            $kind = strtolower((string)($item['kind'] ?? 'link'));
            $out[] = [
                'title' => (string)$item['title'],
                'sub' => (string)($item['sub'] ?? ''),
                'audience' => (string)($item['audience'] ?? ''),
                'kind' => in_array($kind, self::KINDS, true) ? $kind : 'link',
                'updated' => (string)($item['updated'] ?? ''),
                'url' => (string)($item['url'] ?? ''),
            ];
        }
        return $out !== [] ? $out : self::defaults();
    }
}
