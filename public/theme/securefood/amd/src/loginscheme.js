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
 * Colour-scheme toggle for the pre-auth login page.
 *
 * The login page has no logged-in user to hold a preference, so the choice is
 * persisted in a cookie the server reads on the next request (see
 * core_renderer::htmlattributes) — that server-side stamp avoids a flash, and
 * this module keeps the toggle live within the page. Cycles system → light →
 * dark → system.
 *
 * @module     theme_securefood/loginscheme
 * @copyright  2026 SecureFood School
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

const COOKIE = 'theme_securefood_loginscheme';
const SCHEMES = ['system', 'light', 'dark'];

/**
 * Read the persisted scheme from the cookie.
 *
 * @return {string} One of system, light, dark.
 */
const readScheme = () => {
    const match = document.cookie.match(/(?:^|;\s*)theme_securefood_loginscheme=(system|light|dark)/);
    return match ? match[1] : 'system';
};

/**
 * Persist the scheme in a year-long, same-site cookie.
 *
 * @param {string} scheme One of system, light, dark.
 */
const writeScheme = (scheme) => {
    document.cookie = COOKIE + '=' + scheme + ';path=/;max-age=31536000;samesite=lax';
};

/**
 * Initialise the toggle button.
 */
export const init = () => {
    const button = document.querySelector('[data-sfs-loginscheme]');
    if (!button) {
        return;
    }
    const root = document.documentElement;

    const apply = (scheme) => {
        root.dataset.theme = scheme;
        button.dataset.sfsScheme = scheme;
    };

    // The server already stamped data-theme; sync the button to it (falling
    // back to the cookie) so the two never disagree.
    apply(SCHEMES.includes(root.dataset.theme) ? root.dataset.theme : readScheme());

    button.addEventListener('click', () => {
        const current = SCHEMES.indexOf(button.dataset.sfsScheme || 'system');
        const next = SCHEMES[(current + 1) % SCHEMES.length];
        apply(next);
        writeScheme(next);
    });
};
