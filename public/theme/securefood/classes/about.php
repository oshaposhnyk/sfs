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
     * @param \stdClass|null $settings Theme settings object.
     * @return array
     */
    public static function context(?\stdClass $settings): array {
        $provider = settings_provider::from_theme_settings($settings);
        $get = static function(string $name, string $default) use ($provider): string {
            return $provider->text($name, $default);
        };

        $stats = [];
        if ($provider->enabled('showaboutstats')) {
            $stats = self::pairs(
                $provider->raw('aboutstats'),
                [
                    ['value' => get_string('aboutdefault_stat_horizon_value', 'theme_securefood'),
                        'label' => get_string('aboutdefault_stat_horizon_label', 'theme_securefood')],
                    ['value' => get_string('aboutdefault_stat_partners_value', 'theme_securefood'),
                        'label' => get_string('aboutdefault_stat_partners_label', 'theme_securefood')],
                    ['value' => get_string('aboutdefault_stat_labs_value', 'theme_securefood'),
                        'label' => get_string('aboutdefault_stat_labs_label', 'theme_securefood')],
                    ['value' => get_string('aboutdefault_stat_method_value', 'theme_securefood'),
                        'label' => get_string('aboutdefault_stat_method_label', 'theme_securefood')],
                ]
            );
        }

        // The design's About page has no KPI row — render only when configured.
        $kpis = self::pairs($provider->raw('aboutkpis'), []);

        $feeditems = network_feed::stored_items($provider);
        if ($feeditems === []) {
            $feeditems = json_decode($provider->raw('aboutfeed'), true);
        }
        if (!is_array($feeditems) || $feeditems === []) {
            $feeditems = [];
        }
        $variants = ['twin', 'culture', 'water', 'supply'];
        $feed = [];
        foreach ($feeditems as $i => $item) {
            if (!is_array($item) || empty($item['title'])) {
                continue;
            }
            $variant = (string)($item['variant'] ?? '');
            if (!in_array($variant, $variants, true)) {
                $variant = $variants[$i % count($variants)];
            }
            $url = '';
            if (!empty($item['url']) && is_string($item['url'])
                    && ((str_starts_with($item['url'], '/') && !str_starts_with($item['url'], '//'))
                        || preg_match('~^https?://~i', $item['url']) === 1)) {
                try {
                    $url = (new \moodle_url($item['url']))->out(false);
                } catch (\Throwable $exception) {
                    $url = '';
                }
            }
            $feed[] = [
                'chip' => format_string((string)($item['chip'] ?? '')),
                'title' => format_string((string)$item['title']),
                'text' => format_string((string)($item['text'] ?? '')),
                'time' => format_string((string)($item['time'] ?? '')),
                'url' => $url,
                'hasurl' => $url !== '',
                'variant' => $variant,
            ];
        }
        if (!$provider->enabled('showaboutfeed')) {
            $feed = [];
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
        $hubitems = json_decode($provider->raw('abouthubs'), true);
        if (!is_array($hubitems) || $hubitems === []) {
            $hubitems = $hubdefaults;
        }
        $hubs = [];
        foreach (array_values($hubitems) as $i => $item) {
            if (!is_array($item) || empty($item['name'])) {
                continue;
            }
            $islab = ($item['type'] ?? 'partner') === 'lab';
            $status = get_string($islab ? 'abouthub_lab' : 'abouthub_partner', 'theme_securefood');
            $name = format_string((string)$item['name']);
            $country = format_string((string)($item['country'] ?? ''));
            $hub = [
                'hubid' => 'sfs-hub-' . ($i + 1),
                'name' => $name,
                'country' => $country,
                'islab' => $islab,
                'markerlabel' => get_string('abouthubmarkerlabel', 'theme_securefood', (object)[
                    'name' => $name,
                    'country' => $country,
                    'status' => $status,
                ]),
                'onmap' => false,
            ];
            // Dot-map position: equirectangular Europe window (lon -12..42, lat 34..62).
            if (isset($item['lat'], $item['lon']) && is_numeric($item['lat']) && is_numeric($item['lon'])) {
                // Raw coordinates feed the interactive Leaflet map (aboutmap AMD).
                $hub['lat'] = (float)$item['lat'];
                $hub['lon'] = (float)$item['lon'];
                $x = ((float)$item['lon'] + 12) / 54 * 100;
                $y = (62 - (float)$item['lat']) / 28 * 100;
                if ($x >= 0 && $x <= 100 && $y >= 0 && $y <= 100) {
                    $hub['onmap'] = true;
                    $hub['x'] = round($x, 1);
                    $hub['y'] = round($y, 1);
                    $hub['markerxclass'] = (int)round($x);
                    $hub['markeryclass'] = (int)round($y);
                }
            }
            $hubs[] = $hub;
        }
        if (!$provider->enabled('showabouthubs')) {
            $hubs = [];
        }

        $layerdefaults = [
            self::layer_default(1),
            self::layer_default(2),
            self::layer_default(3),
        ];
        $layeritems = json_decode($provider->raw('aboutlayers'), true);
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
        $showlayers = $provider->enabled('showaboutlayers') && $layers !== [];

        $approachdefaults = [
            self::approach_step_default(1),
            self::approach_step_default(2),
            self::approach_step_default(3),
            self::approach_step_default(4),
        ];
        $approachitems = json_decode($provider->raw('aboutapproachsteps'), true);
        if (!is_array($approachitems) || $approachitems === []) {
            $approachitems = $approachdefaults;
        }
        $approachsteps = [];
        foreach ($approachitems as $item) {
            if (!is_array($item) || trim((string)($item['title'] ?? '')) === '') {
                continue;
            }
            $approachsteps[] = [
                'number' => format_string((string)($item['number'] ?? '')),
                'title' => format_string((string)$item['title']),
                'text' => format_string((string)($item['text'] ?? '')),
            ];
        }
        $showapproach = $provider->enabled('showaboutapproach') && $approachsteps !== [];

        return [
            'titleaccent' => format_string($get('abouttitleaccent',
                get_string('aboutdefault_titleaccent', 'theme_securefood')), true, ['escape' => false]),
            'shieldchip' => format_string($get('aboutshieldchip',
                get_string('aboutdefault_shieldchip', 'theme_securefood')), true, ['escape' => false]),
            'shieldtitle' => format_string($get('aboutshieldtitle',
                get_string('aboutdefault_shieldtitle', 'theme_securefood')), true, ['escape' => false]),
            'shieldbody' => format_text($get('aboutshieldbody',
                get_string('aboutdefault_shieldbody', 'theme_securefood')), FORMAT_HTML),
            'layerstitle' => format_string($get('aboutlayerstitle',
                get_string('aboutdefault_layerstitle', 'theme_securefood')), true, ['escape' => false]),
            'showlayers' => $showlayers,
            'layers' => $layers,
            'showapproach' => $showapproach,
            'approachkicker' => format_string($get('aboutapproachkicker',
                get_string('aboutdefault_approachkicker', 'theme_securefood')), true, ['escape' => false]),
            'approachtitle' => format_string($get('aboutapproachtitle',
                get_string('aboutdefault_approachtitle', 'theme_securefood')), true, ['escape' => false]),
            'approachbody' => format_text($get('aboutapproachbody',
                get_string('aboutdefault_approachbody', 'theme_securefood')), FORMAT_HTML),
            'approachsteps' => $approachsteps,
            'hubstitle' => format_string($get('abouthubstitle',
                get_string('aboutdefault_hubstitle', 'theme_securefood')), true, ['escape' => false]),
            'hubsnetworktitle' => format_string($get('abouthubsnetworktitle',
                get_string('aboutdefault_hubsnetworktitle', 'theme_securefood')), true, ['escape' => false]),
            'hubsnetworklede' => format_string($get('abouthubsnetworklede',
                get_string('aboutdefault_hubsnetworklede', 'theme_securefood')), true, ['escape' => false]),
            'maplabel' => get_string('aboutmaplabel', 'theme_securefood'),
            'hubs' => $hubs,
            'feedtitle' => format_string($get('aboutfeedtitle',
                get_string('aboutdefault_feedtitle', 'theme_securefood')), true, ['escape' => false]),
            'feed' => $feed,
            'kicker' => format_string($get('aboutkicker',
                get_string('aboutdefault_kicker', 'theme_securefood')), true, ['escape' => false]),
            'title' => format_string($get('abouttitle',
                get_string('aboutdefault_title', 'theme_securefood')), true, ['escape' => false]),
            'lede' => format_text($get('aboutlede',
                get_string('aboutdefault_lede', 'theme_securefood')),
                FORMAT_HTML),
            'stats' => $stats,
            'kpis' => $kpis,
        ];
    }

    /**
     * One localised reinforcing-layer default.
     *
     * @param int $number Layer number, 1..3.
     * @return array
     */
    private static function layer_default(int $number): array {
        return [
            'title' => get_string("aboutdefault_layer{$number}_title", 'theme_securefood'),
            'text' => get_string("aboutdefault_layer{$number}_text", 'theme_securefood'),
        ];
    }

    /**
     * One localised Learning for Change step.
     *
     * @param int $number Step number, 1..4.
     * @return array
     */
    private static function approach_step_default(int $number): array {
        return [
            'number' => get_string("aboutdefault_approach{$number}_number", 'theme_securefood'),
            'title' => get_string("aboutdefault_approach{$number}_title", 'theme_securefood'),
            'text' => get_string("aboutdefault_approach{$number}_text", 'theme_securefood'),
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
