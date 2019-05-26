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
 * @copyright  2019 Marcus Green
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define(["jquery", "core/ajax", "core/fragment","core/templates","qtype_wordselect/dialog_info" ], 
      function ($, ajax, Fragment,templates, DialogInfo) {
 // define(["jquery", "core/ajax", "core/fragment","core/templates"], 
 // function ($, ajax, Fragment,templates) {
  return {
    init: function (contextid) {
      var  modalCreateFeedback = new DialogInfo('', '', null, false, false);
      var loadFormFragment = function () {
        var params = {};
        Fragment.loadFragment("qtype_wordselect", "feedbackedit", contextid, params).done(function (html, js) {
          debugger;
          modalCreateFeedback.show('Create feedback',
          '<div id="createFeedback">' + html + '</div>',
          '<div id="creatFeedbackDialogFooter"></div>', true);
          runJS(js);

        });
      };

      var runJS = function(source) {
        if (source.trim() !== '') {
            var newscript = $('<script>').attr('type', 'text/javascript').html(source);
            $('head').append(newscript);
        }
    };
      $("#id_itemsettings_button").on("click", function() {
        loadFormFragment();
      });

  
    }
  };
});
