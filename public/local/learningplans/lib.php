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
 * Callback implementations for local_learningplans.
 *
 * @package    local_learningplans
 * @copyright  2026
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Extend global navigation with learning plans entry.
 *
 * @param global_navigation $navigation Navigation object.
 * @return void
 */
function local_learningplans_extend_navigation(global_navigation $navigation): void {
    if (!isloggedin() || isguestuser()) {
        return;
    }

    $context = context_system::instance();
    $canview = has_capability('local/learningplans:view', $context);
    $canviewprogress = has_capability('local/learningplans:viewprogress', $context);
    if (!$canview && !$canviewprogress) {
        return;
    }

    $root = $navigation->add(
        get_string('pluginname', 'local_learningplans'),
        new moodle_url('/local/learningplans/my.php'),
        navigation_node::TYPE_CUSTOM,
        null,
        'local_learningplans'
    );

    if ($canviewprogress) {
        $root->add(
            get_string('list:mytitle', 'local_learningplans'),
            new moodle_url('/local/learningplans/my.php'),
            navigation_node::TYPE_CUSTOM
        );
    }

    if ($canview) {
        $root->add(
            get_string('nav:manage', 'local_learningplans'),
            new moodle_url('/local/learningplans/index.php'),
            navigation_node::TYPE_CUSTOM
        );
    }
}

/**
 * Add learning plans management icon to top navbar for users with manage capability.
 *
 * @param renderer_base $renderer Page renderer.
 * @return string
 */
function local_learningplans_render_navbar_output(renderer_base $renderer): string {
    if (!isloggedin() || isguestuser()) {
        return '';
    }

    $context = context_system::instance();
    if (!has_capability('local/learningplans:manage', $context)) {
        return '';
    }

    $label = get_string('nav:manage', 'local_learningplans');
    $icon = $renderer->render(new pix_icon('i/course', $label, 'core'));
    $url = new moodle_url('/local/learningplans/index.php');

    $link = html_writer::link($url, $icon, [
        'class' => 'nav-link icon-no-margin local-learningplans__nav-link',
        'title' => $label,
        'aria-label' => $label,
    ]);

    return html_writer::tag(
        'div',
        $link,
        ['class' => 'local-learningplans__nav-item d-flex align-items-center']
    );
}
