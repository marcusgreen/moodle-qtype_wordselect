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
 * @copyright  2017 Marcus Green

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
     * Add question-type specific form fields.
     *
     * @param MoodleQuickForm $mform the form being built.
     */
    protected function definition_inner($mform) {
        $mform->removeelement('questiontext');

        $mform->removeelement('generalfeedback');

        // Default mark will be set to 1 * number of fields.
        $mform->removeelement('defaultmark');

        $mform->addElement('editor', 'introduction',
                get_string('introduction', 'qtype_wordselect'), array('size' => 70, 'rows' => 2),
                $this->editoroptions);
        $mform->setType('introduction', PARAM_RAW);

        $mform->addHelpButton('introduction', 'introduction', 'qtype_wordselect');

        $mform->addElement('editor', 'questiontext', get_string('questiontext', 'question'),
                array('rows' => 15), $this->editoroptions);
        $mform->setType('questiontext', PARAM_RAW);

        $mform->addHelpButton('questiontext', 'questiontext', 'qtype_wordselect');

        $mform->addElement('editor', 'generalfeedback', get_string('generalfeedback', 'question')
                , array('rows' => 10), $this->editoroptions);

        $mform->setType('generalfeedback', PARAM_RAW);
        $mform->addHelpButton('generalfeedback', 'generalfeedback', 'question');
        $this->add_penalty($mform);

        // The delimiting characters around fields.
        $delimitchars = array("[]" => "[ ]", "{}" => "{ }", "##" => "##", "@@" => "@ @");
        $mform->addElement('select', 'delimitchars', get_string('delimitchars', 'qtype_wordselect'), $delimitchars);
        $mform->addHelpButton('delimitchars', 'delimitchars', 'qtype_wordselect');

        // To add combined feedback (correct, partial and incorrect).
         $this->add_combined_feedback_fields(false);

        // Adds hinting features.
        $this->add_interactive_settings(true, true);
    }

    /**
     * Add in data from custom fields
     * @param array $question
     */
    public function set_data($question) {
        /* accessing the form in this way is probably not correct style */
        $introduction = $this->get_introduction($question);

        /* ...this ensures introduction is available for image file processing */
        $this->_form->getElement('introduction')->setValue(array('text' => $introduction));
        question_edit_form::set_data($question);
    }

    /**
     * Get the introducory text (where words are never selectable)
     *
     * @param stdClass|array $question
     * @return strings
     */
    public function get_introduction($question) {
        if (property_exists($question, 'options')) {
            return $question->options->introduction;
        } else {
            return "";
        }
    }
    /**
     * Check the question text is valid, specifically that
     * it contains at lease one selectable item.
     *
     * @param array $fromform
     * @param array $data
     * @return boolean
     */
    public function validation($fromform, $data) {
        $errors = array();
        /* don't save the form if there are no fields defined */
        $ws = new qtype_wordselect_question();
        $ws->init($fromform['questiontext']['text'], $fromform['delimitchars']);
        $correctplaces = $ws->get_correct_places($fromform['questiontext']['text'],
                $fromform['delimitchars']);
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
    /**
     * Perform any preprocessing needed on the data passed to {@link set_data()}
     * before it is used to initialise the form.
     * @param object $question the data being passed to the form.
     * @return object $question the modified data.
     */
    protected function data_preprocessing($question) {
        $question = parent::data_preprocessing($question);
        $question = $this->data_preprocessing_combined_feedback($question, false);
        $question = $this->data_preprocessing_hints($question, true, true);
        if (empty($question->options)) {
            return $question;
        }
        $draftid = file_get_submitted_draft_itemid('introduction');
        $question->introduction = array();
        $question->introduction['text'] = file_prepare_draft_area(
            $draftid,           // Draftid
            $this->context->id, // context
            'qtype_wordselect',         // component
            'introduction',     // filarea
            !empty($question->id) ? (int) $question->id : null, // itemid
            $this->fileoptions, // options
            $question->options->introduction // text.
        );

        /* format of introduction will always be the same as questiontext */
        $question->introduction['format'] = $question->questiontextformat;
        $question->introduction['itemid'] = $draftid;

        return $question;
    }
    /**
     * Add penalty for incorrectly selected text items. This is a fraction that is
     * multiplied by the number of correct responses. So if you select 2 correct
     * and 2 incorrect the and the penalty is .5 the calculation is 2*.5 =1 (of the incorrect)
     * then deduct 1 from the correct count of 2 giving  final result of 1
     * @param object $mform
     */
    protected function add_penalty($mform) {
        $config = get_config('qtype_wordselect');
        $penalties = array(
            1.0000000,
            0.5000000,
            0.3333333,
            0.2500000,
            0.2000000,
            0.1000000,
            0.0000000
        );
        if (!empty($this->question->wordpenalty) && !in_array($this->question->wordpenalty, $penalties)) {
            $penalties[] = $this->question->wordpenalty;
            sort($penalties);
        }

        $penaltyoptions = array();
        foreach ($penalties as $wordpenalty) {
            $penaltyoptions["{$wordpenalty}"] = (100 * $wordpenalty) . '%';
        }

        $mform->addElement('select', 'wordpenalty', get_string('wordpenalty', 'qtype_wordselect'), $penaltyoptions);
        $mform->addHelpButton('wordpenalty', 'wordpenalty', 'qtype_wordselect');
        $mform->setDefault('wordpenalty', $config->wordpenalty);
    }

    /**
     * Name of this question type
     * @return string
     */
    public function qtype() {
        return 'wordselect';
    }

}
