
$('.collapsible-actions').hide();
$navbuttons =
        '<button id="nav_previous">Previous</button>\
        <button id="nav_combined">General feedback</button>\
        <button id="nav_combined">Combined feedback</button>\
        <button id="nav_multitries">Multi tries</button>\
        <button id="nav_tags">Tags</button>\
        <button id="nav_next">Next</button>';


$($navbuttons).insertAfter('.collapsible-actions');
$("#id_questiontext").closest('.form-group').hide();
$('#id_generalfeedback').closest('.form-group').hide();
$('#id_delimitchars').closest('.form-group').hide();

$('.ftoggler').hide();
$('#id_delmitchars').hide();

$('#id_combinedfeedbackhdr').hide();

$('#id_multitriesheader').hide();
$('#id_tagsheader').hide();
//$('#id_generalheader').hide();






