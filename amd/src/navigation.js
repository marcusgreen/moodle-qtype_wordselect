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
 * This class provides functionality for the keyboard navigation.
 *
 * @package    qtype_wordselect
 * @copyright  2019 Marcus Green
 * @author     Huong Nguyen <huongnv13@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
define(['jquery'], function($) {
    /**
     * @alias qtype_wordselect/navigation
     */
    var t = {

        /**
         * CSS selector.
         */
        CSS: {
            QUESTION_CONTENT: '.qtext',
            SELECTABLE_WORD: 'span.selectable'
        },

        /**
         * List of rows and columns of words.
         */
        tableMatrix: [],

        /**
         * Initialise the navigation.
         */
        init: function() {
            var questionContent = $(t.CSS.QUESTION_CONTENT);
            var words = questionContent.find(t.CSS.SELECTABLE_WORD);
            if (words.length === 0) {
                // Question must be in read-only mode.
                return;
            }

            var lineCounter = 0;
            var previousY = parseInt($(words[0]).position().top, 10);
            var columnCounter = 0;
            var rows = [];

            for (var i = 0; i < words.length; i++) {
                var wordEle = $(words[i]);
                var isTable = wordEle.parent().closest('table').length;
                var currentTableColNo = 0;
                if (isTable) {
                    currentTableColNo = wordEle.parent().closest('table').find('tr:first td').length;
                }
                if (parseInt(wordEle.position().top, 10) !== previousY || (isTable && columnCounter === currentTableColNo)) {
                    // Next line or next row.
                    columnCounter = 0;
                    lineCounter++;
                }
                if (typeof rows[lineCounter] === 'undefined') {
                    rows[lineCounter] = [];
                }
                var currentItem = [];
                currentItem.id = wordEle.attr('id');
                currentItem.value = wordEle.text();
                rows[lineCounter][columnCounter] = currentItem;
                previousY = parseInt(wordEle.position().top, 10);
                columnCounter++;
            }
            t.tableMatrix = rows;

            $(t.CSS.SELECTABLE_WORD).on('keydown', function(e) {
                if (e.keyCode === 37 || e.keyCode === 38 || e.keyCode === 39 || e.keyCode === 40) {
                    e.preventDefault();
                    var currentWordPos = t.getWordPosition($(this).attr('id'));
                    t.navigateToWord(currentWordPos.row, currentWordPos.col, e.keyCode);
                }
            });

            $(t.CSS.SELECTABLE_WORD).on('keyup', function(e) {
                // Handle keyboard focus.
                if ($(this).hasClass('selected') && (
                    e.keyCode === 9 || (e.shiftKey && (e.which === 9)) ||
                    e.keyCode === 37 || e.keyCode === 38 || e.keyCode === 39 || e.keyCode === 40)) {
                    e.preventDefault();
                    $(this).addClass('keyboard_focus');
                }
            });

            $(t.CSS.SELECTABLE_WORD).focusout(function() {
                if ($(this).hasClass('selected') && $(this).hasClass('keyboard_focus')) {
                    $(this).removeClass('keyboard_focus');
                }
            });
        },

        /**
         *
         * Get the given word position.
         *
         * @param {String} id Id of word need to get the position
         * @returns {boolean|{col: number, row: number}}
         */
        getWordPosition: function(id) {
            for (var i = 0; i < t.tableMatrix.length; i++) {
                var currentRow = t.tableMatrix[i];
                for (var j = 0; j < currentRow.length; j++) {
                    if (currentRow[j].id === id) {
                        return {
                            'row': i,
                            'col': j
                        };
                    }
                }
            }
            return false;
        },

        /**
         *
         * Calculate and set keyboard focus to specific word.
         *
         * @param {int} row Current focus row index
         * @param {int} col Current focus column index
         * @param {int} keyCode Code of keypress
         */
        navigateToWord: function(row, col, keyCode) {
            if (typeof t.tableMatrix[row] !== 'undefined' && typeof t.tableMatrix[row][col] !== 'undefined') {
                var nextCellId = '';
                if (keyCode === 37) {
                    // Left arrow.
                    if (typeof t.tableMatrix[row][col - 1] !== 'undefined') {
                        nextCellId = t.tableMatrix[row][col - 1].id;
                    } else if (typeof t.tableMatrix[row - 1] !== 'undefined' &&
                        typeof t.tableMatrix[row - 1][t.tableMatrix[row - 1].length - 1] !== 'undefined') {
                        // Previous line, last word.
                        nextCellId = t.tableMatrix[row - 1][t.tableMatrix[row - 1].length - 1].id;
                    }
                } else if (keyCode === 38) {
                    // Up arrow.
                    if (typeof t.tableMatrix[row - 1] !== 'undefined' && typeof t.tableMatrix[row - 1][0] !== 'undefined') {
                        nextCellId = t.tableMatrix[row - 1][0].id;
                    }
                } else if (keyCode === 39) {
                    // Right arrow.
                    if (typeof t.tableMatrix[row][col + 1] !== 'undefined') {
                        nextCellId = t.tableMatrix[row][col + 1].id;
                    } else if (typeof t.tableMatrix[row + 1] !== 'undefined' && typeof t.tableMatrix[row + 1][0] !== 'undefined') {
                        // Next line, first word.
                        nextCellId = t.tableMatrix[row + 1][0].id;
                    }
                } else if (keyCode === 40) {
                    // Down arrow.
                    if (typeof t.tableMatrix[row + 1] !== 'undefined' && typeof t.tableMatrix[row + 1][0] !== 'undefined') {
                        // Next line, first word.
                        nextCellId = t.tableMatrix[row + 1][0].id;
                    }
                }
                $("span[name='" + nextCellId + "']").focus();
            }
        }
    };

    return t;
});
