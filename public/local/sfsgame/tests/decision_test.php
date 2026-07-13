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
 * Tests for the decision registry.
 *
 * @package    local_sfsgame
 * @copyright  2026 SecureFood School
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

declare(strict_types=1);

namespace local_sfsgame;

/**
 * Tests for {@see decision}.
 *
 * @covers \local_sfsgame\decision
 */
final class decision_test extends \basic_testcase {

    public function test_unconfigured_registry_is_empty(): void {
        $this->assertSame([], decision::defaults());
        $this->assertSame([], decision::parse(null));
        $this->assertSame([], decision::parse('invalid json'));
    }

    public function test_parse_normalises_configured_choices(): void {
        $choices = decision::parse(json_encode([
            [
                'label' => 'Reject & report',
                'icon' => 'verified_user',
                'note' => 'Open the real quiz.',
                'url' => '/mod/quiz/view.php?id=10',
            ],
            [
                'label' => ' ',
                'url' => '/mod/quiz/view.php?id=11',
            ],
            [
                'label' => 'Invalid',
                'url' => 'not a url',
            ],
        ]));

        $this->assertCount(1, $choices);
        $this->assertSame('Reject & report', $choices[0]['label']);
        $this->assertSame('verified_user', $choices[0]['icon']);
        $this->assertSame('Open the real quiz.', $choices[0]['note']);
        $this->assertSame('/mod/quiz/view.php?id=10', $choices[0]['url']);
    }
}
