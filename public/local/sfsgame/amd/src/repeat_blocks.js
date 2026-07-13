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
 * Repeatable admin-setting blocks for Future Food.
 *
 * @module     local_sfsgame/repeat_blocks
 * @copyright  2026 SecureFood School
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Initialise one repeatable setting control.
 *
 * @param {String} rootId Root element id.
 */
export const init = (rootId) => {
    const root = document.getElementById(rootId);
    if (!root) {
        return;
    }

    const list = root.querySelector('[data-repeat-list]');
    const template = root.querySelector('template[data-repeat-template]');
    const add = root.querySelector('[data-repeat-add]');
    if (!list || !template || !add) {
        return;
    }

    add.addEventListener('click', () => {
        const index = parseInt(root.dataset.nextIndex || '0', 10);
        root.dataset.nextIndex = String(index + 1);
        const wrapper = document.createElement('div');
        wrapper.innerHTML = template.innerHTML.replace(/__INDEX__/g, String(index)).trim();
        const item = wrapper.firstElementChild;
        if (item) {
            list.append(item);
            const firstInput = item.querySelector('input, textarea');
            if (firstInput) {
                firstInput.focus();
            }
        }
    });

    root.addEventListener('click', (event) => {
        const deleteButton = event.target.closest('[data-repeat-delete]');
        if (!deleteButton || !root.contains(deleteButton)) {
            return;
        }

        const item = deleteButton.closest('[data-repeat-item]');
        if (item) {
            item.remove();
        }
    });
};
