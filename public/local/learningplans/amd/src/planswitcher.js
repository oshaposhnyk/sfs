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
 * Plan switcher enhancement: switch via web service instead of the PRG link.
 *
 * The menu links keep their sesskey URLs, so everything still works without
 * JavaScript; with it, switching avoids the redirect hop.
 *
 * @module     local_learningplans/planswitcher
 * @copyright  2026
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import Ajax from 'core/ajax';
import Notification from 'core/notification';

/**
 * Initialise the switcher.
 */
export const init = () => {
    document.addEventListener('click', (e) => {
        const link = e.target.closest('.lp-planmenu__link[data-planid]');
        if (!link) {
            return;
        }
        e.preventDefault();
        link.setAttribute('aria-busy', 'true');
        Ajax.call([{
            methodname: 'local_learningplans_set_active_plan',
            args: {planid: parseInt(link.dataset.planid, 10)},
        }])[0]
            .then(() => window.location.reload())
            .catch(Notification.exception);
    });
};
