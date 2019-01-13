define(
    [
        'jquery',
        'core/fragment',
        'core/modal_factory',
        'core/modal_events',

    ],
    function($, Fragment) {
        return {
            init: function() {

                $('#id_itemsettings_button').on('click', function(e) {
                    alert('hello4');
                });

                var loadFormFragment = function (data) {
                    var params = {
                        property: 'value'
                    };
                    Fragment.loadFragment('qtype_wordselect', 'wordfeedback', M.cfg.contextid, params).done(
                            
                    );
                };
                data=0;
                loadFormFragment(data);
        }
    };
});


/* var loadFormFragment = function(stage, data) {
                var params = {stage: stage};
                if (data) {
                    params.formdata = data;
                }

                var spinner = ajaxOverlay.applyOverlay('#app');
                Fragment.loadFragment('report_globalassign', 'createassign', M.cfg.contextid, params).done(
                    function(html, js) {
                        spinner.removeOverlay();

                        modalCreateAssign.show('Create assignment',
                            '<div id="createAssignment">' + html + '</div>',
                            '<div id="createAssignDialogFooter"></div>', true
                        );
                        runJS(js);
                        $('#id_submissiontypes').addClass('collapsed');
                        $('#id_course').change(function(e) {
                            var courseId = (e.currentTarget.value);
                            if (courseId == 0) {
                                updateSelect({0:'-'});
                                // We have to set disabled after we've updated the select.
                                $('#id_section').attr('disabled', 'disabled');
                                return;
                            }
                            $('#id_section').attr('disabled', 'disabled');
                            var data = {
                                'courseid': courseId
                            };

                            rest.get('course_sections', data)
                                .then(function(responseObj) {
                                    if (!responseObj.success) {
                                        // TODO localise.
                                        modalInfo.show('Error', 'Unknown error: Failed to get course sections');
                                        return;
                                    }
                                    updateSelect(responseObj.sections);
                                });
                        });
                    }
                );
            };

*/
/*
require(['jquery', 'core/modal_factory'], function($, ModalFactory) {
    var trigger = $('#id_itemsettings_button');
    ModalFactory.create({
      title: 'test title',
      body: '<p>test body content</p>',
      footer: 'test footer content',
    }, trigger)
    .done(function(modal) {
      // Do what you want with your new modal.

      // Maybe... add a class to the modal dialog, to be able to style it.
      modal.getRoot().addClass('mydialog');
    });
  });
  */