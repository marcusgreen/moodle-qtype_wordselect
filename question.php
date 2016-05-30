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
 * @copyright  THEYEAR YOURNAME (YOURCONTACTINFO)

 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();
/* require_once($CFG->dirroot . '/question/type/questionbase.php'); */

require_once('Kint/Kint.class.php');

/**
 * Represents a wordselect question.
 *
 * @copyright  THEYEAR YOURNAME (YOURCONTACTINFO)

 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_wordselect_question extends question_graded_automatically_with_countback {


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

    public function get_all_words() {
        $questiontextnodelim = $this->questiontext;
        $questiontextnodelim = preg_replace('/\[/', '', $questiontextnodelim);
        $questiontextnodelim = preg_replace('/\]/', '', $questiontextnodelim);
        $allwords = preg_split('/[\s\n]/', $questiontextnodelim);
        return $allwords;
    }

    /**
     * Return an array of the question type variables that could be submitted
     * as part of a question of this type, with their types, so they can be
     * properly cleaned.
     * @return array variable name => PARAM_... constant.
     */
    public function get_expected_data() {
        $wordcount = sizeof($this->get_all_words());
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
        // TODO.
        return array();
    }

    public function check_file_access($qa, $options, $component, $filearea, $args, $forcedownload) {
        
    }

    /**
     * @param array $response responses, as returned by
     *      {@link question_attempt_step::get_qt_data()}.
     * @return array (number, integer) the fraction, and the state.
     */
    public function grade_response(array $response) {
        $right = 0;
        print "grade response";
        $allwords = $this->get_all_words();

        foreach ($response as $index => $value) {
            $responsewords[] = $allwords[substr($index, 1, 99)];
        }

        foreach ($responsewords as $response) {
            foreach ($this->answers as $answer) {
                if($answer->answer===$response){
                    $right++;
                    break;
                }else{
                    $right--;
                    break;
                }                
            }
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
