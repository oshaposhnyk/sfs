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
$string['region-content-top'] = 'Content top';
$string['region-content-bottom'] = 'Content bottom';

// Settings tabs.
$string['generaltab'] = 'General';
$string['brandtab'] = 'Brand';
$string['navigationtab'] = 'Navigation';
$string['blockstab'] = 'Blocks';
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

// Brand tab.
$string['brandname'] = 'Site display name';
$string['brandname_desc'] = 'Name shown in the SecureFood shell. Leave empty to use the Moodle site short name.';
$string['brandlogoheading'] = 'Logo assets';
$string['brandlogoheading_desc'] = 'Upload custom SecureFood shell logos. Leave any field empty to use the bundled design asset. Uploaded logos accept PNG/JPG only; bundled SVG defaults remain available.';
$string['logofulllight'] = 'Full logo — light scheme';
$string['logofulllight_desc'] = 'Full-width sidebar logo used in the light colour scheme. Recommended: transparent PNG.';
$string['logofulldark'] = 'Full logo — dark scheme';
$string['logofulldark_desc'] = 'Full-width sidebar logo used in the dark colour scheme. Recommended: transparent PNG.';
$string['logoiconlight'] = 'Icon logo — light scheme';
$string['logoiconlight_desc'] = 'Compact sidebar logo used when the shell is collapsed in the light colour scheme.';
$string['logoicondark'] = 'Icon logo — dark scheme';
$string['logoicondark_desc'] = 'Compact sidebar logo used when the shell is collapsed in the dark colour scheme.';
$string['favicon'] = 'Favicon';
$string['favicon_desc'] = 'Optional browser favicon for the SecureFood theme. Accepts ICO or PNG. Leave empty to use the Moodle/core favicon fallback.';

// Navigation tab.
$string['navigation'] = 'Sidebar navigation (JSON)';
$string['navigation_desc'] = 'Custom sidebar menu as a JSON array of sections. Leave empty for the default menu. Format: <pre>[{"label": "Learning", "items": [{"title": "Student Lab", "url": "/local/learningplans/my.php", "icon": "school", "visibility": "loggedin"}, {"title": "Admin", "url": "/admin/search.php", "icon": "admin_panel_settings", "visibility": "capability", "capability": "moodle/site:config"}]}]</pre> "icon" is a Material Icons name; "visibility" is "all", "loggedin" or "capability". Invalid JSON falls back to the default menu at runtime but cannot be saved in the settings form.';
$string['navigationjson_invalidjson'] = 'Navigation JSON is invalid: {$a}';
$string['navigationjson_rootinvalid'] = 'Navigation JSON must be an array of sections.';
$string['navigationjson_sectioninvalid'] = 'Navigation section {$a} must be an object with an items array.';
$string['navigationjson_iteminvalid'] = 'Navigation item {$a->item} in section {$a->section} must include title and url.';
$string['navigationjson_visibilityinvalid'] = 'Navigation item {$a->item} in section {$a->section} has an unsupported visibility value. Use all, loggedin or capability.';
$string['navigationjson_capabilityinvalid'] = 'Navigation item {$a->item} in section {$a->section} uses capability visibility but does not provide a valid Moodle capability.';
$string['navigationjson_noitems'] = 'Navigation JSON must contain at least one usable item.';

// Blocks tab.
$string['showblockcontenttop'] = 'Show content-top block region';
$string['showblockcontenttop_desc'] = 'Allow blocks placed in the content-top region to render above the main page content in SecureFood mode.';
$string['showblockside'] = 'Show right block region';
$string['showblockside_desc'] = 'Allow blocks placed in the side-pre region to render as the right rail in SecureFood mode.';
$string['showblockcontentbottom'] = 'Show content-bottom block region';
$string['showblockcontentbottom_desc'] = 'Allow blocks placed in the content-bottom region to render below the main page content in SecureFood mode.';

