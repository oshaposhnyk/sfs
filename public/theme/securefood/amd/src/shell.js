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

import Carousel from 'theme_boost/bootstrap/carousel';
import {setUserPreference} from 'core_user/repository';
import {announce} from 'theme_securefood/live';

const CLASSES = {
    collapsed: 'sfs-sidebar-collapsed',
    drawerOpen: 'sfs-drawer-open',
};

// Topbar "More" disclosure (ADR-013). The collapsible class is added by JS only,
// so without this module the secondary controls stay inline (progressive
// enhancement) and nothing is hidden behind an inert toggle.
const MORE_COLLAPSIBLE = 'sfs-topbar__more--collapsible';
const MORE_SELECTORS = {
    wrap: '.sfs-topbar__more',
    toggle: '[data-sfs-action="more"]',
};

const isMobile = () => window.matchMedia('(max-width: 820px)').matches;

const USER_MENU_SELECTORS = {
    menu: '#sfs-sidebar .sfs-usercard-menu',
    details: '.sfs-usercard-menu__details',
    carousel: '#usermenu-carousel',
    carouselItem: '.carousel-item',
    activeCarouselItem: '.carousel-item.active',
    carouselNavigationLink: '.carousel-navigation-link',
    menuitem: '[role="menuitem"]',
};

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
 * All three preferences are stamped on <html> (data-theme="system" included)
 * so the dark palette stays scoped to the shell: the CSS `[data-theme="system"]`
 * rule under prefers-color-scheme owns the system dark fallback, and standard
 * Boost pages — which carry no attribute — never inherit the dark tokens.
 *
 * @param {HTMLElement} button The scheme toggle button.
 * @param {string} preference One of light, dark, system.
 */
const applySchemePreference = (button, preference) => {
    document.documentElement.dataset.theme = preference;
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
    const label = button.dataset['sfsScheme' + next.charAt(0).toUpperCase() + next.slice(1)];
    if (label) {
        announce(label);
    }
};

/**
 * Get the topbar "More" wrapper and its toggle button.
 *
 * @return {?{wrap: HTMLElement, toggle: HTMLElement}}
 */
const getMore = () => {
    const wrap = document.querySelector(MORE_SELECTORS.wrap);
    const toggle = wrap ? wrap.querySelector(MORE_SELECTORS.toggle) : null;
    return wrap && toggle ? {wrap, toggle} : null;
};

/**
 * Open or close the "More" popover, keeping aria-expanded in sync.
 *
 * @param {boolean} open Whether the popover should be open.
 */
const setMoreOpen = (open) => {
    const more = getMore();
    if (more) {
        more.toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
    }
};

/**
 * Close the "More" popover if it is open, optionally returning focus.
 *
 * @param {boolean} refocus Whether to move focus back to the toggle.
 */
const closeMore = (refocus = false) => {
    const more = getMore();
    if (!more || more.toggle.getAttribute('aria-expanded') !== 'true') {
        return;
    }
    setMoreOpen(false);
    if (refocus) {
        more.toggle.focus();
    }
};

/**
 * Switch the "More" control between inline (desktop) and collapsible popover
 * (mobile). Always leaves the popover closed.
 */
const applyMoreMode = () => {
    const more = getMore();
    if (!more) {
        return;
    }
    more.wrap.classList.toggle(MORE_COLLAPSIBLE, isMobile());
    setMoreOpen(false);
};

/**
 * Resize inactive user-menu carousel panes to the currently visible pane.
 *
 * Moodle core normally does this on Bootstrap dropdown lifecycle events. The
 * SFS sidebar uses native <details> for stable fixed positioning, so it needs
 * a small scoped bridge instead of relying on dropdown events that never fire.
 *
 * @param {HTMLElement} userMenu The SFS user menu wrapper.
 */
