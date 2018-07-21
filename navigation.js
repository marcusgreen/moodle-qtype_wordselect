
$('.collapsible-actions').hide();
$navbuttons =
        '<div id="question_nav">\
        <button id="nav_previous">Previous</button>\
        <button id="nav_combined">General feedback</button>\
        <button id="nav_combined">Combined feedback</button>\
        <button id="nav_multitries">Multi tries</button>\
        <button id="nav_tags">Tags</button>\
        <button id="nav_next">Next</button>\
        </div>';



$($navbuttons).insertAfter('.collapsible-actions');

$("#nav_combined").click(function(event){
    alert('nav combined');
});

    
$("#question_nav").click(function(event){
    event.preventDefault();
});
     
$('#fitem_id_questiontext').hide();
$('#fitem_id_generalfeedback').hide();
$('#id_delimitchars').hide();

$('#id_submitbutton').hide();
$('#id_updatebutton').hide();


$("#id_questiontext").closest('.form-group').hide();
$('#id_generalfeedback').closest('.form-group').hide();

$('.ftoggler').hide();


$('#id_combinedfeedbackhdr').hide();

$('#id_multitriesheader').hide();
$('#id_tagsheader').hide();
//$('#id_generalheader').hide();






