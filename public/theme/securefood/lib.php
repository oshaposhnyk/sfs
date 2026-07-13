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
 * SecureFood School theme callbacks.
 *
 * Presentation-layer glue only — no business logic lives here.
 *
 * @package    theme_securefood
 * @copyright  2026 SecureFood School
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

declare(strict_types=1);

defined('MOODLE_INTERNAL') || die();

/**
 * Returns the main SCSS content: Boost preset + SecureFood layers.
 *
 * @param theme_config $theme The theme config object.
 * @return string
 */
function theme_securefood_get_main_scss_content($theme): string {
    global $CFG;

    // Boost default preset provides Bootstrap + Moodle core styles.
    $scss = file_get_contents($CFG->dirroot . '/theme/boost/scss/preset/default.scss');

    // SecureFood layers, composed in a fixed order.
    $layers = ['_fonts.scss', '_tokens.scss', 'post.scss'];
    foreach ($layers as $layer) {
        $scss .= "\n" . file_get_contents(__DIR__ . '/scss/' . $layer);
    }

    return $scss;
}

/**
 * SCSS to prepend before compilation (admin raw pre-SCSS only).
 *
 * @param theme_config $theme The theme config object.
 * @return string
 */
function theme_securefood_get_pre_scss($theme): string {
    return (string)($theme->settings->scsspre ?? '');
}

/**
 * SCSS to append after compilation input: settings-driven token overrides
 * (ADR-007) followed by admin raw SCSS so raw SCSS always wins.
 *
 * @param theme_config $theme The theme config object.
 * @return string
 */
function theme_securefood_get_extra_scss($theme): string {
    $scss = \theme_securefood\tokens::overrides_css($theme->settings ?? null);
    $scss .= "\n" . (string)($theme->settings->scss ?? '');
    return $scss;
}

/**
 * Serve files uploaded through the SecureFood theme settings.
 *
 * @param stdClass $course Course object.
 * @param stdClass|null $cm Course module object.
 * @param context $context File context.
 * @param string $filearea File area.
 * @param array $args Pluginfile path args.
 * @param bool $forcedownload Whether download is forced.
 * @param array $options Send-file options.
 * @return bool
 */
function theme_securefood_pluginfile(
    $course,
    $cm,
    $context,
    $filearea,
    $args,
    $forcedownload,
    array $options = []
) {
    $allowedfileareas = [
        'logofulllight',
        'logofulldark',
        'logoiconlight',
        'logoicondark',
        'favicon',
    ];

    if ($context->contextlevel === CONTEXT_SYSTEM && in_array($filearea, $allowedfileareas, true)) {
        $theme = theme_config::load('securefood');
        // Theme assets are safe to cache publicly; the theme revision changes
        // when settings files change.
        if (!array_key_exists('cacheability', $options)) {
            $options['cacheability'] = 'public';
        }

        return $theme->setting_file_serve($filearea, $args, $forcedownload, $options);
    }

    send_file_not_found();
}

/**
 * User preferences owned by the theme (persisted server-side, ADR-004).
 *
 * @return array[]
 */
function theme_securefood_user_preferences(): array {
    return [
        'theme_securefood_colourscheme' => [
            'type' => PARAM_ALPHA,
            'null' => NULL_NOT_ALLOWED,
            'default' => 'system',
            'choices' => ['light', 'dark', 'system'],
            'permissioncallback' => [core_user::class, 'is_current_user'],
        ],
        'theme_securefood_sidebar' => [
            'type' => PARAM_ALPHA,
            'null' => NULL_NOT_ALLOWED,
            'default' => 'expanded',
            'choices' => ['expanded', 'collapsed'],
            'permissioncallback' => [core_user::class, 'is_current_user'],
        ],
        'theme_securefood_uimode' => [
            'type' => PARAM_ALPHA,
            'null' => NULL_NOT_ALLOWED,
            'default' => 'securefood',
            'choices' => ['standard', 'securefood'],
            'permissioncallback' => [core_user::class, 'is_current_user'],
        ],
    ];
}

/**
 * Add the Appearance page to the user preferences hub.
 *
 * @param navigation_node $navigation The user settings node.
 * @param stdClass $user The user.
 * @param context $usercontext User context.
 * @param stdClass $course Course.
 * @param context $coursecontext Course context.
 * @return void
 */
function theme_securefood_extend_navigation_user_settings(
    navigation_node $navigation,
    stdClass $user,
    context $usercontext,
    stdClass $course,
    context $coursecontext
): void {
    global $USER;
    if ((int)$USER->id !== (int)$user->id || isguestuser()) {
        return;
    }
    $navigation->add(
        get_string('appearancetitle', 'theme_securefood'),
        new moodle_url('/theme/securefood/preferences.php'),
        navigation_node::TYPE_SETTING,
        null,
        'securefoodappearance'
    );
}
