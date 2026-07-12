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
 * About the Project front-page content (domain 04, ADR-007).
 *
 * @package    theme_securefood
 * @copyright  2026 SecureFood School
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

declare(strict_types=1);

namespace theme_securefood;

/**
 * Builds the settings-driven About/front-page template context.
 *
 * Every text comes from a theme setting; the prototype copy is the default,
 * so an unconfigured site matches the design (insights.html).
 */
final class about {
    /**
     * Template context for theme_securefood/about.
     *
     * @param \stdClass|null $settings theme_config->settings.
     * @return array
     */
    public static function context(?\stdClass $settings): array {
        $get = static function(string $name, string $default) use ($settings): string {
            $value = trim((string)($settings->{$name} ?? ''));
            return $value !== '' ? $value : $default;
        };

        $stats = self::pairs(
            (string)($settings->aboutstats ?? ''),
            [
                ['value' => '€8M', 'label' => 'Horizon Europe'],
                ['value' => '21', 'label' => 'Partners'],
                ['value' => '10+', 'label' => 'Living Labs'],
                ['value' => 'L4C', 'label' => 'Methodology'],
            ]
        );

        // The design's About page has no KPI row — render only when configured.
        $kpis = self::pairs((string)($settings->aboutkpis ?? ''), []);

        $feeddefaults = [
            ['chip' => 'Digital twin', 'title' => 'Kyiv Living Lab twin goes live',
                'text' => 'Real-time mirroring of the cold-chain sensors is now open to learners.',
                'time' => '2 days ago'],
            ['chip' => 'Culture', 'title' => 'School kitchens join the pilot',
                'text' => 'Three school kitchens start logging temperature and hygiene data.',
                'time' => '5 days ago'],
            ['chip' => 'Water', 'title' => 'Irrigation quality module updated',
                'text' => 'New thresholds aligned with the latest EFSA guidance.',
                'time' => '1 week ago'],
            ['chip' => 'Supply chain', 'title' => 'Route risk workshop recap',
                'text' => 'Learners mapped disruption scenarios across two supply corridors.',
                'time' => '2 weeks ago'],
        ];
        $feeditems = json_decode((string)($settings->aboutfeed ?? ''), true);
        if (!is_array($feeditems) || $feeditems === []) {
            $feeditems = $feeddefaults;
        }
        $variants = ['twin', 'culture', 'water', 'supply'];
        $feed = [];
        foreach ($feeditems as $i => $item) {
            if (!is_array($item) || empty($item['title'])) {
                continue;
            }
            $feed[] = [
                'chip' => format_string((string)($item['chip'] ?? '')),
                'title' => format_string((string)$item['title']),
                'text' => format_string((string)($item['text'] ?? '')),
                'time' => format_string((string)($item['time'] ?? '')),
                'variant' => $variants[$i % count($variants)],
            ];
        }


        $hubdefaults = [];
        $labs = [['Kyiv Lab', 'Ukraine'], ['Lviv Lab', 'Ukraine'], ['Odesa Lab', 'Ukraine'], ['Kharkiv Lab', 'Ukraine']];
        $partners = [
            ['ICCS', 'Athens, Greece'], ['GALANAKIS', 'Chania, Greece'],
            ['European Dynamics', 'Brussels, Belgium'], ['ZLC', 'Zaragoza, Spain'],
            ['DNV', 'Oslo, Norway'], ['IRIS', 'Castelldefels, Spain'],
            ['IAMO', 'Halle, Germany'], ['EXUS AI Labs', 'London, UK'],
            ['INNOV-ACTS', 'Nicosia, Cyprus'], ['Carr Comms', 'Dublin, Ireland'],
            ['LUKE', 'Helsinki, Finland'], ['LAUREA', 'Espoo, Finland'],
            ['EMPRACTIS', 'Lisbon, Portugal'], ['MC SONAE', 'Porto, Portugal'],
            ['EKPIZO', 'Athens, Greece'], ['ELGO–DIMITRA', 'Thessaloniki, Greece'],
            ['NULES', 'Kyiv, Ukraine'], ['SPES', 'Padova, Italy'],
        ];
        foreach ($labs as [$name, $country]) {
            $hubdefaults[] = ['name' => $name, 'country' => $country, 'type' => 'lab'];
        }
        foreach ($partners as [$name, $country]) {
            $hubdefaults[] = ['name' => $name, 'country' => $country, 'type' => 'partner'];
        }
        $hubitems = json_decode((string)($settings->abouthubs ?? ''), true);
        if (!is_array($hubitems) || $hubitems === []) {
            $hubitems = $hubdefaults;
        }
        $hubs = [];
        foreach ($hubitems as $item) {
            if (!is_array($item) || empty($item['name'])) {
                continue;
            }
            $islab = ($item['type'] ?? 'partner') === 'lab';
            $hubs[] = [
                'name' => format_string((string)$item['name']),
                'country' => format_string((string)($item['country'] ?? '')),
                'islab' => $islab,
            ];
        }

        return [
            'hubstitle' => format_string($get('abouthubstitle', 'Living Labs & partners across Europe'), true, ['escape' => false]),
            'hubs' => $hubs,
            'feedtitle' => format_string($get('aboutfeedtitle', 'Latest from the network'), true, ['escape' => false]),
            'feed' => $feed,
            'kicker' => format_string($get('aboutkicker', 'About the SecureFood project · Horizon Europe'), true, ['escape' => false]),
            'title' => format_string($get('abouttitle', 'A shield for the food systems of tomorrow'), true, ['escape' => false]),
            'lede' => format_text($get('aboutlede',
                'Our world is facing unprecedented challenges — from climate shocks to global '
                . 'supply-chain disruptions. SecureFood is developing a "shield" for the food '
                . 'systems of tomorrow by integrating digital twins, governance frameworks, and '
                . 'community-led Living Labs into one operational ecosystem.'),
                FORMAT_HTML),
            'stats' => $stats,
            'kpis' => $kpis,
        ];
    }

    /**
     * Parse a JSON list setting with a design default fallback.
     *
     * @param string $json Admin JSON ([{"value": "...", "label": "...", "sub": "..."}]).
     * @param array $default Fallback list.
     * @return array
     */
    private static function pairs(string $json, array $default): array {
        $items = json_decode($json, true);
        if (!is_array($items)) {
            $items = $default;
        }
        $out = [];
        foreach ($items as $item) {
            if (!is_array($item) || !isset($item['value'], $item['label'])) {
                continue;
            }
            $out[] = [
                'value' => format_string((string)$item['value']),
                'label' => format_string((string)$item['label']),
                'sub' => isset($item['sub']) ? format_string((string)$item['sub']) : '',
            ];
        }
        return $out !== [] || $default === [] ? $out : $default;
    }
}
