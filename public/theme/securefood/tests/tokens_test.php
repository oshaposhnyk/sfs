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
 * Unit tests for the design-token registry.
 *
 * @package    theme_securefood
 * @copyright  2026 SecureFood School
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

declare(strict_types=1);

namespace theme_securefood;

/**
 * Tests for {@see tokens}.
 *
 * @covers \theme_securefood\tokens
 */
final class tokens_test extends \basic_testcase {

    public function test_names_are_unique_and_translucent_subset(): void {
        $names = tokens::names();
        $this->assertSame($names, array_unique($names));
        foreach (tokens::translucent_dark() as $token) {
            $this->assertContains($token, $names);
        }
    }

    public function test_overrides_css_empty_when_no_settings(): void {
        $this->assertSame('', tokens::overrides_css(null));
        $this->assertSame('', tokens::overrides_css(new \stdClass()));
    }

    public function test_overrides_css_emits_only_configured_values(): void {
        $settings = new \stdClass();
        $settings->colour_primary = '#112233';
        $settings->colour_accent_dark = 'rgba(1, 2, 3, 0.5)';
        $settings->colour_bg = '   ';
        $settings->colour_unknown = '#FFFFFF';

        $css = tokens::overrides_css($settings);

        $this->assertStringContainsString(':root {', $css);
        $this->assertStringContainsString('--sfs-primary: #112233;', $css);
        $this->assertStringContainsString(':root[data-theme="dark"] {', $css);
        $this->assertStringContainsString('--sfs-accent: rgba(1, 2, 3, 0.5);', $css);
        $this->assertStringNotContainsString('--sfs-bg:', $css);
        $this->assertStringNotContainsString('unknown', $css);
    }

    public function test_overrides_css_dark_only(): void {
        $settings = new \stdClass();
        $settings->colour_ink_dark = '#FFFFFF';

        $css = tokens::overrides_css($settings);

        $this->assertStringNotContainsString(":root {\n", $css);
        $this->assertStringContainsString(':root[data-theme="dark"]', $css);
        $this->assertStringContainsString('--sfs-ink: #FFFFFF;', $css);
    }
}
