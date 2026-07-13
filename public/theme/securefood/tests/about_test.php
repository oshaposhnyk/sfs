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
 * Tests for the About page context builder.
 *
 * @package    theme_securefood
 * @copyright  2026 SecureFood School
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

declare(strict_types=1);

namespace theme_securefood;

/**
 * Tests for {@see about}.
 *
 * @covers \theme_securefood\about
 */
final class about_test extends \advanced_testcase {

    public function test_default_context_contains_localised_approach(): void {
        $this->resetAfterTest();

        $context = about::context(new \stdClass());

        $this->assertTrue($context['showapproach']);
        $this->assertSame(get_string('aboutdefault_approachtitle', 'theme_securefood'),
            $context['approachtitle']);
        $this->assertCount(4, $context['approachsteps']);
        $this->assertSame(get_string('aboutdefault_approach1_number', 'theme_securefood'),
            $context['approachsteps'][0]['number']);
    }

    public function test_approach_can_be_customised_and_disabled(): void {
        $this->resetAfterTest();

        $settings = (object)[
            'aboutapproachtitle' => 'Custom approach',
            'aboutapproachsteps' => json_encode([[
                'number' => 'A',
                'title' => 'Observe',
                'text' => 'Use configured content.',
            ]]),
        ];
        $context = about::context($settings);

        $this->assertTrue($context['showapproach']);
        $this->assertSame('Custom approach', $context['approachtitle']);
        $this->assertSame([[
            'number' => 'A',
            'title' => 'Observe',
            'text' => 'Use configured content.',
        ]], $context['approachsteps']);

        $settings->showaboutapproach = 0;
        $context = about::context($settings);
        $this->assertFalse($context['showapproach']);
    }

    public function test_hub_map_positions_are_exported_as_safe_classes(): void {
        $this->resetAfterTest();

        $settings = (object)[
            'abouthubs' => json_encode([[
                'name' => 'NULES',
                'country' => 'Kyiv, Ukraine',
                'type' => 'lab',
                'lat' => 50.3850,
                'lon' => 30.4900,
            ]]),
        ];

        $context = about::context($settings);

        $this->assertTrue($context['hubs'][0]['onmap']);
        $this->assertSame('sfs-hub-1', $context['hubs'][0]['hubid']);
        $this->assertSame(79, $context['hubs'][0]['markerxclass']);
        $this->assertSame(41, $context['hubs'][0]['markeryclass']);
        $this->assertSame(50.3850, $context['hubs'][0]['lat']);
        $this->assertSame(30.4900, $context['hubs'][0]['lon']);
        $this->assertStringContainsString('NULES', $context['hubs'][0]['markerlabel']);
        $this->assertStringContainsString('Living Lab', $context['hubs'][0]['markerlabel']);
        $this->assertSame(get_string('aboutmaplabel', 'theme_securefood'), $context['maplabel']);
    }
}
