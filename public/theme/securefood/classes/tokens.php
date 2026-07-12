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
 * Design-token registry and settings-driven CSS custom-property emission.
 *
 * @package    theme_securefood
 * @copyright  2026 SecureFood School
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

declare(strict_types=1);

namespace theme_securefood;

/**
 * Single source of truth for the SecureFood design tokens.
 *
 * The default values live in scss/_tokens.scss; this class only knows the
 * token names and turns admin colour overrides (ADR-007) into CSS
 * custom-property declarations appended after the compiled theme CSS.
 */
final class tokens {
    /**
     * Token names in emission order.
     *
     * Each token maps to the CSS custom property --sfs-<name> and to the two
     * admin settings colour_<name> (light) and colour_<name>_dark.
     *
     * @return string[]
     */
    public static function names(): array {
        return [
            'bg', 'bg2', 'surface', 'surface2',
            'ink', 'ink2', 'muted', 'muted2',
            'line', 'linestrong',
            'primary', 'primary700', 'primary50',
            'accent', 'accent50',
            'teal', 'teal50',
            'success', 'warn', 'danger',
        ];
    }

    /**
     * Tokens whose dark default is an rgba() tint rather than a solid hex.
     *
     * Their dark-variant setting is a free-text CSS colour instead of a
     * colour picker so admins can keep translucency.
     *
     * @return string[]
     */
    public static function translucent_dark(): array {
        return ['line', 'linestrong', 'primary50', 'accent50', 'teal50'];
    }

    /**
     * Build CSS custom-property overrides from theme settings.
     *
     * Only configured values are emitted; anything left empty falls through
     * to the defaults compiled from scss/_tokens.scss.
     *
     * @param \stdClass|null $settings Theme settings object (theme_config->settings).
     * @return string CSS (not SCSS-dependent, safe to append as extra SCSS).
     */
    public static function overrides_css(?\stdClass $settings): string {
        if ($settings === null) {
            return '';
        }

        $light = [];
        $dark = [];
        foreach (self::names() as $name) {
            $lightvalue = trim((string)($settings->{'colour_' . $name} ?? ''));
            if ($lightvalue !== '') {
                $light[] = '--sfs-' . $name . ': ' . $lightvalue . ';';
            }
            $darkvalue = trim((string)($settings->{'colour_' . $name . '_dark'} ?? ''));
            if ($darkvalue !== '') {
                $dark[] = '--sfs-' . $name . ': ' . $darkvalue . ';';
            }
        }

        $css = '';
        if ($light !== []) {
            $css .= ":root {\n    " . implode("\n    ", $light) . "\n}\n";
        }
        if ($dark !== []) {
            $darkblock = implode("\n    ", $dark);
            $css .= ":root[data-theme=\"dark\"] {\n    " . $darkblock . "\n}\n";
            // Scheme preference "system": dark overrides must also apply via
            // the OS preference when no explicit light choice is stamped.
            $css .= "@media (prefers-color-scheme: dark) {\n"
                . ":root:not([data-theme=\"light\"]) {\n    " . $darkblock . "\n}\n}\n";
        }
        return $css;
    }
}
