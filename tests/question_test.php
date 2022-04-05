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
 * @package    qtype_wordselect
 * @subpackage wordselect
 * @copyright  2016 Marcus Green
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace qtype_wordselect;
defined('MOODLE_INTERNAL') || die();

global $CFG;

use \qtype_wordselect_test_helper as helper;


require_once($CFG->dirroot . '/question/type/questionbase.php');
require_once($CFG->dirroot . '/question/engine/tests/helpers.php');

require_once($CFG->dirroot . '/question/type/wordselect/tests/helper.php');

require_once($CFG->dirroot . '/question/type/wordselect/question.php');
require_once($CFG->dirroot . '/question/type/wordselect/renderer.php');

/**
 * Unit tests for the wordselect question definition class.
 *
 * @copyright  2016 Marcus Green
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @coversDefaultClass \question\type\wordselect\question
 */
class question_test extends \advanced_testcase {
    /**
     * Test the behaviour of get_words() method.
     *
     * @covers ::get_words
     */
    public function test_get_words() {
        // ... this markTestSkipped().
        $questiontext = 'cat [sat] cow [jumped]';
        $question = helper::make_question('wordselect', $questiontext);
        $items = $question->get_words();
        $this->assertEquals($items[0]->get_text(), 'cat');
        $this->assertEquals($items[1]->get_text(), ' ');
        $this->assertEquals($items[2]->get_text(), '[sat]');

        $questiontext = 'cat [[sat]] cow [jumped]';
        $question = helper::make_question('wordselect', $questiontext);
        $items = $question->get_words();
        $this->assertEquals($items[0]->get_text(), 'cat');
        $this->assertEquals($items[1]->get_text(), ' ');
        /* with html spaces (&nbsp;) */
        $questiontext = 'cat&nbsp;&nbsp;[[sat]]';
        $question = helper::make_question('wordselect', $questiontext);
        $items = $question->get_words();
    }
    /**
     * Test the behaviour of test_stripdelim() method.
     *
     * @covers ::test_stripdelim
     */
    public function test_stripdelim() {
        $question = helper::make_question('wordselect');
        $stripped = $question->stripdelim('[word]');
        $this->assertEquals('word', $stripped);
    }
    /**
     * Test get_expected_data() method.
     * The type of data that will be received so it can be cleaned
     *
     * @covers ::test_get_expected_data
     */
    public function test_get_expected_data() {
        $question = helper::make_question('wordselect');
        $expecteddata = [
            'p0' => 'raw_trimmed',
            'p1' => 'raw_trimmed',
            'p2' => 'raw_trimmed',
            'p3' => 'raw_trimmed',
            'p4' => 'raw_trimmed'
        ];
        $this->assertEquals($question->get_expected_data(), $expecteddata);
    }
    /**
     * Test summarise_response method.
     * The data that is shown in the summarise responses report
     *
     * @covers ::test_summarise_response
     */
    public function test_summarise_response() {
        $question = helper::make_question('wordselect');
        $response = array('p2' => 'on');
        /* The cat [sat]
        p0 is The p1 is space p2 is cat
         * */
        $this->assertEquals(' cat ', $question->summarise_response($response));
    }
    /**
     * This question type uses subtractive marking to mitigate any benefit from a
     * strategy of click on everything to get full marks. The mark for any incorrect selections
     * is deducted from the marks for correct selections down to zero.
     *
     * @covers ::grade_response
     */
    public function test_grade_response() {
        $question = helper::make_question('wordselect');
        $response = array('p4' => 'on');
        list($fraction, $state) = $question->grade_response($response);
        $this->assertEquals(1, $fraction);
        $questiontext = 'The cat [sat] and the cow [jumped]';
        $question = helper::make_question('wordselect', $questiontext);
        $response = array('p4' => 'on', 'p6' => 'off');
        list($fraction, $state) = $question->grade_response($response);
        $this->assertEquals($fraction, .5);
    }
    /**
     * Called when using interactive with multiple tries question behaviour
     *
     * @covers ::compute_final_grade
     */
    public function test_compute_final_grade() {
        $question = helper::make_question('wordselect');
        $responses[] = ['p4' => 'on'];
        $totaltries = 1;
        $fraction = $question->compute_final_grade($responses, $totaltries);
        $this->assertEquals($fraction, 1, 'All correct responses should return fraction of 1');
    }
    /**
     * A response is considered complete if there is at least one item selected. In this
     * case it is a "correct" item, i.e. one with delimitcharacters but it doesn't have to be
     *
     * @covers ::is_complete_response
     */
    public function test_is_complete_response() {
        $questiontext = 'The cat [sat] and the cow [jumped]';
        $question = helper::make_question('wordselect', $questiontext);

        $response = ['p2' => 'on'];
        $this->assertTrue($question->is_complete_response($response));
        /* this time nothing is selected */
        $response = array();
        $this->assertFalse($question->is_complete_response($response));
    }
    /**
     * Is this place one that will be correct if seleced
     * @covers ::is_correct_place
     */
    public function test_is_correct_place() {
        $questiontext = 'The cat [sat] and the cow [jumped]';
        $question = helper::make_question('wordselect', $questiontext);
        $correctplaces = $question->get_correct_places($question->questiontext, "[]");
        $this->assertTrue($question->is_correct_place($correctplaces, 4));
    }
    /**
     * Has this word (or set of words) been selected
     * @covers ::is_word_selected
     */
    public function test_is_word_selected() {
        $questiontext = 'The cat [sat] and the cow [jumped]';
        $question = helper::make_question('wordselect', $questiontext);
        $response = array('p1' => 'on');
        $this->assertTrue($question->is_word_selected(1, $response));
        $response = array('1' => 'on');
        $this->assertFalse($question->is_word_selected(1, $response));
    }
    /**
     * Add one space to the pointy end of angle brackets.
     * This means that text within table fields can be set
     * as selectable
     *
     * @covers ::pad_angle_brackets
     */
    public function test_pad_angle_brackets() {
        $questiontext = '<p>The cat [<b>sat</b>]';
        $question = helper::make_question('wordselect', $questiontext);
        $paddedquestiontext = $question::pad_angle_brackets($questiontext);
        /* note the gap added between <p> and The */
        $this->assertEquals($paddedquestiontext, "<p> The cat [<b>sat</b>]", 'padding of html tags failed');
        /* tags retained are  sub sup i  u b */
        $this->assertStringContainsString("[<b>sat</b>]", $paddedquestiontext, 'formatting tags not retained ');
    }

