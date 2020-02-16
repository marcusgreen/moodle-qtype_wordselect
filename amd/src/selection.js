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
 * JavaScript code for the wordselect question type.
 *
 * @package    qtype_wordselect
 * @copyright  2016 Marcus Green
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/* global $ */
define(function() {
  /**
   * Initialise the quesiton instance with a unique id
   * Necessary where there is more than one of this
   * type of question per page.
   * @param {int} questionId
   */
  function WordSelectQuestion(questionId) {
    $('#' + questionId + ' .selectable').on('keydown', function(e) {
      /* Space bar */
      if (e.keyCode === 32) {
        toggleSelection($(this));
        return false;
      }
      /* Eat the keycode so it doesnt scroll the screen down */
      if (e.keyCode === 32) {
        return false;
      }
      return true;
    });
    $('#' + questionId + ' .selectable').on('click', function() {
      toggleSelection($(this));
    });
  }

  /**
   * Toggle a word (or collection of word)
   * As selected/unselected.
   * @param {string} selection
   */
  function toggleSelection(selection) {
    var iselected = $(selection).hasClass('selected');
    var wordname = selection.attr('name');
    var hidden = document.getElementById(wordname);
    if (hidden === null || hidden.disabled === true) {
      return;
    }
    if (iselected === true) {
      selection.removeClass('selected');
      selection.removeAttr('title');
      selection.attr('aria-checked', 'false');
      /* Convert type to text, because
      * unchecked textboxes would not
      * be included in the response
      */
      hidden.type = 'text';
      hidden.style.visibility = 'hidden';
      hidden.style.display = 'none';
      hidden.value = '';
    } else {
      selection.addClass('selected');
      selection.prop('title', 'selected');
      selection.attr('aria-checked', 'true');
      hidden.type = 'checkbox';
      hidden.value = 'on';
      hidden.checked = 'true';
    }
  }

  return {
    init: function(questionId) {
      new WordSelectQuestion(questionId);
    },
  };
});