// Shell.
$string['sidebarnav'] = 'Main navigation';
$string['skiptocontent'] = 'Skip to main content';
$string['togglesidebar'] = 'Toggle sidebar';
$string['togglescheme'] = 'Cycle colour scheme';
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
$string['customfooter'] = 'Custom footer';
$string['footerhtml'] = 'Footer HTML';
$string['footerhtml_desc'] = 'Optional footer content shown in the SecureFood shell above the standard Moodle footer. The content is processed through Moodle text filters.';
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
$string['showaboutpage'] = 'Show About page content';
$string['showaboutpage_desc'] = 'Display the SecureFood About/front-page content before the standard Moodle front-page content.';
$string['showaboutstats'] = 'Show About hero statistics';
$string['showaboutstats_desc'] = 'Display the statistics row under the About hero.';
$string['showaboutlayers'] = 'Show About shield and layers';
$string['showaboutlayers_desc'] = 'Display the shield card and reinforcing layers section on the About page.';
$string['showabouthubs'] = 'Show About hubs map';
$string['showabouthubs_desc'] = 'Display the Living Labs and partners map/list section on the About page.';
$string['showaboutfeed'] = 'Show About updates feed';
$string['showaboutfeed_desc'] = 'Display the updates feed cards on the About page.';
$string['aboutkicker'] = 'About page — kicker';
$string['abouttitle'] = 'About page — title';
$string['aboutlede'] = 'About page — intro text';
$string['aboutstats'] = 'About page — hero statistics (JSON)';
$string['aboutkpis'] = 'About page — KPI row (JSON)';
$string['aboutfallback_desc'] = 'Leave empty to use the SecureFood design copy.';
$string['aboutjson_desc'] = 'JSON list, e.g. [{"value": "14", "label": "Living Labs", "sub": "Optional note"}]. Leave empty for the design defaults; invalid JSON falls back to them.';
$string['aboutfeedtitle'] = 'About page — feed heading';
$string['aboutfeed'] = 'About page — updates feed (JSON)';
$string['aboutfeed_desc'] = 'JSON list, e.g. [{"chip": "Digital twin", "title": "…", "text": "…", "time": "2 days ago"}]. Leave empty for the design defaults.';
$string['abouthubstitle'] = 'About page — hubs heading';
$string['abouthubs'] = 'About page — Living Labs & partners (JSON)';
$string['abouthubs_desc'] = 'JSON list, e.g. [{"name": "Kyiv Lab", "country": "Ukraine", "type": "lab"}]. "type" is "lab" or "partner". Leave empty for the design defaults.';
$string['abouthub_lab'] = 'Living Lab';
$string['abouthub_partner'] = 'Partner';
$string['abouthubmarkerlabel'] = '{$a->name}, {$a->country}, {$a->status}. Jump to hub details.';
$string['nav_futurefood'] = 'Future Food';
$string['nav_resources'] = 'Resources & Standards';
$string['appearancetitle'] = 'SecureFood appearance';
$string['colourscheme'] = 'Colour scheme';
$string['scheme_light'] = 'Light';
$string['scheme_dark'] = 'Dark';
$string['scheme_system'] = 'Follow the system';
$string['allpreferences'] = 'All preferences';
$string['plancontext_position'] = 'Course {$a->position} of {$a->total}';
$string['plancontext_progress'] = 'Course progress';
$string['settingshub'] = 'Settings';
$string['settingshub_lede'] = 'Your account, appearance and privacy — everything in one place.';
$string['hub_profile'] = 'Profile';
$string['hub_editprofile'] = 'Edit profile';
$string['hub_language'] = 'Language & region';
$string['hub_changelanguage'] = 'Preferred language';
$string['hub_password'] = 'Password';
$string['hub_changepassword'] = 'Change password';
$string['hub_appearance'] = 'Appearance settings';
$string['hub_notifications'] = 'Notifications';
$string['hub_notificationprefs'] = 'Notification preferences';
$string['hub_privacy'] = 'Privacy';
$string['hub_datarequests'] = 'My data requests';
$string['hub_allpreferences'] = 'All preferences';
$string['abouttitleaccent'] = 'About page — title accent words';
$string['aboutshieldchip'] = 'About page — shield section chip';
$string['aboutshieldtitle'] = 'About page — shield section title';
$string['aboutshieldbody'] = 'About page — shield section body (HTML)';
$string['aboutlayerstitle'] = 'About page — layers heading';
$string['aboutlayers'] = 'About page — reinforcing layers (JSON)';
$string['showaboutapproach'] = 'Show Learning for Change section';
$string['showaboutapproach_desc'] = 'Display the Learning for Change approach and its four-step loop on the About page.';
$string['aboutapproachkicker'] = 'About page — approach kicker';
$string['aboutapproachtitle'] = 'About page — approach title';
$string['aboutapproachbody'] = 'About page — approach body';
$string['aboutapproachsteps'] = 'About page — approach steps (JSON)';
$string['aboutapproachsteps_desc'] = 'JSON list, e.g. [{"number":"01 · Reflect","title":"See the system","text":"…"}]. Leave empty for the localised design defaults.';
$string['aboutdefault_kicker'] = 'SecureFood School';
$string['aboutdefault_title'] = 'Learning for resilience in an';
$string['aboutdefault_titleaccent'] = 'evolving world';
$string['aboutdefault_lede'] = 'The educational hub of the SecureFood project — turning complex science and digital tools into actionable skills for the professionals, policymakers and change-makers building tomorrow\'s food systems.';
$string['aboutdefault_shieldchip'] = 'About the SecureFood project · Horizon Europe';
$string['aboutdefault_shieldtitle'] = 'A shield for the food systems of tomorrow';
$string['aboutdefault_shieldbody'] = '<p>Our world is facing unprecedented challenges — from climate shocks to global supply-chain disruptions. SecureFood is developing a "shield" for the food systems of tomorrow by integrating digital twins, governance frameworks, and community-led Living Labs into one operational ecosystem.</p><p>The School was established as a <strong>core flagship result of the project</strong>: its goal is to transform that science into skills that real people can use in real schools, kitchens and supply chains.</p>';
$string['aboutdefault_layerstitle'] = 'Three reinforcing layers';
$string['aboutdefault_layer1_title'] = 'Digital Twins';
$string['aboutdefault_layer1_text'] = 'Model stress scenarios in virtual environments to predict impacts before they hit the field.';
$string['aboutdefault_layer2_title'] = 'Governance frameworks';
$string['aboutdefault_layer2_text'] = 'New standards for crisis management and policy-making across European food chains.';
$string['aboutdefault_layer3_title'] = 'Living Labs';
$string['aboutdefault_layer3_text'] = 'Community-led spaces where innovative solutions are tested in real-world conditions.';
$string['aboutdefault_approachkicker'] = 'Our approach · L4C';
$string['aboutdefault_approachtitle'] = 'Learning for Change';
$string['aboutdefault_approachbody'] = '<p>We go beyond traditional theory. <strong>Transformative Learning (L4C)</strong> triggers critical reflection on existing systems and fosters collective innovation. The result: <strong>agents of change</strong> capable of navigating and leading through crises — not just describing them.</p>';
$string['aboutdefault_approach1_number'] = '01 · Reflect';
$string['aboutdefault_approach1_title'] = 'See the system';
$string['aboutdefault_approach1_text'] = 'Map your context, identify pressure points and stakeholders honestly.';
$string['aboutdefault_approach2_number'] = '02 · Translate';
$string['aboutdefault_approach2_title'] = 'Connect evidence';
$string['aboutdefault_approach2_text'] = 'Turn sensor data and research into language your community understands.';
$string['aboutdefault_approach3_number'] = '03 · Co-design';
$string['aboutdefault_approach3_title'] = 'Build together';
$string['aboutdefault_approach3_text'] = 'Prototype solutions inside a Living Lab with mentors and peers.';
$string['aboutdefault_approach4_number'] = '04 · Act';
$string['aboutdefault_approach4_title'] = 'Lead the change';
$string['aboutdefault_approach4_text'] = 'Validate in the field and feed findings back into governance.';
$string['aboutdefault_hubstitle'] = 'Living Labs & partners across Europe';
$string['aboutdefault_feedtitle'] = 'Latest from the network';
$string['aboutdefault_feed1_chip'] = 'Digital twin';
$string['aboutdefault_feed1_title'] = 'Kyiv Living Lab twin goes live';
$string['aboutdefault_feed1_text'] = 'Real-time mirroring of the cold-chain sensors is now open to learners.';
$string['aboutdefault_feed1_time'] = '2 days ago';
$string['aboutdefault_feed2_chip'] = 'Culture';
$string['aboutdefault_feed2_title'] = 'School kitchens join the pilot';
$string['aboutdefault_feed2_text'] = 'Three school kitchens start logging temperature and hygiene data.';
$string['aboutdefault_feed2_time'] = '5 days ago';
$string['aboutdefault_feed3_chip'] = 'Water';
$string['aboutdefault_feed3_title'] = 'Irrigation quality module updated';
$string['aboutdefault_feed3_text'] = 'New thresholds aligned with the latest EFSA guidance.';
$string['aboutdefault_feed3_time'] = '1 week ago';
$string['aboutdefault_feed4_chip'] = 'Supply chain';
$string['aboutdefault_feed4_title'] = 'Route risk workshop recap';
$string['aboutdefault_feed4_text'] = 'Learners mapped disruption scenarios across two supply corridors.';
$string['aboutdefault_feed4_time'] = '2 weeks ago';
$string['aboutdefault_stat_horizon_value'] = '€8M';
$string['aboutdefault_stat_horizon_label'] = 'Horizon Europe';
$string['aboutdefault_stat_partners_value'] = '21';
$string['aboutdefault_stat_partners_label'] = 'Partners';
$string['aboutdefault_stat_labs_value'] = '10+';
$string['aboutdefault_stat_labs_label'] = 'Living Labs';
$string['aboutdefault_stat_method_value'] = 'L4C';
$string['aboutdefault_stat_method_label'] = 'Methodology';
$string['rail_aria'] = 'Course overview';
$string['rail_nextup'] = 'Next up';
$string['rail_continue'] = 'Continue';
$string['rail_courseinfo'] = 'Course info';
$string['rail_teachers'] = 'Teachers';
$string['rail_teacher'] = 'Teacher';
$string['rail_sections'] = 'Sections';
$string['rail_activities'] = 'Activities';
$string['rail_progress'] = 'Progress';
$string['rail_language'] = 'Language';
$string['rail_format'] = 'Format';
$string['rail_complete'] = 'Complete';
$string['rail_inprogress'] = 'In progress';
$string['rail_upnext'] = 'Up next';
$string['helpurl'] = 'Help link';
$string['helpurl_desc'] = 'Destination for the "?" button in the topbar. Leave empty to hide the button.';
$string['aboutmaplabel'] = 'Map of Living Labs and partners across Europe';
