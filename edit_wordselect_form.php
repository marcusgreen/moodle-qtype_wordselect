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
 * @package    qtype
 * @subpackage wordselect
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

    protected function definition_inner($mform) {
        $mform->removeelement('questiontext');

        $mform->removeelement('generalfeedback');

        // Default mark will be set to 1 * number of fields.
        $mform->removeelement('defaultmark');

        $mform->addElement('editor', 'introduction', 'Introduction', array('size' => 70, 'rows' => 2), $this->editoroptions);
        $mform->addElement('editor', 'questiontext', get_string('questiontext', 'question'), array('rows' => 15), $this->editoroptions);

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

        $this->_form->getElement('introduction')->setValue(array('text' => $introduction));
        question_edit_form::set_data($question);
    }

    public function get_introduction($question) {
        $introduction = "";
        if (property_exists($question, 'options')) {
            return $question->options->introduction;
        } else {
            return "";
        }
    }

    protected function data_preprocessing($question) {
        $question = parent::data_preprocessing($question);
        $question = $this->data_preprocessing_hints($question);

        return $question;
    }

    public function qtype() {
        return 'wordselect';
    }

}
