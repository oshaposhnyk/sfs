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
 * Switch between the standard and SecureFood experiences (ADR-002).
 *
 * State-changing GET guarded by sesskey; no output.
 *
 * @package    theme_securefood
 * @copyright  2026 SecureFood School
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

declare(strict_types=1);

require(__DIR__ . '/../../config.php');

require_login(null, false);
require_sesskey();

$mode = required_param('mode', PARAM_ALPHA);
$returnurl = optional_param('returnurl', '/', PARAM_LOCALURL);

if (isguestuser()) {
    throw new moodle_exception('noguest');
}
if (!\theme_securefood\mode_manager::can_user_switch()) {
    throw new moodle_exception('nopermissions', 'error', '', get_string('switchmode', 'theme_securefood'));
}
if (!in_array($mode, [\theme_securefood\mode_manager::MODE_STANDARD, \theme_securefood\mode_manager::MODE_SECUREFOOD], true)) {
    throw new moodle_exception('invalidparameter', 'debug');
}

set_user_preference('theme_securefood_uimode', $mode);

redirect(new moodle_url($returnurl ?: '/'));
