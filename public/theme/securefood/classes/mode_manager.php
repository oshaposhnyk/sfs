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
 * Standard/SecureFood mode resolution (ADR-002).
 *
 * @package    theme_securefood
 * @copyright  2026 SecureFood School
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

declare(strict_types=1);

namespace theme_securefood;

/**
 * Resolves which experience a request gets: stock Boost or the SecureFood shell.
 *
 * The pure {@see self::resolve()} contains the whole decision policy so it can
 * be unit-tested without Moodle; the other helpers are thin runtime wrappers.
 */
final class mode_manager {
    /** @var string Stock Boost experience. */
    public const MODE_STANDARD = 'standard';

    /** @var string SecureFood custom shell. */
    public const MODE_SECUREFOOD = 'securefood';

    /**
     * Page layouts that must always render stock Boost (safety, ADR-002).
     *
     * @var string[]
     */
    private const EXCLUDED_LAYOUTS = [
        'admin', 'maintenance', 'embedded', 'popup', 'frametop',
        'secure', 'login', 'redirect', 'print',
    ];

    /**
     * Pure mode resolution policy.
     *
     * Forced site-wide mode wins; then the user preference; then the site
     * default; SecureFood if nothing is configured.
     *
     * @param string|null $force Admin forcemode setting ('' = user choice).
     * @param string|null $default Admin default for users without a preference.
     * @param string|null $preference The user's uimode preference.
     * @return string One of the MODE_* constants.
     */
    public static function resolve(?string $force, ?string $default, ?string $preference): string {
        if (in_array($force, [self::MODE_STANDARD, self::MODE_SECUREFOOD], true)) {
            return $force;
        }
        if (in_array($preference, [self::MODE_STANDARD, self::MODE_SECUREFOOD], true)) {
            return $preference;
        }
        return $default === self::MODE_STANDARD ? self::MODE_STANDARD : self::MODE_SECUREFOOD;
    }

    /**
     * Effective mode for the current request.
     *
     * @return string One of the MODE_* constants.
     */
    public static function effective_mode(): string {
        $preference = null;
        if (isloggedin() && !isguestuser()) {
            $preference = get_user_preferences('theme_securefood_uimode', null);
        }
        return self::resolve(
            (string)get_config('theme_securefood', 'forcemode'),
            (string)get_config('theme_securefood', 'defaultmode'),
            $preference
        );
    }

    /**
     * Whether the current user may switch modes themselves.
     */
    public static function can_user_switch(): bool {
        $force = (string)get_config('theme_securefood', 'forcemode');
        return !in_array($force, [self::MODE_STANDARD, self::MODE_SECUREFOOD], true)
            && isloggedin() && !isguestuser();
    }

    /**
     * Should this page render inside the SecureFood shell?
     *
     * Excluded layouts and editing mode always fall back to Boost — editing
     * relies on Boost drawers/course index which the shell does not provide.
     *
     * @param \moodle_page $page The current page.
     */
    public static function uses_shell(\moodle_page $page): bool {
        if (in_array($page->pagelayout, self::EXCLUDED_LAYOUTS, true)) {
            return false;
        }
        if ($page->user_is_editing()) {
            return false;
        }
        return self::effective_mode() === self::MODE_SECUREFOOD;
    }
}
