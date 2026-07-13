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
 * Appearance preferences: colour scheme + experience mode, no JS required.
 *
 * Progressive-enhancement counterpart of the topbar toggles (domain 03).
 *
 * @package    theme_securefood
 * @copyright  2026 SecureFood School
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

declare(strict_types=1);

require(__DIR__ . '/../../config.php');

require_login(null, false);
if (isguestuser()) {
    throw new moodle_exception('noguest');
}

$url = new moodle_url('/theme/securefood/preferences.php');
$PAGE->set_context(context_user::instance($USER->id));
$PAGE->set_url($url);
$PAGE->set_pagelayout('standard');
$PAGE->set_title(get_string('appearancetitle', 'theme_securefood'));
$PAGE->set_heading(get_string('appearancetitle', 'theme_securefood'));

$settingsprovider = \theme_securefood\settings_provider::from_config();
$canswitchmode = \theme_securefood\mode_manager::can_user_switch();

// Save (PRG, sesskey-guarded).
if (optional_param('save', 0, PARAM_BOOL)) {
    require_sesskey();
    $scheme = required_param('colourscheme', PARAM_ALPHA);
    if (in_array($scheme, ['light', 'dark', 'system'], true)) {
        set_user_preference('theme_securefood_colourscheme', $scheme);
    }
    if ($canswitchmode) {
        $mode = required_param('uimode', PARAM_ALPHA);
        if (in_array($mode, ['standard', 'securefood'], true)) {
            set_user_preference('theme_securefood_uimode', $mode);
        }
    }
    redirect($url, get_string('changessaved'), null, \core\output\notification::NOTIFY_SUCCESS);
}

$scheme = get_user_preferences('theme_securefood_colourscheme', 'system');
$mode = get_user_preferences('theme_securefood_uimode', $settingsprovider->default_mode());

$schemeoptions = [];
foreach (['light', 'dark', 'system'] as $option) {
    $schemeoptions[] = [
        'value' => $option,
        'label' => get_string('scheme_' . $option, 'theme_securefood'),
        'checked' => $scheme === $option,
    ];
}
$modeoptions = [];
foreach (['securefood', 'standard'] as $option) {
    $modeoptions[] = [
        'value' => $option,
        'label' => get_string('mode_' . $option, 'theme_securefood'),
        'checked' => $mode === $option,
    ];
}

echo $OUTPUT->header();
echo $OUTPUT->render_from_template('theme_securefood/preferences', [
    'actionurl' => $url->out(false),
    'sesskey' => sesskey(),
    'schemeoptions' => $schemeoptions,
    'modeoptions' => $modeoptions,
    'canswitchmode' => $canswitchmode,
    'backurl' => (new moodle_url('/user/preferences.php'))->out(false),
]);
echo $OUTPUT->footer();
