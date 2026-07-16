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
 * Callbacks for local_sfsgame.
 *
 * @package    local_sfsgame
 * @copyright  2026 SecureFood School
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

declare(strict_types=1);

defined('MOODLE_INTERNAL') || die();

/**
 * Add the Future Food page to the global navigation with its own icon.
 *
 * @param global_navigation $navigation Global navigation.
 * @return void
 */
function local_sfsgame_extend_navigation(global_navigation $navigation): void {
    if (!isloggedin() || isguestuser()) {
        return;
    }

    $navigation->add(
        get_string('pluginname', 'local_sfsgame'),
        new moodle_url('/local/sfsgame/index.php'),
        navigation_node::TYPE_CUSTOM,
        null,
        'local_sfsgame',
        new pix_icon('icon', '', 'local_sfsgame')
    );
}

/**
 * Add a Future Food icon to the top navbar, next to the learning-plans one.
 *
 * @param renderer_base $renderer Page renderer.
 * @return string
 */
function local_sfsgame_render_navbar_output(renderer_base $renderer): string {
    if (!isloggedin() || isguestuser()) {
        return '';
    }

    $label = get_string('pluginname', 'local_sfsgame');
    $icon = $renderer->render(new pix_icon('icon', $label, 'local_sfsgame'));
    $url = new moodle_url('/local/sfsgame/index.php');

    $out = html_writer::link($url, $icon, [
        'class' => 'nav-link icon-no-margin local-sfsgame__nav-link',
        'title' => $label,
        'aria-label' => $label,
    ]);

    // Shortcut to where the achievements (site badges) are configured — only
    // for users who can manage badges (managers/admins), not learners.
    if (has_capability('moodle/badges:createbadge', context_system::instance())) {
        $managelabel = get_string('nav:manageachievements', 'local_sfsgame');
        $manageicon = $renderer->render(new pix_icon('i/badge', $managelabel));
        $out .= html_writer::link(
            // type=1 is BADGE_TYPE_SITE (badgeslib is not loaded on every page).
            new moodle_url('/badges/index.php', ['type' => 1]),
            $manageicon,
            [
                'class' => 'nav-link icon-no-margin local-sfsgame__nav-link',
                'title' => $managelabel,
                'aria-label' => $managelabel,
            ]
        );
    }

    return html_writer::tag(
        'div',
        $out,
        ['class' => 'local-sfsgame__nav-item d-flex align-items-center']
    );
}
