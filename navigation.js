$(function () {

    $("#question_nav").on('click', function (e) {
        e.preventDefault();
        /* set every link to blue */
        $("#question_nav").children().css('color', '#0070a8');


        $(".displayoff").css("display", "block");
        $("[id^='fitem']").css("display", "none");

        // $("#fitem_id_" + e.target.id).css("display", "inline");

        if (e.target.id === 'introduction') {
            $('#fgroup_id_currentgrp').css("display", "block");
            $('#fitem_id_categorymoveto').css("display","block")
            $('#fitem_id_introduction').css("display", "block");
            $('#fitem_id_name').css("display", "block");
          
        } else {
            $('#fgroup_id_currentgrp').css("display", "none");
            $('#fitem_id_categorymoveto').css("display","none")
            $('#fitem_id_introduction').css("display", "none");
            $('#fitem_id_name').css("display", "none");

        }

        if (e.target.id === 'questiontext') {
            $('#fitem_id_questiontext').css("display", "inline");
        } else {
            $('#fitem_id_questiontext').css("display", "none");
        }

        if (e.target.id === 'generalfeedback') {
            $("#fitem_id_generalfeedback").css("display", "block");
            $("#fitem_id_delimitchars").css("display", "inline");
        }

        if (e.target.id === 'combinedfeedback') {
            $("#fitem_id_correctfeedback").css("display", "block");
            $("#fitem_id_partiallycorrectfeedback").css("display", "block");
            $("#fitem_id_incorrectfeedback").css("display", "block");

        } else {
            $("#fitem_id_correctfeedback").css("display", "none");
            $("#fitem_id_partiallycorrectfeedback").css("display", "none");
            $("#fitem_id_incorrectfeedback").css("display", "none");
        }

        if (e.target.id === 'multitries') {
            $("[id^='fitem_id_hint_']").css("display", "block");
            $("[id^='fgroup_id_hintoptions_']").css("display", "block");
            $("#id_addhint").css("display", "block");
            $("#fitem_id_addhint").css("display", "block");

        } else {
            $("[id^='fitem_id_hint_']").css("display","none");
            $("[id^='fgroup_id_hintoptions_']").css("display","none");
        }
        if (e.target.id === 'complete') {
           $("#fitem_id_tags").css("display","inline");
           $("#fitem_id_created").css("display","inline");
           $("#fitem_id_modified").css("display","inline");
           $("#savecontinuepreview").css("display","inline");
        } else {
            $("#savecontinuepreview").css("display", "none");
        }
        /* set the clicked link to black */
        $('#a_' + e.target.id).css("color", "black");
    });

});
$(document).ready(function () {
    // $(".collapse-all").css("display", "none");
    //   $("#id_combinedfeedbackhdr").css("display", "none");
    // $("[id^='fitem']").css("display", "none");
    //$("[id^='introduction']").css('display', 'inline');
    // $("[id^='fgroup_id_hintoptions_']").css("display", "none");
    //  $("#id_submitbutton").css("display", "none");
    //  $("#id_cancel").css("display", "none");
});

