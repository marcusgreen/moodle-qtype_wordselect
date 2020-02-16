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
 * support for the mdl35+ mobile app
 * This file is the equivalent of
 * qtype/wordselect/classes/wordselect.ts in the core app
 * e.g.
 * https://github.com/moodlehq/moodlemobile2/blob/v3.5.0/src/addon/qtype/ddwtos/classes/ddwtos.ts
 */


var that = this;
var result = {

    componentInit: function() {
      /**
       * Select a word on click event
       * @param {object} event
       */
        function selectWord(event) {
            var selector = "#" + event.target.id;
            var parts = selector.split(":");
            selector = parts[0] + "\\:" + parts[1];
            var selection = document.querySelector(selector + ".selectable");
            if (selection === null) {
                /* Selection will be null after marking/readonly */
                return;
            }
            /* Not sure if this is necessary */
            var hidden = document.getElementById(selection.getAttribute('id'));
            if (selection.classList.contains('selected')) {
                selection.classList.remove('selected');
                selection.title = '';
                selection.setAttribute("aria-checked", false);
                /* Change the type to text to avoid the problem that
                unchecked checkboxes are not passed in the request */
                hidden.style.visibility = "hidden";
                hidden.style.display = "none";
                hidden.value = "";
                hidden.checked = "false";

            } else {
                selection.classList.add('selected');
                selection.title = 'selected';
                selection.setAttribute("aria-checked", true);
                hidden.value = "on";
                selection.value = "on";
                hidden.checked = "true";
            }
        }

        this.questionRendered = function questionRendered() {
            var selchecks = this.componentContainer.querySelectorAll('input.selcheck');
            var i = 0;
            for (i = 0; i < selchecks.length; i++) {
                selchecks[i].type = "hidden";
                if (selchecks[i].checked == true) {
                    selchecks[i].value = "on";
                }
            }
            var selectables = this.componentContainer.querySelectorAll('.selectable');
            for (i = 0; i < selectables.length; i++) {
                if (selectables[i].id) {
                    selectables[i].addEventListener('click', function() {
                        selectWord(event);
                    });
                }
            }
        };

        if (!this.question) {
            return that.CoreQuestionHelperProvider.showComponentError(that.onAbort);
        }

        var div = document.createElement('div');
        div.innerHTML = this.question.html;

         // Treat the correct/incorrect icons.
        this.CoreQuestionHelperProvider.treatCorrectnessIcons(div);

        if (div.querySelector('.readonly') !== null) {
            this.question.readOnly = true;
        }
        if (div.querySelector('.feedback') !== null) {
            this.question.feedback = div.querySelector('.feedback');
            this.question.feedbackHTML = true;
        }

        this.question.text = this.CoreDomUtilsProvider.getContentsOfElement(div, '.qtext');
        this.question.introduction = this.CoreDomUtilsProvider.getContentsOfElement(div, '.introduction');

        if (typeof this.question.text == 'undefined') {
            this.logger.warn('Aborting because of an error parsing question.', this.question.name);
            return this.CoreQuestionHelperProvider.showComponentError(this.onAbort);
        }

        return true;
    }
};
/* eslint-disable */
result;
/* eslint-enable */

