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
use theme_securefood\settings_provider;

/**
 * Extends Boost's renderer to stamp the colour-scheme attribute (ADR-004).
 */
class core_renderer extends \theme_boost\output\core_renderer {
    /**
     * Returns the configured SecureFood favicon, falling back to Moodle/core.
     *
     * @return \moodle_url|string The favicon URL.
     */
    public function favicon() {
        $favicon = settings_provider::from_theme_settings($this->page->theme->settings ?? null)
            ->theme_file_url($this->page->theme, 'favicon', 'favicon', '');
        if ($favicon !== '') {
            return $favicon;
        }

        return parent::favicon();
    }

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
     * Prepend the learner's plan context to the course content header.
     *
     * "Part of <plan> · Course N of M" chips plus the course progress bar,
     * fed by the local_learningplans read model (Phase 6.1). Falls through
     * silently when the plugin is absent or the course is in none of the
     * viewer's plans.
     *
     * @param bool $onlyifnotcalledbefore Core flag, passed through.
     * @return string HTML.
     */
    public function course_content_header($onlyifnotcalledbefore = false) {
        global $USER, $SITE;

        $output = parent::course_content_header($onlyifnotcalledbefore);

        if (!mode_manager::uses_shell($this->page)
                || $this->page->pagelayout !== 'course'
                || empty($this->page->course->id)
                || (int)$this->page->course->id === (int)$SITE->id
                || !isloggedin() || isguestuser()
                || !class_exists('\\local_learningplans\\infrastructure\\moodle\\factory\\learning_plan_service_factory')) {
            return $output;
        }

        $context = \local_learningplans\infrastructure\moodle\factory\learning_plan_service_factory
            ::course_plan_context()->execute((int)$this->page->course->id, (int)$USER->id);
        if ($context === null) {
            return $output;
        }

        return $this->render_from_template('theme_securefood/plan_context', [
            'planname' => format_string($context['planname']),
            'positionlabel' => get_string('plancontext_position', 'theme_securefood', [
                'position' => $context['position'],
                'total' => $context['total'],
            ]),
            'percentage' => $context['percentage'],
        ]) . $output;
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
