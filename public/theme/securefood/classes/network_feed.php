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
 * External "Latest from the network" feed adapter.
 *
 * @package    theme_securefood
 * @copyright  2026 SecureFood School
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

declare(strict_types=1);

namespace theme_securefood;

defined('MOODLE_INTERNAL') || die();

/**
 * Normalises, stores and reads the About-page live feed.
 *
 * Storage deliberately uses Config API rather than a custom table: the feed is
 * a small replaceable presentation feed from an external system, not a Moodle
 * audit ledger.
 */
final class network_feed {
    /** @var string Stored live feed JSON config key. */
    private const CONFIG_ITEMS = 'aboutfeedlive';

    /** @var string Stored received timestamp config key. */
    private const CONFIG_TIME = 'aboutfeedlivetime';

    /** @var string[] Visual variants supported by the About feed card CSS. */
    private const VARIANTS = ['twin', 'culture', 'water', 'supply'];

    /** @var int Hard cap to avoid oversized config rows and expensive pages. */
    private const HARD_MAX_ITEMS = 12;

    /**
     * Read stored live items if live-feed rendering is enabled.
     *
     * @param settings_provider $provider Theme settings provider.
     * @return array[]
     */
    public static function stored_items(settings_provider $provider): array {
        if (!$provider->enabled('aboutfeeduselive')) {
            return [];
        }

        $json = (string)get_config('theme_securefood', self::CONFIG_ITEMS);
        if (trim($json) === '') {
            return [];
        }

        $decoded = json_decode($json, true);
        if (!is_array($decoded)) {
            return [];
        }

        return self::normalise_items($decoded, self::max_items($provider->text('aboutfeedwsmaxitems', '4')));
    }

    /**
     * Store a replacement live feed.
     *
     * @param array[] $items Normalised items.
     * @return void
     */
    public static function store(array $items): void {
        $json = $items === []
            ? ''
            : json_encode(array_values($items), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

        set_config(self::CONFIG_ITEMS, $json === false ? '' : $json, 'theme_securefood');
        set_config(self::CONFIG_TIME, (string)time(), 'theme_securefood');
    }

    /**
     * Normalise raw feed items to the design card contract.
     *
     * Accepted visible fields:
     * - chip
     * - title (required)
     * - text
     * - time
     * - variant: twin|culture|water|supply
     * - url: optional clickable title destination
     *
     * @param array $items Raw item array.
     * @param int $limit Max number of items to keep.
     * @return array[]
     */
    public static function normalise_items(array $items, int $limit): array {
        $out = [];
        $limit = self::max_items($limit);

        foreach (array_values($items) as $index => $item) {
            if (!is_array($item)) {
                continue;
            }

            $title = self::clean_text((string)($item['title'] ?? ''), 120);
            if ($title === '') {
                continue;
            }

            $variant = self::clean_text((string)($item['variant'] ?? ''), 24);
            if (!in_array($variant, self::VARIANTS, true)) {
                $variant = self::VARIANTS[$index % count(self::VARIANTS)];
            }

            $url = self::clean_url((string)($item['url'] ?? ''));

            $out[] = [
                'chip' => self::clean_text((string)($item['chip'] ?? ''), 48),
                'title' => $title,
                'text' => self::clean_text((string)($item['text'] ?? ''), 280),
                'time' => self::clean_text((string)($item['time'] ?? ''), 64),
                'variant' => $variant,
                'url' => $url,
            ];

            if (count($out) >= $limit) {
                break;
            }
        }

        return $out;
    }

    /**
     * Configured max items, clamped to a safe range.
     *
     * @param int|string $value Raw setting.
     * @return int
     */
    public static function max_items($value): int {
        $max = (int)$value;
        if ($max <= 0) {
            return 4;
        }

        return min($max, self::HARD_MAX_ITEMS);
    }

    /**
     * Clean visible text.
     *
     * @param string $value Raw value.
     * @param int $maxchars Max characters.
     * @return string
     */
    private static function clean_text(string $value, int $maxchars): string {
        $value = clean_param($value, PARAM_TEXT);
        $value = preg_replace('/\s+/', ' ', $value);
        $value = is_string($value) ? trim($value) : '';

        return \core_text::substr($value, 0, $maxchars);
    }

    /**
     * Clean optional URL.
     *
     * @param string $value Raw value.
     * @return string
     */
    private static function clean_url(string $value): string {
        $value = trim($value);
        if ($value === '' || str_starts_with($value, '//')
                || (!str_starts_with($value, '/') && !preg_match('~^https?://~i', $value))) {
            return '';
        }

        try {
            return (new \moodle_url($value))->out(false);
        } catch (\Throwable $exception) {
            return '';
        }
    }
}
