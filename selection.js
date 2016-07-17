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
 * @package    qtype
 * @subpackage wordselect
 * @copyright  2016 Marcus Green
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$(function () {
    $(".selectable").on('keydown', function (e) {
        /* right arrow key */
        if (e.keyCode == 39) {
            toggleselected($(this));
        }
    });
    $(".selectable").on('click', function (e) {
        toggleselected($(this));
    });
});

var toggleselected = function (component) {
    iselected = component.hasClass("selected");
    wordname = component.attr('name');
    checkbox = document.getElementById(wordname);
    if (checkbox == null || checkbox.disabled == true) {
        return;
    }
    if (iselected == true) {
        component.removeClass("selected");
        component.removeAttr("title");
        $(checkbox).removeAttr('checked');
    } else {
        component.addClass("selected");
        component.prop('title', 'selected');
        $(checkbox).prop("checked", 'true');
    }
}