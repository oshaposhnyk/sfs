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
 * Admin setting for the SecureFood sidebar navigation JSON.
 *
 * @package    theme_securefood
 * @copyright  2026 SecureFood School
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

declare(strict_types=1);

namespace theme_securefood;

/**
 * Validates the settings-driven sidebar navigation model at save time.
 */
final class admin_setting_navigation extends \admin_setting_configtextarea {
    /**
     * Validate data before storage.
     *
     * @param string $data Submitted setting value.
     * @return mixed true when valid, otherwise a localised error string.
     */
    public function validate($data) {
        $parentvalidation = parent::validate($data);
        if ($parentvalidation !== true) {
            return $parentvalidation;
        }

        $json = trim((string)$data);
        if ($json === '') {
            return true;
        }

        $decoded = json_decode($json, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            return get_string('navigationjson_invalidjson', 'theme_securefood', json_last_error_msg());
        }
        if (!is_array($decoded)) {
            return get_string('navigationjson_rootinvalid', 'theme_securefood');
        }

        foreach (array_values($decoded) as $sectionindex => $section) {
            $sectionnumber = $sectionindex + 1;
            if (!is_array($section) || !isset($section['items']) || !is_array($section['items'])) {
                return get_string('navigationjson_sectioninvalid', 'theme_securefood', $sectionnumber);
            }

            foreach (array_values($section['items']) as $itemindex => $item) {
                $itemnumber = $itemindex + 1;
                $reference = (object)[
                    'section' => $sectionnumber,
                    'item' => $itemnumber,
                ];

                if (!is_array($item) || trim((string)($item['title'] ?? '')) === ''
                        || trim((string)($item['url'] ?? '')) === '') {
                    return get_string('navigationjson_iteminvalid', 'theme_securefood', $reference);
                }

                $visibility = (string)($item['visibility'] ?? 'all');
                if (!in_array($visibility, ['all', 'loggedin', 'capability'], true)) {
                    return get_string('navigationjson_visibilityinvalid', 'theme_securefood', $reference);
                }
                if ($visibility === 'capability') {
                    $capability = trim((string)($item['capability'] ?? ''));
                    if (!self::is_valid_capability($capability)) {
                        return get_string('navigationjson_capabilityinvalid', 'theme_securefood', $reference);
                    }
                }
            }
        }

        if (navigation::parse($json) === null) {
            return get_string('navigationjson_noitems', 'theme_securefood');
        }

        return true;
    }

    /**
     * Does the string look like a Moodle capability name?
     *
     * @param string $capability Candidate capability.
     * @return bool
     */
    private static function is_valid_capability(string $capability): bool {
        return preg_match('/^[a-z][a-z0-9_]*\/[a-z][a-z0-9_]*:[a-z][a-z0-9_]*$/', $capability) === 1;
    }
}
