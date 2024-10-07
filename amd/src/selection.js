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
 * @copyright  2016 Marcus Green
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define(function() {
  /**
   * Initialise the quesiton instance with a unique id
   * Necessary where there is more than one of this
   * type of question per page.
   * @param {int} questionId
   */
  function WordSelectQuestion(questionId) {
    const questionElement = document.getElementById(questionId);
    const selectables = questionElement.querySelectorAll(".selectable");

    selectables.forEach((selectable) => {
      selectable.addEventListener("keydown", (e) => {
        if (e.key === " ") {
          toggleSelection(selectable);
          e.preventDefault();
          return false;
        }
        return true;
      });

      selectable.addEventListener("click", () => {
        toggleSelection(selectable);
      });
    });
  }

  /**
   * Toggle a word (or collection of word)
   * As selected/unselected.
   * @param {string} selection
   */
  function toggleSelection(selection) {

    var isSelected = selection.classList.contains('selected');
    var hidden = document.getElementById(selection.id);
    if (hidden === null || hidden.disabled === true) {
      return;
    }
    if (isSelected  === true) {
      selection.classList.remove('selected');
      selection.removeAttribute('title');
      selection.setAttribute('aria-checked', 'false');
      /* Convert type to text, because
      * unchecked textboxes would not
      * be included in the response
      */
      hidden.type = 'text';
      hidden.style.visibility = 'hidden';
      hidden.style.display = 'none';
      hidden.value = '';
    } else {
      selection.className += ' selected';
      selection.title = 'selected';
      selection.setAttribute('aria-checked', 'true');
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
