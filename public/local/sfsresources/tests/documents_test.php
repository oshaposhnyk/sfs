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
 * Tests for the documents registry.
 *
 * @package    local_sfsresources
 * @copyright  2026 SecureFood School
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

declare(strict_types=1);

namespace local_sfsresources;

/**
 * Tests for {@see documents}.
 *
 * @covers \local_sfsresources\documents
 */
final class documents_test extends \basic_testcase {

    public function test_kind_from_filename(): void {
        $this->assertSame('pdf', documents::kind_from_filename('Report.PDF'));
        $this->assertSame('doc', documents::kind_from_filename('template.docx'));
        $this->assertSame('xls', documents::kind_from_filename('audit.csv'));
        $this->assertSame('zip', documents::kind_from_filename('bundle.tar.gz'));
        $this->assertSame('link', documents::kind_from_filename('README'));
        $this->assertSame('link', documents::kind_from_filename('image.png'));
    }

    public function test_parse_falls_back_to_defaults(): void {
        $this->assertSame([], documents::defaults());
        $this->assertSame(documents::defaults(), documents::parse(null));
        $this->assertSame(documents::defaults(), documents::parse('nonsense'));
        $parsed = documents::parse('[{"title": "X", "kind": "weird"}]');
        $this->assertSame('link', $parsed[0]['kind']);
    }
}