    /**
     * Work out which text is selectable
     *
     * @covers ::set_is_selectable
     */
    public function test_set_is_selectable() {
        $questiontext = '<p>[<b>The</b>] [cat] sat [<b>sat</b>]';
        $question = helper::make_question('wordselect', $questiontext);
        $items = $question->get_words(true);
        // ...p tag is not selectable.
        $this->assertTrue(true, $items[0]->isselectable);
        /* test multi word mode */
        $questiontext = '<p>[[<b>The</b>]] cat [<b>sat</b>]';
        $question = helper::make_question('wordselect', $questiontext);
        $items = $question->get_words(true);
        $this->assertEquals('[[<b>The</b>]]', $items[1]->get_text());
        $this->assertTrue($items[1]->isselectable);
        $this->assertEquals($items[3]->get_text(), 'cat');
        $this->assertFalse($items[3]->isselectable);
        $this->assertEquals($items[5]->get_text(), '[<b>sat</b>]');
        $this->assertTrue($items[5]->isselectable);

        $questiontext = '<p>#The# ##cat## </p>';
        $question = helper::make_question('wordselect', $questiontext, ['delimitchars' => '##']);
        $items = $question->get_words(true);
        $this->assertTrue($items[1]->isselectable);
        $this->assertFalse($items[2]->isselectable);
        $this->assertTrue($items[3]->isselectable);

        $questiontext = '<p>@The@ @@cat@@</p>';
        $question = helper::make_question('wordselect', $questiontext, ['delimitchars' => '@@']);
        $items = $question->get_words(true);
        $this->assertTrue($items[1]->isselectable);
        $this->assertFalse($items[2]->isselectable);
        $this->assertTrue($items[3]->isselectable);
    }

    /**
     * How many selected items were not correct items
     *
     * @covers ::get_wrong_responsecount
     */
    public function test_get_wrong_responsecount() {
        $questiontext = 'The cat [sat] and the cow [jumped]';
        $question = helper::make_question('wordselect', $questiontext);
        $correctplaces = ['p1' => 'on', 'p2' => 'on'];
        $responses = ['p2' => 'on'];
        $wrongresponcecount = $question->get_wrong_responsecount($correctplaces, $responses);
        $this->assertEquals(1, $wrongresponcecount);
    }

    /**
     * Get array of all the words/groups of word that are correct (get a mark)
     * @covers ::get_correct_places
     */
    public function test_get_correct_places() {
        $question = helper::make_question('wordselect');
        /* counting from 0 the correct place is 2 (i.e. the word sat) */
        $correctplaces = ['0' => 4];
        $this->assertEquals($question->get_correct_places($question->questiontext, '[]'), $correctplaces);
    }

}
