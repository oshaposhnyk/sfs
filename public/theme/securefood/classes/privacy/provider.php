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
 * Privacy provider for theme_securefood.
 *
 * @package    theme_securefood
 * @copyright  2026 SecureFood School
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

declare(strict_types=1);

namespace theme_securefood\privacy;

use core_privacy\local\metadata\collection;
use core_privacy\local\request\writer;

/**
 * The theme stores three user preferences and no other personal data.
 */
final class provider implements
    \core_privacy\local\metadata\provider,
    \core_privacy\local\request\user_preference_provider {

    /**
     * Returns metadata about this plugin's data.
     *
     * @param collection $collection The initialised collection to add items to.
     * @return collection A listing of user data stored through this plugin.
     */
    public static function get_metadata(collection $collection): collection {
        $collection->add_user_preference(
            'theme_securefood_colourscheme',
            'privacy:metadata:preference:colourscheme'
        );
        $collection->add_user_preference(
            'theme_securefood_sidebar',
            'privacy:metadata:preference:sidebar'
        );
        $collection->add_user_preference(
            'theme_securefood_uimode',
            'privacy:metadata:preference:uimode'
        );
        return $collection;
    }

    /**
     * Export all user preferences for the plugin.
     *
     * @param int $userid The user id of the user whose data is to be exported.
     */
    public static function export_user_preferences(int $userid): void {
        $preferences = [
            'theme_securefood_colourscheme' => 'privacy:metadata:preference:colourscheme',
            'theme_securefood_sidebar' => 'privacy:metadata:preference:sidebar',
            'theme_securefood_uimode' => 'privacy:metadata:preference:uimode',
        ];
        foreach ($preferences as $name => $description) {
            $value = get_user_preferences($name, null, $userid);
            if ($value === null) {
                continue;
            }
            writer::export_user_preference(
                'theme_securefood',
                $name,
                $value,
                get_string($description, 'theme_securefood')
            );
        }
    }
}
