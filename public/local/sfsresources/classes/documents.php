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
 * Document registry: admin JSON with the design defaults.
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
     * Design defaults (governance.html).
     *
     * @return array[]
     */
    public static function defaults(): array {
        return [
            ['title' => 'HACCP Protocol v2.1', 'sub' => 'Cold chain & temperature thresholds',
                'audience' => 'Director', 'kind' => 'pdf', 'updated' => '2 days ago', 'url' => ''],
            ['title' => 'Supplier MoU Template', 'sub' => 'Standard agreement, EU-aligned',
                'audience' => 'Supplier', 'kind' => 'doc', 'updated' => '5 days ago', 'url' => ''],
            ['title' => 'Sensor Calibration Guide', 'sub' => 'Setup & maintenance for IoT nodes',
                'audience' => 'Tech', 'kind' => 'pdf', 'updated' => '1 week ago', 'url' => ''],
            ['title' => 'Menu Audit Worksheet', 'sub' => 'Weekly nutritional check',
                'audience' => 'Director', 'kind' => 'xls', 'updated' => '1 week ago', 'url' => ''],
            ['title' => 'Data Privacy Annex', 'sub' => 'GDPR clauses for school data',
                'audience' => 'Legal', 'kind' => 'pdf', 'updated' => '2 weeks ago', 'url' => ''],
            ['title' => 'Procurement Checklist', 'sub' => 'Vegetables & dairy verification',
                'audience' => 'Supplier', 'kind' => 'pdf', 'updated' => '3 weeks ago', 'url' => ''],
            ['title' => 'Twin Deployment Bundle', 'sub' => 'Onboarding kit for new hubs',
                'audience' => 'Tech', 'kind' => 'zip', 'updated' => '1 month ago', 'url' => ''],
        ];
    }

    /**
     * Parse the admin JSON, falling back to the design defaults.
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
