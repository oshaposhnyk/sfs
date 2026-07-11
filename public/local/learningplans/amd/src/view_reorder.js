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

define(['jquery', 'core/sortable_list'], function($, SortableList) {
    /**
     * Initialize drag and drop reordering for learning plan courses.
     *
     * @param {string} listSelector CSS selector for sortable list root.
     * @param {string} formSelector CSS selector for reorder submit form.
     * @param {string} inputSelector CSS selector for hidden ordered IDs input.
     */
    var init = function(listSelector, formSelector, inputSelector) {
        var list = $(listSelector);
        var form = $(formSelector);
        var orderInput = $(inputSelector);

        if (!list.length || !form.length || !orderInput.length || list.children().length < 2) {
            return;
        }

        var sortable = new SortableList(listSelector);
        sortable.getElementName = function(element) {
            var name = element.attr('data-name') || $.trim(element.find('.learning-plan__course-name').text());
            return $.Deferred().resolve(name);
        };

        list.children().on(SortableList.EVENTS.DRAGSTART, function(_, info) {
            setTimeout(function() {
                $('.sortable-list-is-dragged').width(info.element.width());
            }, 250);
        }).on(SortableList.EVENTS.DROP, function(_, info) {
            if (!info.positionChanged) {
                return;
            }

            var orderedIds = [];
            info.targetList.children().each(function() {
                var courseId = parseInt($(this).attr('data-courseid'), 10);
                if (!isNaN(courseId) && courseId > 0) {
                    orderedIds.push(courseId);
                }
            });

            if (!orderedIds.length) {
                return;
            }

            orderInput.val(orderedIds.join(','));
            form.trigger('submit');
        });
    };

    return {
        init: init
    };
});