const syncUserMenuCarouselSize = (userMenu) => {
    const activeItem = userMenu.querySelector(
        `${USER_MENU_SELECTORS.carousel} ${USER_MENU_SELECTORS.activeCarouselItem}`
    );
    if (!activeItem || activeItem.offsetWidth <= 0 || activeItem.offsetHeight <= 0) {
        return;
    }

    userMenu.querySelectorAll(
        `${USER_MENU_SELECTORS.carousel} ${USER_MENU_SELECTORS.carouselItem}`
    ).forEach((item) => {
        if (item === activeItem) {
            return;
        }
        item.style.width = `${activeItem.offsetWidth}px`;
        item.style.height = `${activeItem.offsetHeight}px`;
    });
};

/**
 * Move the sidebar user-menu carousel to the target pane.
 *
 * @param {HTMLElement} userMenu The SFS user menu wrapper.
 * @param {string|undefined} targetId The carousel item id.
 */
const moveUserMenuCarousel = (userMenu, targetId) => {
    if (!targetId) {
        return;
    }

    const carousel = userMenu.querySelector(USER_MENU_SELECTORS.carousel);
    const targetItem = document.getElementById(targetId);
    if (!carousel || !targetItem || !userMenu.contains(targetItem) || !targetItem.parentNode) {
        return;
    }

    const index = Array.from(targetItem.parentNode.children).indexOf(targetItem);
    if (index < 0) {
        return;
    }

    syncUserMenuCarouselSize(userMenu);
    Carousel.getOrCreateInstance(carousel).to(index);
};

/**
 * Reset the sidebar user-menu carousel to the main pane.
 *
 * @param {HTMLElement} userMenu The SFS user menu wrapper.
 */
const resetUserMenuCarousel = (userMenu) => {
    const carousel = userMenu.querySelector(USER_MENU_SELECTORS.carousel);
    if (!carousel) {
        return;
    }
    Carousel.getOrCreateInstance(carousel).to(0);
};

/**
 * The [role=menuitem] elements in the user menu's active carousel pane.
 *
 * @param {HTMLElement} userMenu The SFS user menu wrapper.
 * @return {HTMLElement[]} Ordered menu items (possibly empty).
 */
const activePaneItems = (userMenu) => {
    const pane = userMenu.querySelector(USER_MENU_SELECTORS.activeCarouselItem);
    return pane ? Array.from(pane.querySelectorAll(USER_MENU_SELECTORS.menuitem)) : [];
};

/**
 * Apply a roving tabindex to a set of menu items and focus one of them.
 *
 * @param {HTMLElement[]} items The menu items.
 * @param {number} index Target index (wraps around).
 */
const rovingFocus = (items, index) => {
    if (!items.length) {
        return;
    }
    const target = (index + items.length) % items.length;
    items.forEach((item, i) => item.setAttribute('tabindex', i === target ? '0' : '-1'));
    items[target].focus();
};

/**
 * Move focus to the first item of the active pane (menu open / pane switch).
 *
 * @param {HTMLElement} userMenu The SFS user menu wrapper.
 */
const focusFirstMenuItem = (userMenu) => {
    const items = activePaneItems(userMenu);
    if (items.length) {
        rovingFocus(items, 0);
        return;
    }
    const pane = userMenu.querySelector(USER_MENU_SELECTORS.activeCarouselItem);
    if (pane) {
        pane.focus();
    }
};

/**
 * WAI-ARIA menu keyboard handling: Up/Down/Home/End roving focus and Escape to
 * close (returning focus to the summary). Enter/Space activation stays native
 * for links or is handled by the carousel-navigation bridge.
 *
 * @param {KeyboardEvent} e The keydown event.
 * @param {HTMLElement} userMenu The SFS user menu wrapper.
 */
const handleUserMenuKeydown = (e, userMenu) => {
    const details = userMenu.querySelector(USER_MENU_SELECTORS.details);
    if (!details || !details.open) {
        return;
    }
    if (e.key === 'Escape') {
        e.preventDefault();
        details.open = false;
        const summary = details.querySelector('summary');
        if (summary) {
            summary.focus();
        }
        return;
    }
    const items = activePaneItems(userMenu);
    if (!items.length) {
        return;
    }
    const current = items.indexOf(document.activeElement);
    if (e.key === 'ArrowDown') {
        e.preventDefault();
        rovingFocus(items, current < 0 ? 0 : current + 1);
    } else if (e.key === 'ArrowUp') {
        e.preventDefault();
        rovingFocus(items, current < 0 ? items.length - 1 : current - 1);
    } else if (e.key === 'Home') {
        e.preventDefault();
        rovingFocus(items, 0);
    } else if (e.key === 'End') {
        e.preventDefault();
        rovingFocus(items, items.length - 1);
    }
};

