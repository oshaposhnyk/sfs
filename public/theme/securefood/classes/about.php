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
        $labs = [
            ['Kyiv Lab', 'Ukraine', 50.4501, 30.5234], ['Lviv Lab', 'Ukraine', 49.8397, 24.0297],
            ['Odesa Lab', 'Ukraine', 46.4825, 30.7233], ['Kharkiv Lab', 'Ukraine', 49.9935, 36.2304],
        ];
        $partners = [
            ['ICCS', 'Athens, Greece', 37.9838, 23.7275], ['GALANAKIS', 'Chania, Greece', 35.5138, 24.0180],
            ['European Dynamics', 'Brussels, Belgium', 50.8503, 4.3517], ['ZLC', 'Zaragoza, Spain', 41.6488, -0.8891],
            ['DNV', 'Oslo, Norway', 59.9139, 10.7522], ['IRIS', 'Castelldefels, Spain', 41.2810, 2.0],
            ['IAMO', 'Halle, Germany', 51.4969, 11.9690], ['EXUS AI Labs', 'London, UK', 51.5074, -0.1278],
            ['INNOV-ACTS', 'Nicosia, Cyprus', 35.1856, 33.3823], ['Carr Comms', 'Dublin, Ireland', 53.3498, -6.2603],
            ['LUKE', 'Helsinki, Finland', 60.1699, 24.9384], ['LAUREA', 'Espoo, Finland', 60.2055, 24.6559],
            ['EMPRACTIS', 'Lisbon, Portugal', 38.7223, -9.1393], ['MC SONAE', 'Porto, Portugal', 41.1579, -8.6291],
            ['EKPIZO', 'Athens, Greece', 37.9755, 23.7350], ['ELGO–DIMITRA', 'Thessaloniki, Greece', 40.6401, 22.9444],
            ['NULES', 'Kyiv, Ukraine', 50.3850, 30.4900], ['SPES', 'Padova, Italy', 45.4064, 11.8768],
        ];
        foreach ($labs as [$name, $country, $lat, $lon]) {
            $hubdefaults[] = ['name' => $name, 'country' => $country, 'type' => 'lab', 'lat' => $lat, 'lon' => $lon];
        }
        foreach ($partners as [$name, $country, $lat, $lon]) {
            $hubdefaults[] = ['name' => $name, 'country' => $country, 'type' => 'partner', 'lat' => $lat, 'lon' => $lon];
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
            $hub = [
                'name' => format_string((string)$item['name']),
                'country' => format_string((string)($item['country'] ?? '')),
                'islab' => $islab,
                'onmap' => false,
            ];
            // Dot-map position: equirectangular Europe window (lon -12..42, lat 34..62).
            if (isset($item['lat'], $item['lon']) && is_numeric($item['lat']) && is_numeric($item['lon'])) {
                $x = ((float)$item['lon'] + 12) / 54 * 100;
                $y = (62 - (float)$item['lat']) / 28 * 100;
                if ($x >= 0 && $x <= 100 && $y >= 0 && $y <= 100) {
                    $hub['onmap'] = true;
                    $hub['x'] = round($x, 1);
                    $hub['y'] = round($y, 1);
                }
            }
            $hubs[] = $hub;
        }

        $layerdefaults = [
            ['title' => 'Digital Twins',
                'text' => 'Model stress scenarios in virtual environments to predict impacts before they hit the field.'],
            ['title' => 'Governance frameworks',
                'text' => 'New standards for crisis management and policy-making across European food chains.'],
            ['title' => 'Living Labs',
                'text' => 'Community-led spaces where innovative solutions are tested in real-world conditions.'],
        ];
        $layeritems = json_decode((string)($settings->aboutlayers ?? ''), true);
        if (!is_array($layeritems) || $layeritems === []) {
            $layeritems = $layerdefaults;
        }
        $layers = [];
        foreach (array_values($layeritems) as $i => $item) {
            if (!is_array($item) || empty($item['title'])) {
                continue;
            }
            $layers[] = [
                'number' => $i + 1,
                'title' => format_string((string)$item['title']),
                'text' => format_string((string)($item['text'] ?? '')),
            ];
        }

        return [
            'titleaccent' => format_string($get('abouttitleaccent', 'evolving world'), true, ['escape' => false]),
            'shieldchip' => format_string($get('aboutshieldchip',
                'About the SecureFood project · Horizon Europe'), true, ['escape' => false]),
            'shieldtitle' => format_string($get('aboutshieldtitle',
                'A shield for the food systems of tomorrow'), true, ['escape' => false]),
            'shieldbody' => format_text($get('aboutshieldbody',
                '<p>Our world is facing unprecedented challenges — from climate shocks to global '
                . 'supply-chain disruptions. SecureFood is developing a "shield" for the food systems '
                . 'of tomorrow by integrating digital twins, governance frameworks, and community-led '
                . 'Living Labs into one operational ecosystem.</p>'
                . '<p>The School was established as a <strong>core flagship result of the project</strong>: '
                . 'its goal is to transform that science into skills that real people can use in real '
                . 'schools, kitchens and supply chains.</p>'), FORMAT_HTML),
            'layerstitle' => format_string($get('aboutlayerstitle', 'Three reinforcing layers'), true, ['escape' => false]),
            'layers' => $layers,
            'hubstitle' => format_string($get('abouthubstitle', 'Living Labs & partners across Europe'), true, ['escape' => false]),
            'hubs' => $hubs,
            'feedtitle' => format_string($get('aboutfeedtitle', 'Latest from the network'), true, ['escape' => false]),
            'feed' => $feed,
            'kicker' => format_string($get('aboutkicker', 'SecureFood School'), true, ['escape' => false]),
            'title' => format_string($get('abouttitle', 'Learning for resilience in an'), true, ['escape' => false]),
            'lede' => format_text($get('aboutlede',
                'The educational hub of the SecureFood project — turning complex science and '
                . 'digital tools into actionable skills for the professionals, policymakers and '
                . 'change-makers building tomorrow\'s food systems.'),
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
