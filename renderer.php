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
 * wordselect question renderer class.
 *
 * @package    qtype
 * @subpackage wordselect
 * @copyright  Marcus Green 2016

 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();

/**
 * Generates the output for wordselect questions.
 *
 * @copyright  2016 Marcus Green

 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
require_once('Kint/Kint.class.php');

class qtype_wordselect_renderer extends qtype_with_combined_feedback_renderer {

    public function formulation_and_controls(question_attempt $qa, question_display_options $options) {
        global $PAGE;

        $question = $qa->get_question();
        $PAGE->requires->js('/question/type/wordselect/selection.js');
        $response = $qa->get_last_qt_data();
        $correctplaces = $question->get_correct_places($question->questiontext, $question->delimitchars);
        $output = $question->introduction;
        $inputattributes = array();
        foreach ($question->get_words() as $place => $value) {
            if (!array_key_exists('p' . $place, $response)) {
                $response['p' . $place] = '0';
            }
            $inputattributes=array('icon'=>'','title'=>'','tabindex'=>'');
            $inputattributes['name'] = $this->get_input_name($qa, $value, $place);
            $inputattributes['id'] = $this->get_input_id($qa, $value, $place);     
            $inputattributes['value'] = $value;
            $correctresponse = true;

            $hidden = "";
            /* if the current word/place exists in the response */
            $isselected = $question->is_word_selected($place, $response);
            if ($isselected) {
                $inputattributes['class'] = ' class=selected';
            } 
            if (!$options->readonly) {
                $hidden = html_writer::empty_tag('input', array(
                            'type' => 'hidden',
                            'name' => $inputattributes['name'],
                            'id' => $inputattributes['name'],
                            'value' => $response['p' . $place],
                ));
            }

            if ($isselected && $options->correctness == 1) {
                if ($this->is_correct_place($correctplaces, $place)) {
                    $inputattributes['icon'] = $this->feedback_image(1);
                    $inputattributes['title'] = ' title= "' . get_string('correctresponse', 'qtype_wordselect') . '"';
                    $inputattributes['class'] = ' class = correctresponse ';
                }
                if ($inputattributes['icon'] == "") {
                    $inputattributes['icon'] = $this->feedback_image(0);
                    $correctresponse = false;
                    $inputattributes['title'] = ' title="' . get_string('incorrectresponse', 'qtype_wordselect') . '"';
                }
            } else if ($this->is_correct_place($correctplaces, $place)) {
                if ($options->correctness == 1) {
                    if ($options->rightanswer == 1) {
                        /* $options->rightanswer is the setting for the quiz
                         * to show the non selected correct answers
                         * once the attempt is complete.
                         * if the word is a correct answer but not selected
                         * and the marking is complete (correctness==1)
                         */
                        $inputattributes['title'] = ' title="' . get_string('correctanswer', 'qtype_wordselect') . '"';
                        $inputattributes['value'] = '<span ' . $inputattributes['title'] . ' class="correct">[' . $inputattributes['value'] . ']</span>';
                    }
                }
            }
            /* when scrolling back and forth between questions
             * previously selected value into each place
             */
            if ($qa->get_last_qt_var($question->field($place)) == "1") {
                $inputattributes['class'] = " class = ' selected selectable'";
            } else {
                $inputattributes['class'] = ' class=selectable ';
            }
            /* When previewing after a quiz is complete */
            $inputattributes['readonly'] = '';
            if ($options->readonly) {
                $inputattributes['disabled'] = 'disabled';
                $inputattributes['readonly'] = " disabled='true' ";
                if ($correctresponse == false) {
                    $inputattributes['class'] = ' class = incorrect ';
                }
                if ($this->is_correct_place($correctplaces, $place)) {
                    $inputattributes['class'] = ' class = correctresponse ';
                }
            }
            /* skip empty places when tabbing */
            if ($inputattributes['value'] > "") {
                $inputattributes['tabindex'] = ' tabindex=99 ';
            }
            $regex = '/' . $inputattributes['value'] . '/';
            if (@preg_match($regex, $question->selectable)) {
                $output.=$hidden;
                $output .= '<span ' . $inputattributes['tabindex'] . ' name =' . $inputattributes['name'] . $inputattributes['class'] . $inputattributes['title'] . '>' . $inputattributes['value'] . '</span>' . $inputattributes['icon'];
                $output .= ' ';
            } else {
                /* for non selectable items such as the tags for tables etc */
                $output .= ' ' . $inputattributes['value'];
            }
        }

        return $output;
    }

    protected function get_input_name(question_attempt $qa, $value, $place) {
        /* prefix is the number of this question attempt */
        $qprefix = $qa->get_qt_field_name('');
        $inputname = $qprefix . 'p' . ($place);
        return $inputname;
    }

    protected function get_input_id(question_attempt $qa, $value, $place) {
        return $this->get_input_name($qa, $value, $place);
    }

    protected function get_input_value($value) {
        return 1;
    }

    /**
     * @param array $correctplaces
     * @param int $place
     * @return boolean
     * Check if the number represented by place occurs in the
     * array of correct places
     */
    protected function is_correct_place($correctplaces, $place) {
        if (in_array($place, $correctplaces)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * @param question_attempt $qa
     * @return string feedback for correct/partially correct/incorrect feedback
     */
    protected function specific_feedback(question_attempt $qa) {
        return $this->combined_feedback($qa);
    }

    protected function combined_feedback(question_attempt $qa) {
        $question = $qa->get_question();
        $state = $qa->get_state();
        if (!$state->is_finished()) {
            $response = $qa->get_last_qt_data();
            if (!$qa->get_question()->is_gradable_response($response)) {
                return '';
            }
            list($notused, $state) = $qa->get_question()->grade_response($response);
        }

        $feedback = '';
        $field = $state->get_feedback_class() . 'feedback';
        $format = $state->get_feedback_class() . 'feedbackformat';
        if ($question->$field) {
            $feedback .= $question->format_text($question->$field, $question->$format, $qa, 'question', $field, $question->id);
        }
        return $feedback;
    }

}
