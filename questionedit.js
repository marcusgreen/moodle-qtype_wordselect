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
 * JavaScript code for the gapfill question type.
 *
 * @package    qtype
 * @subpackage gapfill
 * @copyright  2016 Marcus Green
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


/* the data is stored in a hidden field */
var feedbackdata = ($("[name='wordfeedbackdata']").val());
var feedback = {};
if(feedbackdata > ""){
    var feedback = JSON.parse(feedbackdata);
}
var propno = 0;

function get_feedback($word, offset) {
    wordfeedback = new Array();
    for (var fb in feedback) {
        if (feedback[fb].word === $word) {
            if (feedback[fb].offset == offset) {
                wordfeedback[0] = feedback[fb];
            }
        }
    }
    return wordfeedback;
}

function add_or_update(word, offset) {
    found = null;
    for (var fb in feedback) {
        if (feedback[fb].word === word && feedback[fb].offset === offset) {
            feedback[fb].selected = $("#id_selectededitable").html(),
                    feedback[fb].notselected = $("#id_notselectededitable").html()
            found = feedback[fb];
        }
    }
    if (found === null) {
        /* if there is no record for this word add one 
         * a combination of wordtext and offset will be unique*/
        var prop = 'prop' + propno;
        propno++;
        var wordfeedback = {
            id: prop,
            question: $("input[name=id]").val(),
            selected: $("#id_selectededitable").html(),
            notselected: $("#id_notselectededitable").html(),
            word: word,
            offset: offset
        };
        feedback[prop] = wordfeedback;
    }
    return feedback;
}

$("#id_gapfeedback").on("click", function () {
    if ($('#id_questiontexteditable').get(0).isContentEditable) {
        $("#id_questiontexteditable").attr('contenteditable', 'false');
        $("#fitem_id_questiontext").find('button').attr("disabled", 'true');
        $("#id_questiontexteditable").css("backgroundColor", 'lightgrey');
    } else {
        $("#id_questiontexteditable").attr('contenteditable', 'true');
        $("#fitem_id_questiontext").find('button').removeAttr("disabled");
        $("#id_questiontexteditable").css("backgroundColor", 'white');
        $("#id_feedback_popup").css("display", "none");
    }
});

$("#fitem_id_questiontext").on("click", function () {
    if ($('#id_questiontexteditable').get(0).isContentEditable) {
        $the_text = $("#id_questiontexteditable").text();
        delimitchars = $("#id_delimitchars").val();
        /*l and r for left and right */
        l = delimitchars.substr(0, 1);
        r = delimitchars.substr(1, 1);
        rangy.init();
        var sel = rangy.getSelection();
        var word = get_selected_word(sel);
        if (word != null) {
            wordfeedback = get_feedback(word, 0);
            if (wordfeedback == null || wordfeedback.length == 0) {
                $("#id_selectededitable").html('');
                $("#id_notselectededitable").html('');
            } else {
                $("#id_selectededitable").html(wordfeedback[0].selected);
                $("#id_notselectededitable").html(wordfeedback[0].notselected);
            }
            $("label[for*='id_selected']").text("When " + word + " is selected");
            $("label[for*='id_notselected']").text("When " + word + " is not selected");
            $("#id_feedback_popup").dialog({
                height: 500,
                width: 600,
                modal: true,
                buttons: [
                    {
                        text: "OK",
                        click: function () {
                            $feedback = add_or_update(word, 0);
                            var JSONstr = JSON.stringify($feedback);
                            $("[name='wordfeedbackdata']").val(JSONstr);
                            $(this).dialog("close");
                        }

                    }
                ]
            });
        }
    }

});

function get_selected_word( sel) {
    $qtext = sel.anchorNode.nodeValue;
    $clickpoint = sel.focusOffset;
    var node=sel.focusNode; 
    /* find the character num of the left delimiter*/
    $leftdelim = null;
    for (var x = $clickpoint; x >= 0; x--)
    {
        if ($qtext.charAt(x) === l) {
            $leftdelim = x + 1;
            break;
        }
        if ($qtext.charAt(x) === r) {
            break;
        }
    }
    /* find the character num of the right delimiter*/
    $rightdelim = null;
    for (var x = $clickpoint; x < $qtext.length; x++)
    {
        if ($qtext.charAt(x) === l) {
            break;
        }
        if ($qtext.charAt(x) === r) {
            $rightdelim = x;
            break;
        }
    }
    $word = null;
    if ($leftdelim !== null) {
        if ($rightdelim !== null) {
            /* $the_text = $("#id_questiontexteditable").text();*/
            $word = $qtext.substring($leftdelim, $rightdelim);
            /* Stores where it is in the string, e.g. if it is the only one it will be 0, if there are two it 
             * will be 1 etc etc
             */
        }

    }
    return $word;
}


