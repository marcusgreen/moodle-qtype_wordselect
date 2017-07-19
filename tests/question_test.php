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
 * Unit tests for the wordselect question definition class.
 *
 * @package    qtype
 * @subpackage wordselect
 * @copyright  2016 Marcus Green
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();

global $CFG;

require_once($CFG->dirroot . '/question/type/questionbase.php');
require_once($CFG->dirroot . '/question/engine/tests/helpers.php');
require_once($CFG->dirroot . '/question/type/wordselect/tests/helpers.php');
require_once($CFG->dirroot . '/question/type/wordselect/question.php');

/**
 * Unit tests for the wordselect question definition class.
 *
 * @copyright  2016 Marcus Green
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_wordselect_question_test extends advanced_testcase {

    public function test_get_expected_data() {
        $question = qtype_wordselect_test_helper::make_question('wordselect');
        $expecteddata = ['p0' => 'raw_trimmed', 'p1' => 'raw_trimmed',
                    'p2' => 'raw_trimmed'];

        $this->assertEquals($question->get_expected_data(), $expecteddata);
    }

    public function test_multiword_delim() {
        $questiontext = 'The [[cat sat]] and the cow [jumped]';
        $question = qtype_wordselect_test_helper::make_question('wordselect', $questiontext);
        $correctplaces = $question->get_correct_places($question->questiontext, "[]");
        $this->assertTrue($question->is_correct_place($correctplaces, 1));
        $this->assertFalse($question->is_correct_place($correctplaces, 5));
    }

    public function test_summarise_response() {
        $question = qtype_wordselect_test_helper::make_question('wordselect');
        $response = array('p2' => 'on');
        $this->assertEquals(($question->summarise_response($response)), ' sat ');
    }

    public function test_grade_response() {
        $question = qtype_wordselect_test_helper::make_question('wordselect');
        $response = array('p2' => 'on');
        list($fraction, $state) = $question->grade_response($response);
        $this->assertEquals($fraction, 1);
        /* This question type uses subtractive marking to mitigate any benefit from a
         * strategy of click on everything to get full marks. The mark for any incorrect selections
         * is deducted from the marks for correct selections down to zero.
         */
        $questiontext = 'The cat [sat] and the cow [jumped]';
        $question = qtype_wordselect_test_helper::make_question('wordselect', $questiontext);
        $response = array('p2' => 'on', 'p6' => 'off');
        list($fraction, $state) = $question->grade_response($response);
        $this->assertEquals($fraction, .5);
    }

    public function test_is_complete_response() {
        $questiontext = 'The cat [sat] and the cow [jumped]';
        $question = qtype_wordselect_test_helper::make_question('wordselect', $questiontext);
        /*
         * A response is considered complete if there is at least one item selected. In this
         * case it is a "correct" item, i.e. one with delimitcharacters but it doesn't have to be
         */
        $response = array('p2' => 'on');
        $this->assertTrue($question->is_complete_response($response));
        /* this time nothing is selected */
        $response = array();
        $this->assertFalse($question->is_complete_response($response));
    }

    public function test_is_correct_place() {
        $questiontext = 'The cat [sat] and the cow [jumped]';
        $question = qtype_wordselect_test_helper::make_question('wordselect', $questiontext);
        $correctplaces = $question->get_correct_places($question->questiontext, "[]");
        $this->assertTrue($question->is_correct_place($correctplaces, 2));
    }

    public function test_is_word_selected() {
        $questiontext = 'The cat [sat] and the cow [jumped]';
        $question = qtype_wordselect_test_helper::make_question('wordselect', $questiontext);
        $response = array('p1' => 'on');
        $this->assertTrue($question->is_word_selected(1, $response));
        $response = array('1' => 'on');
        $this->assertFalse($question->is_word_selected(1, $response));
    }

    public function test_get_correct_places() {
        $question = qtype_wordselect_test_helper::make_question('wordselect');
        /* counting from 0 the correct place is 2 (i.e. the word sat) */
        $correctplaces = ['0' => 2];
        $this->assertEquals($question->get_correct_places($question->questiontext, '[]'), $correctplaces);
        $question = qtype_wordselect_test_helper::make_question('wordselect', '[the] [cat] [[sat]]');
        /* counting from 0 the correct place is 2 (i.e. the word sat) */
        $correctplaces = ['0' => 2];
        $this->assertEquals($question->get_correct_places($question->questiontext, '[]'), $correctplaces);
    }

    public function test_get_correct_response() {
        $question = qtype_wordselect_test_helper::make_question('wordselect');
        /* The third word is the correct response */
        $correctresponse = array('p2' => 'on');
        $this->assertEquals($question->get_correct_response(), $correctresponse);
    }

    public function test_get_correct_response_multi() {
        $questiontext = 'The [[cat sat]]';
        $question = qtype_wordselect_test_helper::make_question('wordselect', $questiontext);
        /* The second word is the correct response */
        $correctresponse = array('p1' => 'on');
        $this->assertEquals($question->get_correct_response(), $correctresponse);
    }

    public function test_get_words() {
        $question = qtype_wordselect_test_helper::make_question('wordselect');
        /* The cat [sat] splits into three "words" */
        $this->assertEquals(count($question->get_words()), 3);
        /* This will break into two "words" */
        $questiontext = 'The [[cat sat]]';
        $question = qtype_wordselect_test_helper::make_question('wordselect', $questiontext);
        $this->assertEquals(count($question->get_words()), 2);
    }
       public function test_get_wrong_responsecount() {
        $question = qtype_wordselect_test_helper::make_question('wordselect');
        $correctplaces=array(1);
        /* click word 0 "The" which is an incorrect response so the wrong response count is 1 */
        $response = array('p0' => 'on');        
        $this->assertEquals($question->get_wrong_responsecount($correctplaces,$response),1);      
         /* click words 0 and 1 "The" and "cat" which is one correct and one incorrect word so the count is still 1*/
        $response = array('p0' => 'on','p1'=>'on');        
        $this->assertEquals($question->get_wrong_responsecount($correctplaces,$response),1);      
    }

}
