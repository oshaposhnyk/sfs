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
 * Unified settings hub per the product brief (Phase 7.3).
 *
 * Design cards linking the relevant core pages; profile facts are read from
 * core data (cohort from cohort membership, per the brief).
 *
 * @package    theme_securefood
 * @copyright  2026 SecureFood School
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

declare(strict_types=1);

require(__DIR__ . '/../../config.php');
require_once($CFG->dirroot . '/cohort/lib.php');

require_login(null, false);
if (isguestuser()) {
    throw new moodle_exception('noguest');
}

$PAGE->set_context(context_user::instance($USER->id));
$PAGE->set_url(new moodle_url('/theme/securefood/mysettings.php'));
$PAGE->set_pagelayout('standard');
$PAGE->set_title(get_string('settingshub', 'theme_securefood'));
$PAGE->set_heading(get_string('settingshub', 'theme_securefood'));

$cohorts = cohort_get_user_cohorts($USER->id);
$languages = get_string_manager()->get_list_of_translations();

$link = static fn(string $url, string $strkey): array => [
    'url' => (new moodle_url($url))->out(false),
    'label' => get_string($strkey, 'theme_securefood'),
];

$cards = [
    [
        'icon' => 'person',
        'title' => get_string('hub_profile', 'theme_securefood'),
        'facts' => [
            ['label' => get_string('fullname'), 'value' => fullname($USER)],
            ['label' => get_string('email'), 'value' => $USER->email],
            ['label' => get_string('cohort', 'cohort'),
                'value' => $cohorts ? format_string(reset($cohorts)->name) : '—'],
        ],
        'links' => [$link('/user/edit.php', 'hub_editprofile')],
    ],
    [
        'icon' => 'language',
        'title' => get_string('hub_language', 'theme_securefood'),
        'facts' => [
            ['label' => get_string('language'), 'value' => $languages[$USER->lang] ?? $USER->lang],
            ['label' => get_string('timezone'),
                'value' => core_date::get_localised_timezone((string)$USER->timezone)],
        ],
        'links' => [$link('/user/language.php', 'hub_changelanguage')],
    ],
    [
        'icon' => 'lock',
        'title' => get_string('hub_password', 'theme_securefood'),
        'facts' => [],
        'links' => [$link('/login/change_password.php', 'hub_changepassword')],
    ],
    [
        'icon' => 'palette',
        'title' => get_string('appearancetitle', 'theme_securefood'),
        'facts' => [
            ['label' => get_string('colourscheme', 'theme_securefood'),
                'value' => get_string('scheme_' . get_user_preferences('theme_securefood_colourscheme', 'system'),
                    'theme_securefood')],
        ],
        'links' => [$link('/theme/securefood/preferences.php', 'hub_appearance')],
    ],
    [
        'icon' => 'notifications',
        'title' => get_string('hub_notifications', 'theme_securefood'),
        'facts' => [],
        'links' => [$link('/message/notificationpreferences.php', 'hub_notificationprefs')],
    ],
    [
        'icon' => 'verified_user',
        'title' => get_string('hub_privacy', 'theme_securefood'),
        'facts' => [],
        'links' => [
            $link('/admin/tool/dataprivacy/mydatarequests.php', 'hub_datarequests'),
            $link('/user/preferences.php', 'hub_allpreferences'),
        ],
    ],
];

foreach ($cards as $i => $card) {
    $cards[$i]['hasfacts'] = $card['facts'] !== [];
}

echo $OUTPUT->header();
echo $OUTPUT->render_from_template('theme_securefood/settings_hub', [
    'lede' => get_string('settingshub_lede', 'theme_securefood'),
    'cards' => $cards,
]);
echo $OUTPUT->footer();
