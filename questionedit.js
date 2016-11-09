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

$("#id_tablewrap").on("click", function () {
    //  var selection=document.getSelection();
    //  var words = selection.split(" ");
   var sel=getSelectionText();
   var selText= sel.focusNode.textContent;
  // var words = selText.split(" ");
  var table="<table><tr>";
  for (var i = 0, len = selText.length; i < len; i++) {
   table+="<td>"+selText[i]+"</td>";
}
table+="</tr></table>";
console.log(table);
replaceSelection(table);
      
      
});


function replaceSelection(t) {
  if (typeof t === 'function') {
    t = t(window.getSelection().toString());
  }
  var range = window.getSelection().getRangeAt(0);
  range.deleteContents();
  range.insertNode(document.createTextNode(t));
}



function getSelectionText() {
    if (window.getSelection) {
        txt = window.getSelection();
    } else if (window.document.getSelection) {
        txt =window.document.getSelection();
    } else if (window.document.selection) {
        txt = window.document.selection.createRange().text;
    }
    return txt;  
}