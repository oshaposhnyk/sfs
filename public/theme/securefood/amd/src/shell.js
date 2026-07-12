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
 * SecureFood shell behaviour: sidebar collapse/drawer and colour-scheme toggle.
 *
 * Progressive enhancement only — the shell is fully usable without this
 * module (sidebar stays expanded, scheme follows the stored preference).
 *
 * @module     theme_securefood/shell
 * @copyright  2026 SecureFood School
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import {setUserPreference} from 'core_user/repository';

const CLASSES = {
    collapsed: 'sfs-sidebar-collapsed',
    drawerOpen: 'sfs-drawer-open',
};

const isMobile = () => window.matchMedia('(max-width: 820px)').matches;

/**
 * Persist a preference, ignoring failures for guests.
 *
 * @param {string} name Preference name.
 * @param {string} value Preference value.
 */
const persist = (name, value) => {
    setUserPreference(name, value).catch(() => null);
};

/**
 * Toggle the sidebar: drawer on mobile, collapse on desktop (persisted).
 *
 * @param {HTMLElement} button The toggle button.
 */
const toggleSidebar = (button) => {
    const body = document.body;
    if (isMobile()) {
        body.classList.toggle(CLASSES.drawerOpen);
        return;
    }
    const collapsed = body.classList.toggle(CLASSES.collapsed);
    button.setAttribute('aria-expanded', collapsed ? 'false' : 'true');
    persist('theme_securefood_sidebar', collapsed ? 'collapsed' : 'expanded');
};

/**
 * Toggle the colour scheme between light and dark (persisted).
 */
const toggleScheme = () => {
    const root = document.documentElement;
    const explicit = root.dataset.theme;
    const systemDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
    const current = explicit || (systemDark ? 'dark' : 'light');
    const next = current === 'dark' ? 'light' : 'dark';
    root.dataset.theme = next;
    persist('theme_securefood_colourscheme', next);
};

/**
 * Initialise the shell interactions.
 */
export const init = () => {
    document.addEventListener('click', (e) => {
        const target = e.target.closest('[data-sfs-action]');
        if (!target) {
            return;
        }
        const action = target.dataset.sfsAction;
        if (action === 'sidebar') {
            e.preventDefault();
            toggleSidebar(target);
        } else if (action === 'scheme') {
            e.preventDefault();
            toggleScheme();
        } else if (action === 'backdrop') {
            document.body.classList.remove(CLASSES.drawerOpen);
        }
    });

    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && document.body.classList.contains(CLASSES.drawerOpen)) {
            document.body.classList.remove(CLASSES.drawerOpen);
        }
    });
};
