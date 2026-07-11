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

namespace local_learningplans\domain\value;

use local_learningplans\domain\exception\domain_exception;

defined('MOODLE_INTERNAL') || die();

/**
 * Enrolment mode value object.
 *
 * @package    local_learningplans
 * @copyright  2026
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class enrolment_mode {
    /** @var string */
    public const IMMEDIATE = 'immediate';

    /** @var string */
    public const SEQUENTIAL_RELEASE = 'sequentialrelease';

    /**
     * Validate mode.
     *
     * @param string $mode Mode value.
     * @return string
     */
    public static function normalize(string $mode): string {
        $mode = trim(strtolower($mode));
        if ($mode === self::IMMEDIATE || $mode === self::SEQUENTIAL_RELEASE) {
            return $mode;
        }

        throw new domain_exception('Invalid enrolment mode: ' . $mode);
    }
}
