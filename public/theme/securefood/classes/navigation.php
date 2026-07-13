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
 * Settings-driven sidebar navigation (ADR-007).
 *
 * @package    theme_securefood
 * @copyright  2026 SecureFood School
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

declare(strict_types=1);

namespace theme_securefood;

/**
 * Builds the sidebar navigation model from the admin JSON setting.
 *
 * Setting format (theme_securefood/navigation), an array of sections:
 * [
 *   {"label": "Learning", "items": [
 *     {"title": "Student Lab", "url": "/local/learningplans/my.php",
 *      "icon": "school", "visibility": "loggedin"}
 *     {"title": "Admin", "url": "/admin/search.php",
 *      "icon": "admin_panel_settings", "visibility": "capability",
 *      "capability": "moodle/site:config"}
 *   ]}
 * ]
 *
 * visibility: "all" (default), "loggedin" or "capability". Titles and labels
 * pass through format_string() at render time so multilang filters apply.
 */
final class navigation {
    /**
     * Parse and normalise the navigation JSON setting.
     *
     * Pure and side-effect free. Returns null when the JSON is invalid or has
     * no usable items so callers can fall back to {@see self::defaults()}.
     *
     * @param string|null $json Raw setting value.
     * @return array[]|null Normalised sections or null.
     */
    public static function parse(?string $json): ?array {
        if ($json === null || trim($json) === '') {
            return null;
        }
        $decoded = json_decode($json, true);
        if (!is_array($decoded)) {
            return null;
        }

        $sections = [];
        foreach ($decoded as $section) {
            if (!is_array($section) || !isset($section['items']) || !is_array($section['items'])) {
                continue;
            }
            $items = [];
            foreach ($section['items'] as $item) {
                if (!is_array($item) || empty($item['title']) || empty($item['url'])) {
                    continue;
                }
                $visibility = (string)($item['visibility'] ?? 'all');
                if (!in_array($visibility, ['loggedin', 'capability'], true)) {
                    $visibility = 'all';
                }
                $normalised = [
                    'title' => (string)$item['title'],
                    'url' => (string)$item['url'],
                    'icon' => preg_replace('/[^a-z0-9_]/', '', (string)($item['icon'] ?? 'circle')),
                    'visibility' => $visibility,
                ];
                if ($visibility === 'capability') {
                    $capability = trim((string)($item['capability'] ?? ''));
                    if ($capability === '') {
                        continue;
                    }
                    $normalised['capability'] = $capability;
                }
                $items[] = $normalised;
            }
            if ($items !== []) {
                $sections[] = [
                    'label' => (string)($section['label'] ?? ''),
                    'items' => $items,
                ];
            }
        }
        return $sections === [] ? null : $sections;
    }

    /**
     * Default navigation: the prototype menu, minus pages that do not exist yet.
     *
     * Titles are language-string keys resolved at render time ('titlestr').
     *
     * @return array[]
     */
    public static function defaults(): array {
        return [
            [
                'labelstr' => 'nav_learning',
                'items' => [
                    ['titlestr' => 'nav_about', 'url' => '/?redirect=0', 'icon' => 'info', 'visibility' => 'all'],
                    ['titlestr' => 'nav_studentlab', 'url' => '/local/learningplans/my.php',
                        'icon' => 'school', 'visibility' => 'loggedin'],
                    ['titlestr' => 'nav_futurefood', 'url' => '/local/sfsgame/index.php',
                        'icon' => 'videogame_asset', 'visibility' => 'loggedin'],
                    ['titlestr' => 'nav_resources', 'url' => '/local/sfsresources/index.php',
                        'icon' => 'gavel', 'visibility' => 'loggedin'],
                    ['titlestr' => 'nav_mycourses', 'url' => '/my/courses.php',
                        'icon' => 'grid_view', 'visibility' => 'loggedin'],
                ],
            ],
            [
                'labelstr' => 'nav_account',
                'items' => [
                    ['titlestr' => 'nav_notifications', 'url' => '/message/output/popup/notifications.php',
                        'icon' => 'notifications', 'visibility' => 'loggedin'],
                    ['titlestr' => 'nav_messages', 'url' => '/message/index.php',
                        'icon' => 'forum', 'visibility' => 'loggedin'],
                    ['titlestr' => 'nav_settings', 'url' => '/theme/securefood/mysettings.php',
                        'icon' => 'settings', 'visibility' => 'loggedin'],
                ],
            ],
        ];
    }

    /**
     * Template-ready sections for the current request.
     *
     * Resolves setting vs defaults, localises labels, filters by visibility
     * and marks the active item by longest URL prefix match.
     *
     * @param \moodle_url $currenturl The current page URL.
     * @param bool $loggedin Whether a real (non-guest) user is logged in.
     * @param \context|null $context Context for capability-gated items.
     * @return array[]
     */
    public static function for_page(\moodle_url $currenturl, bool $loggedin, ?\context $context = null): array {
        global $CFG;

        $sections = self::parse(settings_provider::from_config()->navigation_json()) ?? self::defaults();
        $currentpath = $currenturl->out_omit_querystring();
        $context = $context ?? \context_system::instance();

        // Find the active item: longest matching URL prefix wins.
        $bestlen = 0;
        $bestref = null;
        $result = [];
        foreach ($sections as $si => $section) {
            $items = [];
            foreach ($section['items'] as $item) {
                $visibility = $item['visibility'] ?? 'all';
                if ($visibility === 'loggedin' && !$loggedin) {
                    continue;
                }
                if ($visibility === 'capability' && (!$loggedin || empty($item['capability'])
                        || !has_capability($item['capability'], $context))) {
                    continue;
                }
                $title = isset($item['titlestr'])
                    ? get_string($item['titlestr'], 'theme_securefood')
                    : format_string($item['title']);
                $url = new \moodle_url($item['url']);
                $fullurl = $url->out(false);
                $path = $url->out_omit_querystring();
                $items[] = [
                    'title' => $title,
                    'url' => $fullurl,
                    'icon' => $item['icon'],
                    'active' => false,
                ];
                $ii = count($items) - 1;
                $ishome = rtrim($path, '/') === rtrim($CFG->wwwroot, '/');
                $matches = $ishome
                    ? rtrim($currentpath, '/') === rtrim($CFG->wwwroot, '/')
                    : str_starts_with($currentpath, $path);
                if ($matches && strlen($path) > $bestlen) {
                    $bestlen = strlen($path);
                    $bestref = [count($result), $ii];
                }
            }
            if ($items !== []) {
                $label = isset($section['labelstr'])
                    ? get_string($section['labelstr'], 'theme_securefood')
                    : format_string($section['label']);
                $result[] = ['label' => $label, 'haslabel' => $label !== '', 'items' => $items];
            }
        }
        if ($bestref !== null) {
            $result[$bestref[0]]['items'][$bestref[1]]['active'] = true;
        }
        return $result;
    }
}
