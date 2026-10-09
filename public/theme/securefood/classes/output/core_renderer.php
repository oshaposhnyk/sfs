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
     * Add data-theme to <html> when the SecureFood mode is active.
     *
     * Stamps the scheme (light/dark/system) whenever SecureFood mode is active,
     * including on standard Boost drawer layouts (course/activity pages), so
     * dark tokens apply. Standard mode requests never inherit data-theme.
     *
     * @return string HTML attributes for the html element.
     */
    public function htmlattributes() {
        $attributes = parent::htmlattributes();

        $issfs = mode_manager::effective_mode() === mode_manager::MODE_SECUREFOOD;
        if (!$issfs) {
            // Pre-auth login page in standard mode: driven by cookie.
            if ($this->page->pagelayout === 'login') {
                $scheme = $_COOKIE['theme_securefood_loginscheme'] ?? 'system';
                if (!in_array($scheme, ['light', 'dark', 'system'], true)) {
                    $scheme = 'system';
                }
                $attributes .= ' data-theme="' . $scheme . '"';
            }
            return $attributes;
        }

        $scheme = 'system';
        if (isloggedin() && !isguestuser()) {
            $scheme = get_user_preferences('theme_securefood_colourscheme', 'system');
        }
        if (!in_array($scheme, ['light', 'dark', 'system'], true)) {
            $scheme = 'system';
        }
        $attributes .= ' data-theme="' . $scheme . '"';
        return $attributes;
    }

    /**
     * Ensure .sfs-mode class is stamped on body whenever SecureFood mode is active.
     *
     * Enables SecureFood token theming and typography across both the SFS shell
     * and the Boost drawers layout (Option 1 for course/activity pages).
     * Standard mode leaves body classes untouched.
     *
     * @param string[] $additionalclasses
     * @return string
     */
    public function body_attributes($additionalclasses = []) {
        if (mode_manager::effective_mode() === mode_manager::MODE_SECUREFOOD) {
            if (!in_array('sfs-mode', $additionalclasses, true)) {
                $additionalclasses[] = 'sfs-mode';
            }
        }
        return parent::body_attributes($additionalclasses);
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

        if (mode_manager::effective_mode() !== mode_manager::MODE_SECUREFOOD
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
     * Navbar control returning the user to SecureFood or Standard mode (ADR-002).
     *
     * Rendered in the Boost navbar. Shows "Standard" button when in SecureFood
     * mode to return to stock Boost, and "SFS" when in Standard mode. Empty when
     * inside the SFS shell (which has its own topbar toggle) or when switching is
     * disabled.
     *
     * @return string HTML fragment for the Boost navbar.
     */
    public function sfs_mode_switch(): string {
        if (!mode_manager::can_user_switch() || mode_manager::uses_shell($this->page)) {
            return '';
        }
        $currentmode = mode_manager::effective_mode();
        $targetmode = $currentmode === mode_manager::MODE_SECUREFOOD
            ? mode_manager::MODE_STANDARD
            : mode_manager::MODE_SECUREFOOD;
        try {
            $returnurl = $this->page->url->out_as_local_url(false);
        } catch (\moodle_exception $e) {
            $returnurl = '/';
        }
        $url = new \moodle_url('/theme/securefood/mode.php', [
            'mode' => $targetmode,
            'sesskey' => sesskey(),
            'returnurl' => $returnurl,
        ]);
        $label = $targetmode === mode_manager::MODE_STANDARD
            ? get_string('switchtostandard', 'theme_securefood')
            : get_string('switchtosecurefood', 'theme_securefood');
        $text = $targetmode === mode_manager::MODE_STANDARD ? 'Standard' : 'SFS';
        return \html_writer::link($url, $text, [
            'class' => 'nav-link px-2 fw-bold align-self-center',
            'title' => $label,
            'aria-label' => $label,
        ]);
    }
}
