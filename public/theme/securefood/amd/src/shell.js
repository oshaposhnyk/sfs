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

let lastfocus = null;

/**
 * Keep Tab cycling inside the sidebar while the mobile drawer is open.
 *
 * @param {KeyboardEvent} e Keydown event.
 */
const trapFocus = (e) => {
    if (e.key !== 'Tab' || !document.body.classList.contains(CLASSES.drawerOpen)) {
        return;
    }
    const sidebar = document.getElementById('sfs-sidebar');
    if (!sidebar) {
        return;
    }
    const focusables = sidebar.querySelectorAll('a[href], button:not([disabled])');
    if (!focusables.length) {
        return;
    }
    const first = focusables[0];
    const last = focusables[focusables.length - 1];
    if (e.shiftKey && document.activeElement === first) {
        e.preventDefault();
        last.focus();
    } else if (!e.shiftKey && document.activeElement === last) {
        e.preventDefault();
        first.focus();
    }
};

/**
 * Close the mobile drawer and restore focus to the opener.
 */
const closeDrawer = () => {
    document.body.classList.remove(CLASSES.drawerOpen);
    if (lastfocus) {
        lastfocus.focus();
        lastfocus = null;
    }
};

/**
 * Toggle the sidebar: drawer on mobile, collapse on desktop (persisted).
 *
 * @param {HTMLElement} button The toggle button.
 */
const toggleSidebar = (button) => {
    const body = document.body;
    if (isMobile()) {
        const opened = body.classList.toggle(CLASSES.drawerOpen);
        if (opened) {
            lastfocus = button;
            const first = document.querySelector('#sfs-sidebar a[href]');
            if (first) {
                first.focus();
            }
        } else {
            closeDrawer();
        }
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
            closeDrawer();
        }
    });

    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && document.body.classList.contains(CLASSES.drawerOpen)) {
            closeDrawer();
        }
        trapFocus(e);
    });
};
