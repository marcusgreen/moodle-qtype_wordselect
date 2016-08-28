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
class qtype_wordselect_renderer extends qtype_with_combined_feedback_renderer {

    public function formulation_and_controls(question_attempt $qa, question_display_options $options) {
        global $PAGE;
        $question = $qa->get_question();
        $PAGE->requires->js('/question/type/wordselect/selection.js');
        $response = $qa->get_last_qt_data();
        $correctplaces = $question->get_correct_places($question->questiontext, $question->delimitchars);
        /* this will ensure filters are applied to the introduction, done particularly for the multilang filter */
        $output = $question->format_text($question->introduction, $question->questiontextformat,
            $qa, 'qtype_wordselect', 'introduction', $question->id);
        foreach ($question->get_words() as $place => $word) {
            $correctnoselect = false;
            $wordattributes = array("role" => "checkbox");
            $afterwordfeedback = '';
            $wordattributes['name'] = $this->get_input_name($qa, $word, $place);
            $wordattributes['id'] = $this->get_input_id($qa, $word, $place);
            $correctresponse = true;
            $iscorrectplace = $question->is_correct_place($correctplaces, $place);
            $checkbox = "";
            /* if the current word/place exists in the response */
            $isselected = $question->is_word_selected($place, $response);
            if ($isselected) {
                $wordattributes['class'] = 'selected';
            }
            if ($isselected && $options->correctness == 1) {
                if ($iscorrectplace) {
                    $afterwordfeedback = $this->feedback_image(1);
                    $wordattributes['title'] = get_string('correctresponse', 'qtype_wordselect');
                    $wordattributes['class'] = 'correctresponse';
                } else {
                    $afterwordfeedback = $this->feedback_image(0);
                    $wordattributes['title'] = ' ' . get_string('incorrectresponse', 'qtype_wordselect');
                }
            } else if ($iscorrectplace) {
                if ($options->correctness == 1) {
                    if ($options->rightanswer == 1) {
                        /* $options->rightanswer is the setting for the quiz
                         * to show the non selected correct answers
                         * once the attempt is complete.
                         * if the word is a correct answer but not selected
                         * and the marking is complete (correctness==1)
                         */
                        $wordattributes['title'] = get_string('correctanswer', 'qtype_wordselect');
                        $wordattributes['class'] = 'correct';
                        $correctnoselect = true;
                    }
                }
            }
            /* skip empty places when tabbing */
            if ($word > "") {
                $wordattributes['tabindex'] = '1';
            }
            if ($options->readonly) {
                $wordattributes['tabindex'] = '';
                if ($iscorrectplace && ($isselected == true)) {
                    $wordattributes['class'] = 'correctresponse';
                }
                if ((!($iscorrectplace)) && ($isselected == true)) {
                    $wordattributes['class'] = 'incorrect ';
                }
            } else {
                $qasdata = $qa->get_last_qt_var($question->field($place));
                /* when scrolling back and forth between questions
                 * previously selected value into each place. This
                 * is retrieved from the question_attempt_step_data
                 * table
                 */
                if ($qasdata == "on") {
                    $wordattributes['class'] = 'selected selectable';
                    $wordattributes['aria-checked'] = 'true';
                } else {
                    $wordattributes['class'] = 'selectable';
                    $wordattributes['aria-checked'] = 'false';
                }
                $properties = array(
                    'type' => 'checkbox',
                    'name' => $wordattributes['name'],
                    'id' => $wordattributes['name'],
                    'hidden' => 'true');
                if ($isselected == true) {
                    $properties['checked'] = "true";
                    $wordattributes['aria-checked'] = 'true';
                }
                $checkbox = html_writer::empty_tag('input', $properties);
            }
            /* the @ supresses error messages if selectable is empty */
            if (@strpos($question->selectable, $word) !== false) {
                if ($correctnoselect == true) {
                    $word = "[" . $word . "]";
                }
                $output .= $checkbox;
                $output .= html_writer::tag('span', $word, $wordattributes);
                $output .= $afterwordfeedback;
                $output .= ' ';
            } else {
                /* for non selectable items such as the tags for tables etc */
                $output .= ' ' . $word;
            }
        }
        /* this ensures that any files inserted through the editor menu will display */
        $output = $question->format_text($output, $question->questiontextformat, $qa, 'question',
                    'questiontext', $question->id);
        return $output;
    }

    protected function get_input_name(question_attempt $qa, $word, $place) {
        /* prefix is the number of this question attempt */
        $qprefix = $qa->get_qt_field_name('');
        $inputname = $qprefix . 'p' . ($place);
        return $inputname;
    }

    protected function get_input_id(question_attempt $qa, $word, $place) {
        return $this->get_input_name($qa, $word, $place);
    }

    /**
     * @param question_attempt $qa
     * @return string feedback for correct/partially correct/incorrect feedback
     */
    protected function specific_feedback(question_attempt $qa) {
        return $this->combined_feedback($qa);
    }

    /* correct,partially correct and incorrect */

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
