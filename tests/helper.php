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
 * Contains the helper class for the select missing words question type tests.
 *
 * @package    qtype_wordselect
 * @copyright  2013 Marcus Green
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();

/**
 * For testing the wordselect question type
 * @package    qtype_wordselect
 * @copyright  2018 Marcus Green
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_wordselect_test_helper extends question_test_helper {

    /**
     * must be implemented or class made abstract
     * @return array
     */
    public function get_test_questions() {
        return array('catmat');
    }

    /**
     * Create an instance of the question for testing
     * @param object $type
     * @param string $questiontext
     * @param array $options
     * @return \qtype_wordselect_question
     */
    public static function make_question($type, $questiontext='The cat [sat]', $options = array('delimitchars' => '[])')) {
        question_bank::load_question_definition_classes($type);
        $question = new qtype_wordselect_question();
        $question->questiontext = $questiontext;
        $question->delimitchars = $options['delimitchars'];
        test_question_maker::initialise_a_question($question);
        $question->qtype = question_bank::get_qtype('wordselect');
        $question->introduction = '';
        $question->correctfeedback = '';
        return $question;
    }

    /**
     * Data to create a wordselect question for unit tests
     * @return stdClass
     */
    public function get_wordselect_question_form_data_catmat() {
        $fromform = new stdClass();

        $fromform->name = 'Cat cliche';
        $fromform->introduction = ['text' => 'Highlight the nouns in this sentence:', 'format' => FORMAT_HTML];
        $fromform->questiontext = ['text' => 'The [cat] sat on the [mat].', 'format' => FORMAT_HTML];
        $fromform->generalfeedback = ['text' => "You should have selected 'cat' and 'mat'.", 'format' => FORMAT_HTML];
        $fromform->defaultmark = 1;
        $fromform->penalty = 0.3333333;

        $fromform->wordpenalty = 1;
        $fromform->delimitchars = '[]';

        test_question_maker::set_standard_combined_feedback_form_data($fromform);

        return $fromform;
    }
}
