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
        /*$("#id_questiontexteditable").css("backgroundColor", 'lightgrey');*/
        var fbheight=$("#id_questiontexteditable").css("height");
        var fbwidth=$("#id_questiontexteditable").css("width");
        $("#id_questiontexteditable").css("display", 'none');        
        var ed=$("#id_questiontexteditable").closest(".editor_atto_content_wrap");
        $("#id_questiontextfeedback").css({
            position: "absolute",
            width: "100%",
            height: "100%",
            top: 0,
            left: 0,
            background: "lightgrey",
            color: "black",
            display: "block"
        }).appendTo(ed).css("position", "relative");
        var text =$("#id_questiontexteditable").prop("innerHTML");
        $("#id_questiontextfeedback").html(text); 
        var el = document.getElementById("id_questiontextfeedback");
        wrapContent(el);
        $("#id_questiontextfeedback").css('height',fbheight);
        $("#id_questiontextfeedback").css('width',fbwidth);
        $("#id_questiontextfeedback").addClass("editor_atto_content");
        $("#id_gapfeedback").attr('value','Edit Question Text');
    } else {
        $("#id_questiontexteditable").css("display", 'block');      
        $("#id_questiontextfeedback").css("display", "none");
        $("#id_questiontexteditable").attr('contenteditable', 'true');
        $("#fitem_id_questiontext").find('button').removeAttr("disabled");
        $("#id_questiontexteditable").css("backgroundColor", 'white');
        $("#id_feedback_popup").css("display", "none");
        $("#id_gapfeedback").attr('value','Add Word Feedback');
    }
});

$("#fitem_id_questiontext").on("click", function () {
    if (!$('#id_questiontexteditable').get(0).isContentEditable) {
        delimitchars = $("#id_delimitchars").val();
        /*l and r for left and right */
        l = delimitchars.substr(0, 1);
        r = delimitchars.substr(1, 1);
        rangy.init();
        var sel = rangy.getSelection();
        var item = get_selected_item(sel);
        if (item.text != '') {
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
    var space = /\s/g;
    
    for (var x = clickpoint; x >= 0; x--)
    {
        var char = questiontext.charAt(x);
        if (char === l || char.match(space)) {
            leftdelim = x + 1;
            break;
        }
        if (char === r || char.match(space)) {
            break;
        }
    }
    /* find the character num of the right delimiter*/
    rightdelim = 0;
    for (var x = clickpoint; x < questiontext.length; x++)
    {
        var char = questiontext.charAt(x);
        if (char === l || char.match(space)) {
            rightdelim = x;
            break;
        }
        if (char === r || char.match(space)) {
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
    item.offset = itemoffset(uptothisitem);
    return item;
}
/**
 * 
 * @param {string} uptothisitem - the questiontext behind this item 
 * @param {string} itemwithdelim 
 * @returns {Number} offset 
 */
function Xitemoffset(uptothisitem, itemwithdelim) {
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
/**
 * 
 * @param {string} uptothisitem - the questiontext behind this item 
 * @returns {Number} offset 
 */
function itemoffset(uptothisitem) {
    var itemstothis=uptothisitem.split(/\s/);
    return itemstothis.length;
}
function toArray(obj) {
  var arr = [];
  for (var i=0, iLen=obj.length; i<iLen; i++) {
    arr.push(obj[i]);
  }
  return arr;
}


// Wrap the words of an element and child elements in a span
// Recurs over child elements, add an ID and class to the wrapping span
// Does not affect elements with no content, or those to be excluded
var wrapContent = (function() {
    var count=0;
    return function(el) {
    
    // If element provided, start there, otherwise use the body
    el = el && el.parentNode? el : document.body;

    // Get all child nodes as a static array
    var node, nodes = toArray(el.childNodes);
    if(el.id=="id_questiontextfeedback" && (count >0)){
             count=0;
    }
    var frag, parent, text;
    var re = /\S+/;
    var sp, span = document.createElement('span');

    // Tag names of elements to skip, there are more to add
    var skip = {'script':'', 'button':'', 'input':'', 'select':'',
                'textarea':'', 'option':''};

    // For each child node...
    for (var i=0, iLen=nodes.length; i<iLen; i++) {
      node = nodes[i];
        // If it's an element, call wrapContent
      if (node.nodeType == 1 && !(node.tagName.toLowerCase() in skip)) {
        wrapContent(node);

      // If it's a text node, wrap words
      } else if (node.nodeType == 3) {

        // Match sequences of whitespace and non-whitespace
        text = node.data.match(/\s+|\S+/g);

        if (text) {

          // Create a fragment, handy suckers these
          frag = document.createDocumentFragment();
          for (var j=0, jLen=text.length; j<jLen; j++) {
            // If not whitespace, wrap it and append to the fragment
            if (re.test(text[j])) {
              sp = span.cloneNode(false);
              sp.id = count++;
              sp.className = 'foo';
              sp.appendChild(document.createTextNode(text[j]));
              frag.appendChild(sp);

            // Otherwise, just append it to the fragment
            } else {
              frag.appendChild(document.createTextNode(text[j]));
            }
          }
        }

        // Replace the original node with the fragment
        node.parentNode.replaceChild(frag, node);
      }
    
    }
  }
 }());

