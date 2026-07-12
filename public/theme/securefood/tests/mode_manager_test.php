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
 * Unit tests for the mode resolution policy.
 *
 * @package    theme_securefood
 * @copyright  2026 SecureFood School
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

declare(strict_types=1);

namespace theme_securefood;

/**
 * Tests for {@see mode_manager::resolve()}.
 *
 * @covers \theme_securefood\mode_manager
 */
final class mode_manager_test extends \basic_testcase {

    /**
     * @dataProvider resolve_provider
     */
    public function test_resolve(?string $force, ?string $default, ?string $pref, string $expected): void {
        $this->assertSame($expected, mode_manager::resolve($force, $default, $pref));
    }

    /**
     * Data provider for test_resolve.
     *
     * @return array[]
     */
    public static function resolve_provider(): array {
        return [
            'force standard beats preference' => ['standard', 'securefood', 'securefood', 'standard'],
            'force securefood beats preference' => ['securefood', 'standard', 'standard', 'securefood'],
            'invalid force ignored' => ['banana', 'standard', 'securefood', 'securefood'],
            'preference wins over default' => ['', 'securefood', 'standard', 'standard'],
            'default used without preference' => ['', 'standard', null, 'standard'],
            'securefood when nothing set' => ['', '', null, 'securefood'],
            'invalid preference falls to default' => [null, 'standard', 'weird', 'standard'],
            'invalid everything is securefood' => [null, null, null, 'securefood'],
        ];
    }
}
