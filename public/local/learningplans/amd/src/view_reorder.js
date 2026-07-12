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
     * Drag-and-drop for learning plan courses across stage blocks.
     *
     * Every stage renders its own list; SortableList allows dragging
     * between them, and dropping into another block also moves the course
     * into that stage. On drop all lists are serialised in DOM order into
     * parallel courseid/stageid sequences and posted for the restructure
     * use case.
     *
     * @param {string} wrapSelector CSS selector of the stage-groups wrapper.
     * @param {string} formSelector CSS selector for the reorder submit form.
     * @param {string} inputSelector CSS selector for the ordered IDs input.
     * @param {string} stageInputSelector CSS selector for the stage IDs input.
     */
    var init = function(wrapSelector, formSelector, inputSelector, stageInputSelector) {
        var wrap = $(wrapSelector);
        var form = $(formSelector);
        var orderInput = $(inputSelector);
        var stageInput = $(stageInputSelector);
        var listSelector = wrapSelector + ' .learning-plan__stage-list';

        if (!wrap.length || !form.length || !orderInput.length
                || wrap.find('.learning-plan__course-item').length < 2) {
            return;
        }

        var sortable = new SortableList(listSelector, {
            targetListSelector: listSelector,
        });
        sortable.getElementName = function(element) {
            var name = element.attr('data-name') || $.trim(element.find('.learning-plan__course-name').text());
            return $.Deferred().resolve(name);
        };

        wrap.find('.learning-plan__course-item').on(SortableList.EVENTS.DRAGSTART, function(_, info) {
            setTimeout(function() {
                $('.sortable-list-is-dragged').width(info.element.width());
            }, 250);
        }).on(SortableList.EVENTS.DROP, function(_, info) {
            if (!info.positionChanged) {
                return;
            }

            var orderedIds = [];
            var stageIds = [];
            wrap.find('.learning-plan__stage-list').each(function() {
                var stageId = parseInt($(this).attr('data-stageid'), 10) || 0;
                $(this).children().each(function() {
                    var courseId = parseInt($(this).attr('data-courseid'), 10);
                    if (!isNaN(courseId) && courseId > 0) {
                        orderedIds.push(courseId);
                        stageIds.push(stageId);
                    }
                });
            });

            if (!orderedIds.length) {
                return;
            }

            orderInput.val(orderedIds.join(','));
            stageInput.val(stageIds.join(','));
            form.trigger('submit');
        });
    };

    return {
        init: init
    };
});
