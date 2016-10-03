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
var feedback = new Array();
if (feedbackdata > "") {
    var obj = JSON.parse(feedbackdata);
    for (var o in obj) {
        feedback.push(obj[o]);
    }
}
var itemkey = 0;

/**
 * @param {object} item
 * @returns {Array|itemfeedback}
 */
function get_feedback(item) {
    itemfeedback = new Array();
    for (var fb in feedback) {
        if (feedback[fb].word == item.text) {
            if (feedback[fb].offset == item.offset) {
                itemfeedback[0] = feedback[fb];
            }
        }
    }
    return itemfeedback;
}
/**
 * @param {object} item
 * @returns {Array|feedback}
 */
function add_or_update(item) {
    found = false;
    for (var fb in feedback) {
        if (feedback[fb].word == item.text) {
            if (feedback[fb].offset == item.offset) {
                feedback[fb].selected = $("#id_selectededitable").html();
                feedback[fb].notselected = $("#id_notselectededitable").html();
                found = true;
            }
        }
    }
    if (found == false) {
        /* if there is no record for this word add one 
         * a combination of wordtext and offset will be unique*/
        itemkey++;
        var itemfeedback = {
            id: 'id' + itemkey,
            question: $("input[name=id]").val(),
            selected: $("#id_selectededitable").html(),
            notselected: $("#id_notselectededitable").html(),
            word: item.text,
            offset: item.offset
        };
        feedback.push(itemfeedback);
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
        delimitchars = $("#id_delimitchars").val();
        /*l and r for left and right */
        l = delimitchars.substr(0, 1);
        r = delimitchars.substr(1, 1);
        rangy.init();
        var sel = rangy.getSelection();
        var item = get_selected_item(sel);
        if (item.text != null) {
            itemfeedback = get_feedback(item);
            if (itemfeedback == null || itemfeedback.length == 0) {
                $("#id_selectededitable").html('');
                $("#id_notselectededitable").html('');
            } else {
                $("#id_selectededitable").html(itemfeedback[0].selected);
                $("#id_notselectededitable").html(itemfeedback[0].notselected);
            }
            $("label[for*='id_selected']").text("When " + item.text + " is selected");
            $("label[for*='id_notselected']").text("When " + item.text + " is not selected");
            $("#id_feedback_popup").dialog({
                height: 500,
                width: 600,
                modal: true,
                buttons: [
                    {
                        text: "OK",
                        click: function () {
                            feedback = add_or_update(item);
                            var JSONstr = JSON.stringify(feedback);
                            $("[name='wordfeedbackdata']").val(JSONstr);
                            $(this).dialog("close");
                        }

                    }
                ]
            });
        }
    }

});
/**
 * 
 * @param {string} sel
 * @returns {item}
 */
function get_selected_item(sel) {
    var questiontext = sel.anchorNode.nodeValue;
    var clickpoint = sel.focusOffset;
    var node = sel.focusNode;
    /* find the character num of the left delimiter*/
    leftdelim = 0;
    for (var x = clickpoint; x >= 0; x--)
    {
        if (questiontext.charAt(x) === l) {
            leftdelim = x + 1;
            break;
        }
        if (questiontext.charAt(x) === r) {
            break;
        }
    }
    /* find the character num of the right delimiter*/
    rightdelim = 0;
    for (var x = clickpoint; x < questiontext.length; x++)
    {
        if (questiontext.charAt(x) === l) {
            break;
        }
        if (questiontext.charAt(x) === r) {
            rightdelim = x;
            break;
        }
    }
    item = {};
    if (leftdelim !== null) {
        if (rightdelim !== null) {
            item.text = questiontext.substring(leftdelim, rightdelim);
        }
    }
    var itemwithdelim = l + item.text + r;
    var uptothisitem = questiontext.substring(0, leftdelim);
    item.offset = itemoffset(uptothisitem, itemwithdelim);
    return item;
}
/**
 * 
 * @param {string} uptothisitem - the questiontext behind this item 
 * @param {string} itemwithdelim 
 * @returns {Number} offset 
 */
function itemoffset(uptothisitem, itemwithdelim) {
    var offset = 0;
    var pos = 0;
    var step = itemwithdelim.length;
    while (true) {
        pos = uptothisitem.indexOf(itemwithdelim, pos);
        if (pos >= 0) {
            ++offset;
            pos += step;
        } else
            break;
    }
    return offset;
}


