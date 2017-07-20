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
 * @copyright  2017 Marcus Green
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();


require_once($CFG->dirroot . '/question/type/wordselect/question.php');

class qtype_wordselect_test_helper extends question_test_helper {

    /**
     * required by parent class
     * @return array
     */
    public function get_test_questions() {
        return array();
    }
/**
 * Create an instance of the question for testing
 * 
 * @param string $type probably not necessary
 * @param string $questiontext
 * @param array $options
 * @return \qtype_wordselect_question
 */
    public static function make_question($type='wordselect', $questiontext='The cat [sat]', $options = array('delimitchars' => '[])')) {
        question_bank::load_question_definition_classes($type);
        $question = new qtype_wordselect_question();
        $question->questiontext = $questiontext;
        test_question_maker::initialise_a_question($question);
        $correctplaces = $question->get_correct_places($questiontext, $options['delimitchars']);
        return $question;
    }

}
