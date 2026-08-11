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
 * Shared polite live-region announcer for the SecureFood shell.
 *
 * Writes to the single `#sfs-live` region rendered by the shell layout, so
 * async UI changes (colour-scheme toggle, map hub selection) are conveyed to
 * assistive technology. Progressive enhancement: if the region is absent the
 * call is a no-op.
 *
 * @module     theme_securefood/live
 * @copyright  2026 SecureFood School
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Announce a message through the shell's polite live region.
 *
 * The region is cleared first and re-populated after a short timeout so screen
 * readers re-announce even when the same message repeats. A timeout (not
 * requestAnimationFrame) is used so the update still fires in a background tab.
 *
 * @param {string} message The text to announce.
 */
export const announce = (message) => {
    const region = document.getElementById('sfs-live');
    if (!region || !message) {
        return;
    }
    region.textContent = '';
    window.setTimeout(() => {
        region.textContent = message;
    }, 50);
};
