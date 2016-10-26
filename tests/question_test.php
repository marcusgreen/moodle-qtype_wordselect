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
class qtype_wordselect_question_test extends UnitTestCase {
    public function test_get_expected_data() {
        $questiontext='The cat [sat]';
        $question = qtype_wordselect_test_helper::make_question('wordselect',$questiontext);        
        $expecteddata = ['p0' => 'raw_trimmed', 'p1' => 'raw_trimmed','p2'=>'raw_trimmed'];
        $this->assertEquals($question->get_expected_data(), $expecteddata);
    }
       public function test_summarise_response() {
         $questiontext='The cat [sat] on the mat';
         $question = qtype_wordselect_test_helper::make_question('wordselect',$questiontext);
         $response = array('p2'=>'on');
         $this->assertEquals(($question->summarise_response($response)), ' sat ');
    }
 
}
