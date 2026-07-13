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
 * Tests for SecureFood theme preferences and privacy exports.
 *
 * @package    theme_securefood
 * @copyright  2026 SecureFood School
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

declare(strict_types=1);

namespace theme_securefood;

use context_user;
use core_privacy\local\metadata\collection;
use core_privacy\local\metadata\types\user_preference;
use core_privacy\local\request\writer;
use theme_securefood\privacy\provider;

/**
 * Tests for the theme-owned user preferences and privacy provider.
 *
 * @covers \theme_securefood\privacy\provider
 */
final class preferences_privacy_test extends \core_privacy\tests\provider_testcase {

    /**
     * Load the theme callback file that defines user preferences.
     */
    private function require_theme_callbacks(): void {
        global $CFG;

        require_once($CFG->dirroot . '/theme/securefood/lib.php');
    }

    public function test_user_preference_definitions_are_strict(): void {
        $this->require_theme_callbacks();

        $preferences = \theme_securefood_user_preferences();

        $this->assertSame([
            'theme_securefood_colourscheme',
            'theme_securefood_sidebar',
            'theme_securefood_uimode',
        ], array_keys($preferences));

        $this->assertSame(PARAM_ALPHA, $preferences['theme_securefood_colourscheme']['type']);
        $this->assertSame(NULL_NOT_ALLOWED, $preferences['theme_securefood_colourscheme']['null']);
        $this->assertSame('system', $preferences['theme_securefood_colourscheme']['default']);
        $this->assertSame(['light', 'dark', 'system'],
            $preferences['theme_securefood_colourscheme']['choices']);

        $this->assertSame(PARAM_ALPHA, $preferences['theme_securefood_sidebar']['type']);
        $this->assertSame(NULL_NOT_ALLOWED, $preferences['theme_securefood_sidebar']['null']);
        $this->assertSame('expanded', $preferences['theme_securefood_sidebar']['default']);
        $this->assertSame(['expanded', 'collapsed'],
            $preferences['theme_securefood_sidebar']['choices']);

        $this->assertSame(PARAM_ALPHA, $preferences['theme_securefood_uimode']['type']);
        $this->assertSame(NULL_NOT_ALLOWED, $preferences['theme_securefood_uimode']['null']);
        $this->assertSame('securefood', $preferences['theme_securefood_uimode']['default']);
        $this->assertSame(['standard', 'securefood'],
            $preferences['theme_securefood_uimode']['choices']);

        foreach ($preferences as $definition) {
            $this->assertSame([\core_user::class, 'is_current_user'], $definition['permissioncallback']);
        }
    }

    public function test_privacy_metadata_declares_theme_preferences(): void {
        $collection = provider::get_metadata(new collection('theme_securefood'));
        $items = $collection->get_collection();

        $this->assertContainsOnlyInstancesOf(user_preference::class, $items);

        $metadata = [];
        foreach ($items as $item) {
            $metadata[$item->get_name()] = $item->get_summary();
        }

        $this->assertSame([
            'theme_securefood_colourscheme' => 'privacy:metadata:preference:colourscheme',
            'theme_securefood_sidebar' => 'privacy:metadata:preference:sidebar',
            'theme_securefood_uimode' => 'privacy:metadata:preference:uimode',
        ], $metadata);
    }

    public function test_export_user_preferences_exports_existing_values(): void {
        $this->resetAfterTest();
        writer::reset();

        $user = $this->getDataGenerator()->create_user();

        set_user_preference('theme_securefood_colourscheme', 'dark', $user);
        set_user_preference('theme_securefood_sidebar', 'collapsed', $user);
        set_user_preference('theme_securefood_uimode', 'standard', $user);

        provider::export_user_preferences((int)$user->id);

        $writer = writer::with_context(context_user::instance((int)$user->id));
        $this->assertTrue($writer->has_any_data());

        $exportedpreferences = $writer->get_user_preferences('theme_securefood');

        $this->assertCount(3, (array)$exportedpreferences);
        $this->assertSame('dark', $exportedpreferences->theme_securefood_colourscheme->value);
        $this->assertSame('collapsed', $exportedpreferences->theme_securefood_sidebar->value);
        $this->assertSame('standard', $exportedpreferences->theme_securefood_uimode->value);
        $this->assertSame(get_string('privacy:metadata:preference:colourscheme', 'theme_securefood'),
            $exportedpreferences->theme_securefood_colourscheme->description);
        $this->assertSame(get_string('privacy:metadata:preference:sidebar', 'theme_securefood'),
            $exportedpreferences->theme_securefood_sidebar->description);
        $this->assertSame(get_string('privacy:metadata:preference:uimode', 'theme_securefood'),
            $exportedpreferences->theme_securefood_uimode->description);
    }

    public function test_export_user_preferences_skips_missing_values(): void {
        $this->resetAfterTest();
        writer::reset();

        $user = $this->getDataGenerator()->create_user();

        provider::export_user_preferences((int)$user->id);

        $writer = writer::with_context(context_user::instance((int)$user->id));
        $this->assertFalse($writer->has_any_data());
    }
}
