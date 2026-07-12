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
 * Core renderer for theme_securefood.
 *
 * @package    theme_securefood
 * @copyright  2026 SecureFood School
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

declare(strict_types=1);

namespace theme_securefood\output;

use theme_securefood\mode_manager;

/**
 * Extends Boost's renderer to stamp the colour-scheme attribute (ADR-004).
 */
class core_renderer extends \theme_boost\output\core_renderer {

    /**
     * Add data-theme to <html> when the SecureFood shell is active.
     *
     * 'light' and 'dark' are stamped explicitly; 'system' omits the attribute
     * and the prefers-color-scheme fallback in _tokens.scss takes over.
     *
     * @return string HTML attributes for the html element.
     */
    public function htmlattributes() {
        $attributes = parent::htmlattributes();
        if (!mode_manager::uses_shell($this->page)) {
            return $attributes;
        }
        $scheme = 'system';
        if (isloggedin() && !isguestuser()) {
            $scheme = get_user_preferences('theme_securefood_colourscheme', 'system');
        }
        if ($scheme === 'light' || $scheme === 'dark') {
            $attributes .= ' data-theme="' . $scheme . '"';
        }
        return $attributes;
    }

    /**
     * Navbar control returning the user to SecureFood mode (ADR-002).
     *
     * Rendered by the theme's navbar override, i.e. only in standard mode;
     * the SecureFood topbar carries its own switch. Empty when the user is
     * not allowed to switch or is already in SecureFood mode.
     *
     * @return string HTML fragment for the Boost navbar.
     */
    public function sfs_mode_switch(): string {
        if (!mode_manager::can_user_switch()
                || mode_manager::effective_mode() === mode_manager::MODE_SECUREFOOD) {
            return '';
        }
        try {
            $returnurl = $this->page->url->out_as_local_url(false);
        } catch (\moodle_exception $e) {
            $returnurl = '/';
        }
        $url = new \moodle_url('/theme/securefood/mode.php', [
            'mode' => mode_manager::MODE_SECUREFOOD,
            'sesskey' => sesskey(),
            'returnurl' => $returnurl,
        ]);
        $label = get_string('switchtosecurefood', 'theme_securefood');
        return \html_writer::link($url, 'SFS', [
            'class' => 'nav-link px-2 fw-bold align-self-center',
            'title' => $label,
            'aria-label' => $label,
        ]);
    }
}
