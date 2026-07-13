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
 * Keep the drawer's accessibility state aligned with its visual state.
 *
 * The off-canvas navigation must not remain keyboard-focusable while hidden.
 * On desktop the sidebar is always exposed and aria-expanded reflects only
 * the persisted collapsed/expanded presentation.
 *
 * @param {boolean} opened Whether the mobile drawer is open.
 */
const syncSidebarAccessibility = (opened = false) => {
    const sidebar = document.getElementById('sfs-sidebar');
    const button = document.querySelector('[data-sfs-action="sidebar"]');
    if (!sidebar || !button) {
        return;
    }

    if (isMobile()) {
        button.setAttribute('aria-expanded', opened ? 'true' : 'false');
        sidebar.setAttribute('aria-hidden', opened ? 'false' : 'true');
        sidebar.toggleAttribute('inert', !opened);
        return;
    }

    sidebar.removeAttribute('aria-hidden');
    sidebar.removeAttribute('inert');
    button.setAttribute(
        'aria-expanded',
        document.body.classList.contains(CLASSES.collapsed) ? 'false' : 'true'
    );
};

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
    syncSidebarAccessibility(false);
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
        syncSidebarAccessibility(opened);
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
 * Apply the colour-scheme preference to the current document.
 *
 * Explicit light/dark preferences are stamped on <html>; system removes the
 * attribute so the CSS prefers-color-scheme fallback owns the effective theme.
 *
 * @param {HTMLElement} button The scheme toggle button.
 * @param {string} preference One of light, dark, system.
 */
const applySchemePreference = (button, preference) => {
    const root = document.documentElement;
    if (preference === 'system') {
        root.removeAttribute('data-theme');
    } else {
        root.dataset.theme = preference;
    }
    button.dataset.sfsSchemePreference = preference;
};

/**
 * Return the next persisted colour-scheme preference.
 *
 * @param {string} preference Current preference.
 * @return {string} Next preference.
 */
const nextSchemePreference = (preference) => {
    if (preference === 'light') {
        return 'dark';
    }
    if (preference === 'dark') {
        return 'system';
    }
    return 'light';
};

/**
 * Toggle the colour scheme through light, dark and system (persisted).
 *
 * @param {HTMLElement} button The scheme toggle button.
 */
const toggleScheme = (button) => {
    const current = button.dataset.sfsSchemePreference || 'system';
    const next = nextSchemePreference(current);
    applySchemePreference(button, next);
    persist('theme_securefood_colourscheme', next);
};

/**
 * Initialise the shell interactions.
 */
export const init = () => {
    syncSidebarAccessibility(document.body.classList.contains(CLASSES.drawerOpen));

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
            toggleScheme(target);
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

    window.matchMedia('(max-width: 820px)').addEventListener('change', () => {
        document.body.classList.remove(CLASSES.drawerOpen);
        lastfocus = null;
        syncSidebarAccessibility(false);
    });
};
