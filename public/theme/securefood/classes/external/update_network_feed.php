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
 * External function: update the About-page network feed.
 *
 * @package    theme_securefood
 * @copyright  2026 SecureFood School
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

declare(strict_types=1);

namespace theme_securefood\external;

use core_external\external_api;
use core_external\external_function_parameters;
use core_external\external_multiple_structure;
use core_external\external_single_structure;
use core_external\external_value;
use theme_securefood\network_feed;
use theme_securefood\settings_provider;

defined('MOODLE_INTERNAL') || die();

/**
 * Replaces the small "Latest from the network" card feed.
 */
final class update_network_feed extends external_api {

    /**
     * Parameters.
     *
     * @return external_function_parameters
     */
    public static function execute_parameters(): external_function_parameters {
        return new external_function_parameters([
            'items' => new external_multiple_structure(new external_single_structure([
                'chip' => new external_value(PARAM_TEXT, 'Category/chip shown on the card', VALUE_DEFAULT, ''),
                'title' => new external_value(PARAM_TEXT, 'Card title', VALUE_REQUIRED),
                'text' => new external_value(PARAM_TEXT, 'Card body text', VALUE_DEFAULT, ''),
                'time' => new external_value(PARAM_TEXT, 'Human-readable time/status label', VALUE_DEFAULT, ''),
                'variant' => new external_value(PARAM_ALPHA, 'Visual variant: twin, culture, water or supply', VALUE_DEFAULT, ''),
                'url' => new external_value(PARAM_RAW, 'Optional http(s) or local Moodle URL for the card title', VALUE_DEFAULT, ''),
            ]), 'Network feed cards'),
        ]);
    }

    /**
     * Execute.
     *
     * @param array $items Feed cards.
     * @return array
     */
    public static function execute(array $items): array {
        $params = self::validate_parameters(self::execute_parameters(), ['items' => $items]);

        $context = \context_system::instance();
        self::validate_context($context);
        require_capability('moodle/site:config', $context);

        $provider = settings_provider::from_config();
        if (!$provider->enabled('aboutfeedwsenabled', false)) {
            throw new \moodle_exception('feedwsdisabled', 'theme_securefood');
        }

        $normalised = network_feed::normalise_items(
            $params['items'],
            network_feed::max_items($provider->text('aboutfeedwsmaxitems', '4'))
        );

        if ($params['items'] !== [] && $normalised === []) {
            throw new \invalid_parameter_exception('No valid feed items were supplied.');
        }

        network_feed::store($normalised);

        return [
            'status' => 'ok',
            'stored' => count($normalised),
            'items' => $normalised,
        ];
    }

    /**
     * Return description.
     *
     * @return external_single_structure
     */
    public static function execute_returns(): external_single_structure {
        return new external_single_structure([
            'status' => new external_value(PARAM_ALPHA, 'ok'),
            'stored' => new external_value(PARAM_INT, 'Number of stored cards'),
            'items' => new external_multiple_structure(new external_single_structure([
                'chip' => new external_value(PARAM_TEXT, 'Category/chip shown on the card'),
                'title' => new external_value(PARAM_TEXT, 'Card title'),
                'text' => new external_value(PARAM_TEXT, 'Card body text'),
                'time' => new external_value(PARAM_TEXT, 'Human-readable time/status label'),
                'variant' => new external_value(PARAM_ALPHA, 'Visual variant'),
                'url' => new external_value(PARAM_RAW, 'Optional card URL'),
            ])),
        ]);
    }
}
