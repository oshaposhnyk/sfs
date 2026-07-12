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
 * External functions (Phase 6.3, owner-approved).
 *
 * @package    local_learningplans
 * @copyright  2026
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

declare(strict_types=1);

defined('MOODLE_INTERNAL') || die();

$functions = [
    'local_learningplans_get_student_lab' => [
        'classname' => \local_learningplans\external\get_student_lab::class,
        'description' => 'Current user\'s Student Lab overview: plans, stages, courses, continue target (own data only).',
        'type' => 'read',
        'ajax' => true,
        'capabilities' => 'local/learningplans:viewprogress',
        'services' => [MOODLE_OFFICIAL_MOBILE_SERVICE],
    ],
    'local_learningplans_set_active_plan' => [
        'classname' => \local_learningplans\external\set_active_plan::class,
        'description' => 'Set the current user\'s active learning plan (must be an own membership).',
        'type' => 'write',
        'ajax' => true,
        'capabilities' => 'local/learningplans:viewprogress',
        'services' => [MOODLE_OFFICIAL_MOBILE_SERVICE],
    ],
];
