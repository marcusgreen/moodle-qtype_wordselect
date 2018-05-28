
$('.collapsible-actions').hide();
$navbuttons =
       '<button id="nav_combined">General feedback</button>\
        <button id="nav_combined">Combined feedback</button>\
        <button id="nav_multitries">Multi tries</button>\
        <button id="nav_tags">Tags</button>';



$($navbuttons).insertAfter('.collapsible-actions');

$('#id_generalfeedback').closest('.form-group').hide();
$('.ftoggler').hide();
$('#id_combinedfeedbackhdr').hide();

$('#id_multitriesheader').hide();
$('#id_tagsheader').hide();
//$('#id_generalheader').hide();






