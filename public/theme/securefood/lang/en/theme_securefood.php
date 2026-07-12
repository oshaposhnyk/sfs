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
 * English language strings for theme_securefood.
 *
 * @package    theme_securefood
 * @copyright  2026 SecureFood School
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

declare(strict_types=1);

defined('MOODLE_INTERNAL') || die();

$string['pluginname'] = 'SecureFood School';
$string['choosereadme'] = 'SecureFood School — a Boost child theme implementing the SecureFood design system, with a switch between the standard Moodle experience and the SecureFood custom mode.';
$string['configtitle'] = 'SecureFood School settings';
$string['region-side-pre'] = 'Right';

// Settings tabs.
$string['generaltab'] = 'General';
$string['navigationtab'] = 'Navigation';
$string['colourstab'] = 'Colours';
$string['advancedtab'] = 'Advanced';

// General tab.
$string['forcemode'] = 'Force experience mode';
$string['forcemode_desc'] = 'Force every user into one experience, or let each user choose between the standard Moodle interface and the SecureFood mode.';
$string['forcemode_userchoice'] = 'User choice';
$string['defaultmode'] = 'Default experience mode';
$string['defaultmode_desc'] = 'The experience users get before they make their own choice.';
$string['mode_standard'] = 'Standard Moodle';
$string['mode_securefood'] = 'SecureFood mode';
$string['switchmode'] = 'Switch experience mode';
$string['switchtostandard'] = 'Switch to the standard Moodle interface';
$string['switchtosecurefood'] = 'Switch to the SecureFood interface';

// Navigation tab.
$string['navigation'] = 'Sidebar navigation (JSON)';
$string['navigation_desc'] = 'Custom sidebar menu as a JSON array of sections. Leave empty for the default menu. Format: <pre>[{"label": "Learning", "items": [{"title": "Student Lab", "url": "/local/learningplans/my.php", "icon": "school", "visibility": "loggedin"}]}]</pre> "icon" is a Material Icons name; "visibility" is "all" or "loggedin". Invalid JSON falls back to the default menu.';

// Shell.
$string['sidebarnav'] = 'Main navigation';
$string['skiptocontent'] = 'Skip to main content';
$string['togglesidebar'] = 'Toggle sidebar';
$string['togglescheme'] = 'Toggle light/dark mode';
$string['searchplaceholder'] = 'Search courses, documents…';

// Default navigation items.
$string['nav_learning'] = 'Learning';
$string['nav_account'] = 'Account';
$string['nav_about'] = 'About the Project';
$string['nav_studentlab'] = 'Student Lab';
$string['nav_mycourses'] = 'My courses';
$string['nav_notifications'] = 'Notifications';
$string['nav_messages'] = 'Messages';
$string['nav_settings'] = 'Settings';

// Colours tab.
$string['colourintro'] = 'Design token colours';
$string['colourintro_desc'] = 'Every colour in the SecureFood interface comes from a design token. Leave a field empty to use the SecureFood default. Overrides apply to the SecureFood mode in both the light and dark colour schemes. When overriding, keep text/background pairs at a contrast ratio of at least 4.5:1 (WCAG 2.1 AA).';
$string['colourlight'] = '{$a} — light';
$string['colourdark'] = '{$a} — dark';
$string['colour_desc'] = 'CSS custom property <code>{$a}</code>. Leave empty for the SecureFood default.';
$string['colourdarkrgba_desc'] = 'CSS custom property <code>{$a}</code> (dark scheme). Accepts any CSS colour, including <code>rgba()</code> for translucency. Leave empty for the SecureFood default.';

// Token names.
$string['token_bg'] = 'Page background';
$string['token_bg2'] = 'Recessed background';
$string['token_surface'] = 'Surface';
$string['token_surface2'] = 'Surface (raised)';
$string['token_ink'] = 'Headings and primary text';
$string['token_ink2'] = 'Body text';
$string['token_muted'] = 'Secondary text';
$string['token_muted2'] = 'Placeholder text';
$string['token_line'] = 'Borders';
$string['token_linestrong'] = 'Borders (strong)';
$string['token_primary'] = 'Primary (teal)';
$string['token_primary700'] = 'Primary hover';
$string['token_primary50'] = 'Primary tint';
$string['token_accent'] = 'Accent (amber)';
$string['token_accent50'] = 'Accent tint';
$string['token_teal'] = 'Teal';
$string['token_teal50'] = 'Teal tint';
$string['token_success'] = 'Success';
$string['token_warn'] = 'Warning';
$string['token_danger'] = 'Danger';

// Advanced tab.
$string['rawscsspre'] = 'Raw initial SCSS';
$string['rawscsspre_desc'] = 'SCSS code injected before everything else. Use it to define variables the rest of the compilation can consume.';
$string['rawscss'] = 'Raw SCSS';
$string['rawscss_desc'] = 'SCSS code appended at the end of the stylesheet. It wins over all theme styles, including colour token overrides.';

// Privacy.
$string['privacy:metadata:preference:colourscheme'] = 'The colour scheme the user prefers: light, dark, or follow the operating system.';
$string['privacy:metadata:preference:sidebar'] = 'Whether the user keeps the SecureFood sidebar expanded or collapsed.';
$string['privacy:metadata:preference:uimode'] = 'Whether the user uses the standard Moodle experience or the SecureFood mode.';

// Pages & content tab.
$string['pagestab'] = 'Pages & content';
$string['aboutkicker'] = 'About page — kicker';
$string['abouttitle'] = 'About page — title';
$string['aboutlede'] = 'About page — intro text';
$string['aboutstats'] = 'About page — hero statistics (JSON)';
$string['aboutkpis'] = 'About page — KPI row (JSON)';
$string['aboutfallback_desc'] = 'Leave empty to use the SecureFood design copy.';
$string['aboutjson_desc'] = 'JSON list, e.g. [{"value": "14", "label": "Living Labs", "sub": "Optional note"}]. Leave empty for the design defaults; invalid JSON falls back to them.';