/**
 * Handle a click/keyboard activation on any nested part of a carousel link.
 *
 * Moodle core/user-menu checks event.target directly, which misses clicks on
 * nested icon/label spans inside the styled SFS rows. This bridge uses closest()
 * and stops the event before the generic core listener can apply the old rule.
 *
 * @param {Event} e Click or keydown event.
 * @param {HTMLElement} userMenu The SFS user menu wrapper.
 */
const handleUserMenuCarouselNavigation = (e, userMenu) => {
    if (!(e.target instanceof Element)) {
        return;
    }

    const trigger = e.target.closest(USER_MENU_SELECTORS.carouselNavigationLink);
    if (!trigger || !userMenu.contains(trigger)) {
        return;
    }

    e.preventDefault();
    e.stopPropagation();
    if (typeof e.stopImmediatePropagation === 'function') {
        e.stopImmediatePropagation();
    }

    moveUserMenuCarousel(userMenu, trigger.dataset.carouselTargetId);
};

/**
 * Initialise the SFS sidebar bridge for Moodle user-menu submenus.
 */
const initUserMenuCarouselBridge = () => {
    document.querySelectorAll(USER_MENU_SELECTORS.menu).forEach((userMenu) => {
        if (userMenu.dataset.sfsCarouselBridge === 'true') {
            return;
        }

        const details = userMenu.querySelector(USER_MENU_SELECTORS.details);
        const carousel = userMenu.querySelector(USER_MENU_SELECTORS.carousel);
        if (!details || !carousel) {
            return;
        }

        userMenu.dataset.sfsCarouselBridge = 'true';

        userMenu.addEventListener('click', (e) => {
            handleUserMenuCarouselNavigation(e, userMenu);
        }, true);

        userMenu.addEventListener('keydown', (e) => {
            if (e.key !== 'Enter' && e.key !== ' ') {
                return;
            }
            handleUserMenuCarouselNavigation(e, userMenu);
        }, true);

        userMenu.addEventListener('keydown', (e) => {
            handleUserMenuKeydown(e, userMenu);
        });

        details.addEventListener('toggle', () => {
            if (details.open) {
                syncUserMenuCarouselSize(userMenu);
                focusFirstMenuItem(userMenu);
                return;
            }
            resetUserMenuCarousel(userMenu);
        });

        carousel.addEventListener('slid.bs.carousel', () => {
            syncUserMenuCarouselSize(userMenu);
            focusFirstMenuItem(userMenu);
        });
    });
};

/**
 * Initialise the shell interactions.
 */
export const init = () => {
    syncSidebarAccessibility(document.body.classList.contains(CLASSES.drawerOpen));
    initUserMenuCarouselBridge();
    applyMoreMode();

    document.addEventListener('click', (e) => {
        // Click-away closes the "More" popover before any action handling.
        if (!e.target.closest(MORE_SELECTORS.wrap)) {
            closeMore();
        }

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
        } else if (action === 'more') {
            e.preventDefault();
            setMoreOpen(target.getAttribute('aria-expanded') !== 'true');
        } else if (action === 'backdrop') {
            closeDrawer();
        }
    });

    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            if (document.body.classList.contains(CLASSES.drawerOpen)) {
                closeDrawer();
            }
            closeMore(true);
        }
        trapFocus(e);
    });

    window.matchMedia('(max-width: 820px)').addEventListener('change', () => {
        document.body.classList.remove(CLASSES.drawerOpen);
        lastfocus = null;
        syncSidebarAccessibility(false);
        applyMoreMode();
    });
};
