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
 * Per-section progress fractions in course section headers (audit C2).
 *
 * Progressive enhancement: the layout passes the viewer's own completion
 * counts and this module decorates the section headers to match course.html.
 * Without JS the headers simply stay plain.
 *
 * @module     theme_securefood/sectionprogress
 * @copyright  2026 SecureFood School
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Decorate course section headers with "done / total" fractions.
 *
 * @param {Array} sections [{number, frac, label}] from courserail::context().
 */
export const init = (sections) => {
    sections.forEach((section) => {
        const header = document.querySelector(
            'li.course-section[data-number="' + section.number + '"] .course-section-header'
        );
        if (!header || header.querySelector('.sfs-secprog')) {
            return;
        }
        const box = document.createElement('div');
        box.className = 'sfs-secprog';
        const frac = document.createElement('div');
        frac.className = 'sfs-secprog__frac';
        frac.textContent = section.frac;
        const label = document.createElement('div');
        label.className = 'sfs-secprog__label';
        label.textContent = section.label;
        box.append(frac, label);
        header.appendChild(box);
    });
};
