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
 * Membership provenance value object.
 *
 * Tracks why a membership exists so that automatic (cohort) and explicit
 * (manual) enrolments never silently overwrite each other.
 *
 * @package    local_learningplans
 * @copyright  2026
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class membership_source {
    /** @var string Membership created by an administrator action. */
    public const MANUAL = 'manual';

    /** @var string Membership created automatically from a linked cohort. */
    public const COHORT = 'cohort';

    /**
     * Validate and normalise a source value.
     *
     * @param string $source Raw source value.
     * @return string
     */
    public static function normalize(string $source): string {
        $source = trim(strtolower($source));
        if ($source === self::MANUAL || $source === self::COHORT) {
            return $source;
        }

        throw new domain_exception('Invalid membership source: ' . $source);
    }
}