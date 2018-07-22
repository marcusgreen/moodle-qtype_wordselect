
$currentpage=0;
$('.collapsible-actions').hide();
$navbuttons =
        '<div id="question_nav">\
        <button id="nav_questiontext">Question Text</button>\
        <button id="nav_general">General feedback</button>\
        <button id="nav_combined">Combined feedback</button>\
        <button id="nav_multitries">Multi tries</button>\
        <button id="nav_tags">Tags</button>\
        </div>';
$($navbuttons).insertAfter('.collapsible-actions');

hideall();
$('#id_introduction').closest('.form-group').show()


$("#question_nav").find('button').click(function (event) {
    event.preventDefault();
    $("#question_nav").children().removeAttr('disabled');
    $(this).attr('disabled', 'disabled');
    hideall();
    if(this.id=='nav_questiontext'){
        $("#id_questiontext").closest('.form-group').show();
    }
      if(this.id=='nav_general'){
        $("#id_generalfeedback").closest('.form-group').show();
    }
      if(this.id=='nav_combined'){
        $("#id_combinedfeedbackhdr").show();    
    }
      if(this.id=='nav_multitries'){
          $("#id_multitriesheader").show();
    }
    if(this.id=='nav_tags'){
       $('#id_tagsheader').show();
    }    
});

function hideall(){ 
$("#id_generalheader").removeClass('collapsible');
$("#id_generalheader").removeClass('collapsed');
$("#id_combinedfeedbackhdr").removeClass('collapsible');
$("#id_combinedfeedbackhdr").removeClass('collapsed');
$('#id_multitriesheader').removeClass('collapsible');
$("#id_multitriesheader").removeClass('collapsed');
$('#fitem_id_questiontext').hide();
$('#fitem_id_generalfeedback').hide();
$('#id_introduction').closest('.form-group').hide()
$("#id_delimitchars").closest('.form-group').hide();
$('#id_submitbutton').hide();
$('#id_updatebutton').hide();
$("#id_questiontext").closest('.form-group').hide();
$('#id_generalfeedback').closest('.form-group').hide();
//$('.ftoggler').hide();
$('#id_combinedfeedbackhdr').hide();
$('#id_multitriesheader').hide();
$('#id_tagsheader').removeClass('collapsible');
$('#id_tagsheader').removeClass('collapsed');
$('#id_tagsheader').hide();
$('#id_tagsheader').find('legend').hide();
}






