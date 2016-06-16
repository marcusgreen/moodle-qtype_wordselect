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
 * wordselect question definition class.
 *
 * @package    qtype
 * @subpackage wordselect
 * @copyright  Marcus Green 2016)

 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();

//require_once('Kint/Kint.class.php');

/**
 * Represents a wordselect question.
 *
 * @copyright  2016 Marcus Green 

 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_wordselect_question extends question_graded_automatically_with_countback {

    public $markedselections = array();
    public $selectable = array();
    
     
    /* the characters indicating a field to fill i.e. [cat] creates
     * a field where the correct answer is cat
     */
    public $delimitchars = "[]";

    public function compare_response_with_answer(array $response, question_answer $answer) {
        
    }

    /**
     * @param int $key stem number
     * @return string the question-type variable name.
     */
    public function field($place) {
        return 'p' . $place;
    }

    public function is_gradable_response(array $response) {
        return true;
    }

    /**
     * The text with delimiters removed so the user cannot see
     * which words are the ones that should be selected. So The cow [jumped]
     * becomes The cow jumped
     */
    public function get_words() {
        $questiontextnodelim = $this->questiontext;
        $l = substr($this->delimitchars, 0, 1);
        $r = substr($this->delimitchars, 1, 1);
        $text = $this->get_questiontext_exploded($this->questiontext);
        $questiontextnodelim = preg_replace('/\\' . $l . '/', '', $text);
        $questiontextnodelim = preg_replace('/\\' . $r . '/', '', $questiontextnodelim);
        $this->selectable = strip_tags($questiontextnodelim);
        $allwords = preg_split('/[\s\n]/', $questiontextnodelim);
        return $allwords;
    }

    public static function get_questiontext_exploded($questiontext) {
        //put a space before and after tags so they get split as words
        //  $text = str_replace('<p>', ' <p> ', $this->questiontext);
        $text = str_replace('>', '> ', $questiontext);
        $text = str_replace('<', ' <', $text);
        return $text;
    }

    public static function get_correct_places($questiontext,$delimitchars) {
        $correctplaces=array();
        $text =qtype_wordselect_question::get_questiontext_exploded($questiontext);
        $allwords = preg_split('/[\s\n]/', $text);
        $l = substr($delimitchars, 0, 1);
        $r = substr($delimitchars, 1, 1);
        foreach ($allwords as $key => $word) {
            $regex = '/\\' . $l . '.*\\' . $r . '/';
            if (preg_match($regex, $word)) {
                $correctplaces[] = $key;
            }
        }
        return $correctplaces;
    }

    /**
     * Return an array of the question type variables that could be submitted
     * as part of a question of this type, with their types, so they can be
     * properly cleaned.
     * @return array variable name => PARAM_... constant.
     */
    public function get_expected_data() {
        $wordcount = sizeof($this->get_words());
        for ($key = 0; $key < $wordcount; $key++) {
            $data['p' . $key] = PARAM_RAW_TRIMMED;
        }
        return $data;
    }

    public function summarise_response(array $response) {
        
    }

    public function is_complete_response(array $response) {
        // TODO.
        return true;
    }

    public function get_validation_error(array $response) {
        // TODO.
        return '';
    }

    public function is_same_response(array $prevresponse, array $newresponse) {
        /* if you are moving from viewing one question to another this will
         * discard the processing if the answer has not changed. If you don't
         * use this method it will constantantly generate new question steps and
         * the question will be repeatedly set to incomplete. This is a comparison of
         * the equality of two arrays. Without this deferred feedback behaviour probably
         * wont work.
         */
        if ($prevresponse == $newresponse) {
            return true;
        } else {
            return false;
        }
    }

    public function get_correct_response() {
        $response = array();
        foreach ($this->get_correct_places($this->questiontext,$this->delimitchars) as $t) {
            $response['p' . $t] = 'on';
        }
        return $response;
    }

    public function check_file_access($qa, $options, $component, $filearea, $args, $forcedownload) {
        // TODO
    }

    /**
     * @param array $response responses, as returned by
     *      {@link question_attempt_step::get_qt_data()}.
     * @return array (number, integer) the fraction, and the state.
     */
    public function grade_response(array $response) {

        $right = 0;
        $allwords = $this->get_words();



        $responsewords = array();
        foreach ($response as $index => $value) {
            //TODO change 99 to length of string
            $responsewords[substr($index, 1, 99)] = $allwords[substr($index, 1, 99)];
        }



        $right = 0;
        $found = false;
        foreach ($responsewords as $key => $response) {
            foreach ($this->answers as $answer) {

                if ($answer->answer === $response) {
                    $found = true;
                }
            }

            if ($found == true) {
                $right++;
                $this->markedselections[$key]['word'] = $responsewords[$key];
                $markedselections[$key]['fraction'] = 1;
            } else {
                $right--;
                $this->markedselections[$key]['word'] = $responsewords[$key];
                $this->markedselections[$key]['fraction'] = 0;
            }
            $found = false;
        }
        if ($right < 0) {
            $right = 0;
        }
        $fraction = $right / sizeof($this->answers);
        $grade = array($fraction, question_state::graded_state_for_fraction($fraction));
        return $grade;
    }

    public function compute_final_grade($responses, $totaltries) {
        // TODO.
        return 0;
    }

}
