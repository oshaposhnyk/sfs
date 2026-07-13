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
 * Typed reader for SecureFood theme settings (ADR-007).
 *
 * @package    theme_securefood
 * @copyright  2026 SecureFood School
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

declare(strict_types=1);

namespace theme_securefood;

/**
 * Centralises reads from theme_securefood configuration.
 *
 * This keeps Moodle config access at the boundary and gives layout/rendering
 * code explicit, typed fallbacks. Empty admin settings always fall back to the
 * design defaults supplied by callers.
 */
final class settings_provider {
    /** @var string Stock Boost experience. */
    private const MODE_STANDARD = 'standard';

    /** @var string SecureFood custom shell. */
    private const MODE_SECUREFOOD = 'securefood';

    /** @var \stdClass Theme settings object. */
    private \stdClass $settings;

    /**
     * @param \stdClass|null $settings Theme settings object.
     */
    private function __construct(?\stdClass $settings) {
        $this->settings = $settings ?? new \stdClass();
    }

    /**
     * Build from persisted plugin configuration.
     *
     * @return self
     */
    public static function from_config(): self {
        $settings = get_config('theme_securefood');

        return new self($settings instanceof \stdClass ? $settings : null);
    }

    /**
     * Build from the already-loaded theme settings object.
     *
     * @param \stdClass|null $settings Theme settings object.
     * @return self
     */
    public static function from_theme_settings(?\stdClass $settings): self {
        return new self($settings);
    }

    /**
     * Return the underlying settings object for legacy adapters not yet ported.
     *
     * @return \stdClass
     */
    public function settings(): \stdClass {
        return $this->settings;
    }

    /**
     * Read a trimmed text setting.
     *
     * @param string $name Setting name without plugin prefix.
     * @param string $default Fallback when the setting is empty.
     * @return string
     */
    public function text(string $name, string $default = ''): string {
        $value = trim((string)($this->settings->{$name} ?? ''));

        return $value !== '' ? $value : $default;
    }

    /**
     * Read a raw setting value, preserving internal whitespace.
     *
     * @param string $name Setting name without plugin prefix.
     * @param string $default Fallback when the setting is empty.
     * @return string
     */
    public function raw(string $name, string $default = ''): string {
        $value = (string)($this->settings->{$name} ?? '');

        return trim($value) !== '' ? $value : $default;
    }

    /**
     * Read an HTML setting value.
     *
     * Rendering code must still pass this through Moodle's format_text().
     *
     * @param string $name Setting name without plugin prefix.
     * @param string $default Fallback when the setting is empty.
     * @return string
     */
    public function html(string $name, string $default = ''): string {
        return $this->raw($name, $default);
    }

    /**
     * Read a checkbox/boolean setting.
     *
     * @param string $name Setting name without plugin prefix.
     * @param bool $default Fallback when the setting has not been saved.
     * @return bool
     */
    public function enabled(string $name, bool $default = true): bool {
        $value = trim((string)($this->settings->{$name} ?? ''));
        if ($value === '') {
            return $default;
        }

        return (int)$value === 1;
    }

    /**
     * URL for a file uploaded through admin_setting_configstoredfile.
     *
     * @param \core\output\theme_config $theme Loaded theme config.
     * @param string $setting Setting name without plugin prefix.
     * @param string $filearea File area used by the stored-file setting.
     * @param string $default Fallback URL when no custom file is configured.
     * @return string
     */
    public function theme_file_url(
        \core\output\theme_config $theme,
        string $setting,
        string $filearea,
        string $default
    ): string {
        $url = $theme->setting_file_url($setting, $filearea);

        return $url !== null && $url !== '' ? $url : $default;
    }

    /**
     * Read a URL setting.
     *
     * @param string $name Setting name without plugin prefix.
     * @return string|null Clean URL or null when empty/invalid.
     */
    public function url(string $name): ?string {
        $value = $this->text($name);
        if ($value === '') {
            return null;
        }

        $clean = clean_param($value, PARAM_URL);

        return $clean !== '' ? $clean : null;
    }

    /**
     * Brand display name used by the shell.
     *
     * @param string $default Site short name fallback.
     * @return string
     */
    public function brand_name(string $default): string {
        return $this->text('brandname', $default);
    }

    /**
     * Optional topbar help URL.
     *
     * @return string|null
     */
    public function help_url(): ?string {
        return $this->url('helpurl');
    }

    /**
     * Raw navigation JSON setting.
     *
     * @return string|null
     */
    public function navigation_json(): ?string {
        $json = $this->raw('navigation');

        return $json !== '' ? $json : null;
    }

    /**
     * Optional forced mode setting.
     *
     * @return string|null One of the mode constants or null for user choice.
     */
    public function force_mode(): ?string {
        $mode = $this->text('forcemode');

        return self::is_mode($mode) ? $mode : null;
    }

    /**
     * Default mode for users without a preference.
     *
     * @return string One of the mode constants.
     */
    public function default_mode(): string {
        $mode = $this->text('defaultmode');

        return $mode === self::MODE_STANDARD ? self::MODE_STANDARD : self::MODE_SECUREFOOD;
    }

    /**
     * Is the supplied value a supported experience mode?
     *
     * @param string $mode Candidate mode.
     * @return bool
     */
    private static function is_mode(string $mode): bool {
        return in_array($mode, [self::MODE_STANDARD, self::MODE_SECUREFOOD], true);
    }
}
