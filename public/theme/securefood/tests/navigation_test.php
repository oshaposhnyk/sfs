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
 * Unit tests for the navigation JSON parser.
 *
 * @package    theme_securefood
 * @copyright  2026 SecureFood School
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

declare(strict_types=1);

namespace theme_securefood;

/**
 * Tests for {@see navigation::parse()}.
 *
 * @covers \theme_securefood\navigation
 */
final class navigation_test extends \basic_testcase {

    public function test_parse_rejects_invalid_input(): void {
        $this->assertNull(navigation::parse(null));
        $this->assertNull(navigation::parse(''));
        $this->assertNull(navigation::parse('not json'));
        $this->assertNull(navigation::parse('{"label": "no items"}'));
        $this->assertNull(navigation::parse('[{"label": "empty", "items": []}]'));
        $this->assertNull(navigation::parse('[{"items": [{"title": "No URL"}]}]'));
    }

    public function test_parse_normalises_items(): void {
        $json = json_encode([
            [
                'label' => 'Learning',
                'items' => [
                    ['title' => 'Lab', 'url' => '/local/learningplans/my.php',
                        'icon' => 'sch@o-o=l', 'visibility' => 'loggedin'],
                    ['title' => 'About', 'url' => '/'],
                ],
            ],
        ]);

        $sections = navigation::parse($json);

        $this->assertCount(1, $sections);
        $this->assertSame('Learning', $sections[0]['label']);
        $this->assertSame('school', $sections[0]['items'][0]['icon']);
        $this->assertSame('loggedin', $sections[0]['items'][0]['visibility']);
        $this->assertSame('all', $sections[0]['items'][1]['visibility']);
        $this->assertSame('circle', navigation::parse(
            '[{"items": [{"title": "X", "url": "/"}]}]'
        )[0]['items'][0]['icon'] ?? 'circle');
    }

    public function test_defaults_have_required_keys(): void {
        foreach (navigation::defaults() as $section) {
            $this->assertArrayHasKey('labelstr', $section);
            $this->assertNotEmpty($section['items']);
            foreach ($section['items'] as $item) {
                $this->assertArrayHasKey('titlestr', $item);
                $this->assertArrayHasKey('url', $item);
                $this->assertArrayHasKey('icon', $item);
            }
        }
    }
}
