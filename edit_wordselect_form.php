    <?php

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
 * Defines the editing form for the wordselect question type.
 *
 * @package    qtype_wordselect
 * @copyright  2016 Marcus Green

 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();

/**
 * wordselect question editing form definition.
 *
 * @copyright  2016 Marcus Green

 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

class qtype_wordselect_edit_form extends question_edit_form {

    /**
     * 
     * @global type $PAGE
     * @global type $CFG
     * @param question_wizard_form $mform
     */
    protected function definition_inner($mform) {
        global $PAGE;
        $PAGE->requires->jquery();
        $PAGE->requires->jquery_plugin('ui');
        $PAGE->requires->jquery_plugin('ui-css');
        $PAGE->requires->jquery_plugin('serialize-object', 'qtype_wordselect');
        $PAGE->requires->strings_for_js(array('itemfeedbackerror','additemfeedback','selected','notselected'), 'qtype_wordselect');
        $PAGE->requires->js('/question/type/wordselect/questionedit.js');
    
        $mform->addElement('hidden', 'reload', 1);
        $mform->setType('reload', PARAM_RAW);
        $mform->addElement('text', 'wordfeedbackdata','',array('size'=>'80'));
        $mform->setType('wordfeedbackdata', PARAM_RAW);

        $mform->removeelement('questiontext');

        $mform->removeelement('generalfeedback');

        // Default mark will be set to 1 * number of fields.
        $mform->removeelement('defaultmark');


        /* popup for entering feedback for individual words */
        $mform->addElement('html', '<div id="id_feedback_popup" title="'.get_string('additemfeedback','qtype_wordselect').'" style="display:none;background-color:lightgrey" >');
             
        $mform->addElement('editor', 'selected', '', array('size' => 70, 'rows' => 4), $this->editoroptions);
        $mform->addElement('editor', 'notselected', '', array('size' => 70, 'rows' => 4), $this->editoroptions);
                
        $mform->addElement('html', '</div>');

         /* presented for clicking on the words once they have been given numberical ids */
        $mform->addElement('html', '<div id="id_questiontextfeedback" style="display:none;background-color:lightgrey" ></div>');


        $mform->addElement('editor', 'introduction', get_string('introduction', 'qtype_wordselect'), array('size' => 70, 'rows' => 2), $this->editoroptions);
        $mform->setType('introduction', PARAM_RAW);

        $mform->addHelpButton('introduction', 'introduction', 'qtype_wordselect');

        $mform->addElement('editor', 'questiontext', get_string('questiontext', 'question'), array('rows' => 10), $this->editoroptions);
        $mform->setType('questiontext', PARAM_RAW);
        $mform->addHelpButton('questiontext', 'questiontext', 'qtype_wordselect');
      
        $mform->addElement('button', 'wordfeedback', get_string('itemfeedbackbutton','qtype_wordselect'));
        $mform->addHelpButton('wordfeedback', 'feedback_button', 'qtype_wordselect');

      
        
        $mform->addElement('editor', 'generalfeedback', get_string('generalfeedback', 'question')
                , array('rows' => 10), $this->editoroptions);

        $mform->setType('generalfeedback', PARAM_RAW);
        $mform->addHelpButton('generalfeedback', 'generalfeedback', 'question');

        // The delimiting characters around fields.
        $delimitchars = array("[]" => "[ ]", "{}" => "{ }", "##" => "##", "@@" => "@ @");
        $mform->addElement('select', 'delimitchars', get_string('delimitchars', 'qtype_wordselect'), $delimitchars);
        $mform->addHelpButton('delimitchars', 'delimitchars', 'qtype_wordselect');

        // To add combined feedback (correct, partial and incorrect).
        $this->add_combined_feedback_fields(true);

        // Adds hinting features.
        $this->add_interactive_settings(true, true);
    }

    public function set_data($question) {
        /* accessing the form in this way is probably not correct style */
        $introduction = $this->get_introduction($question);

        /* ...this ensures introduction is available for image file processing */
        $this->_form->getElement('introduction')->setValue(array('text' => $introduction));

        /* this gets manipulated by javascript */
        $wordfeedbackdata = $this->get_wordfeedbackdata($question);
        $this->_form->getElement('wordfeedbackdata')->setValue(($wordfeedbackdata));

        question_edit_form::set_data($question);
    }

    public function get_wordfeedbackdata($question){
        $wordfeedbackdata = "";
        if (property_exists($question, 'wordfeedbackdata')) {
            return $question->wordfeedbackdata;
        } else {
            return "";
        }
    }
    public function get_introduction($question) {
        $introduction = "";
        if (property_exists($question, 'options')) {
            return $question->options->introduction;
        } else {
            return "";
        }
    }

    public function validation($fromform, $data) {
        $errors = array();
        /* don't save the form if there are no fields defined */
        $correctplaces = qtype_wordselect_question::get_correct_places($fromform['questiontext']['text'], $fromform['delimitchars']);
        if (count($correctplaces) == 0) {
            $errors['questiontext'] = get_string('nowordsdefined', 'qtype_wordselect');
        }
        if ($errors) {
            return $errors;
        } else {
            return true;
        }
        return $errors;
    }

    protected function data_preprocessing($question) {
        $question = parent::data_preprocessing($question);
        $question = $this->data_preprocessing_hints($question);
        if (empty($question->options)) {
            return $question;
        }
        $draftid = file_get_submitted_draft_itemid('introduction');
        $question->introduction = array();
        $question->introduction['text'] = file_prepare_draft_area(
                $draftid, // Draftid
                $this->context->id, // context
                'qtype_wordselect', // component
                'introduction', // filarea
                !empty($question->id) ? (int) $question->id : null, // itemid
                $this->fileoptions, // options
                $question->options->introduction // text.
        );

        /* format of introduction will always be the same as questiontext */
        $question->introduction['format'] = $question->questiontextformat;
        $question->introduction['itemid'] = $draftid;

        return $question;
    }

    public function qtype() {
        return 'wordselect';
    }

}
