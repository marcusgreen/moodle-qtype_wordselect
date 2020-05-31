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
 * @package    qtype_wordselect
 *
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
    /**
     * Generate the area that contains the question text, and the controls for students to
     * input their answers.
     *      *
     * @param question_attempt $qa the question attempt to display.
     * @param question_display_options $options controls what should and should not be displayed.
     * @return string HTML fragment.
     */
    public function formulation_and_controls(question_attempt $qa, question_display_options $options) {
        global $PAGE;

        $output = '';

        $question = $qa->get_question();
        $this->page->requires->js_call_amd('qtype_wordselect/navigation', 'init');
        $response = $qa->get_last_qt_data();
        $correctplaces = $question->get_correct_places($question->questiontext, $question->delimitchars);
        $output .= html_writer::start_div('introduction');
        /* this will ensure filters are applied to the introduction, done particularly for the multilang filter */
        $output .= $question->format_text($question->introduction, $question->questiontextformat, $qa, 'qtype_wordselect',
                'introduction', $question->id);
        $output .= html_writer::end_div();
        $output .= html_writer::start_div('qtext');

        /*initialised */
        $question->init($question->questiontext, $question->delimitchars);
        $items = $question->get_words();

        foreach ($items as $place => $item) {
            $word = $item->get_without_delim();
            $correctnoselect = false;
            $wordattributes = array("role" => "checkbox");
            $afterwordfeedback = '';
            $wordattributes['name'] = $this->get_input_name($qa, $place);
            $wordattributes['id'] = $this->get_input_id($qa, $place);
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
                    $wordattributes['class'] = 'readonly correctresponse';
                }

                if (!($iscorrectplace)) {
                    if ($isselected == true) {
                        $wordattributes['class'] = 'readonly incorrect ';
                    } else if (($question->multiword == true)) {
                        $wordattributes['class'] = 'readonly multiword ';
                    }
                }
            } else {
                $qasdata = $qa->get_last_qt_var($question->field($place));
                /* when scrolling back and forth between questions
                 * previously selected value into each place. This
                 * is retrieved from the question_attempt_step_data
                 * table
                 */
                if (($qasdata == "on") || ($qasdata == "true")) {
                    $wordattributes['class'] = 'selected selectable';
                    $wordattributes['aria-checked'] = 'true';
                } else {
                    $class = 'selectable';
                    if ($question->multiword == true) {
                        $class .= ' multiword';
                    }
                    $wordattributes['class'] = $class;
                    $wordattributes['aria-checked'] = 'false';
                }
                $properties = array(
                    'type' => 'checkbox',
                    'name' => $wordattributes['name'],
                    'id' => $wordattributes['name'],
                    'hidden' => 'true',
                    'class' => 'selcheck');
                if ($isselected == true) {
                    $properties['checked'] = "true";
                    $wordattributes['aria-checked'] = 'true';
                }
                $checkbox = html_writer::empty_tag('input', $properties);
            }

            if ($item->isselectable == true) {
                if ($correctnoselect == true) {
                    $word = "[" . $word . "]";
                }
                $output .= $checkbox;
                $output .= html_writer::tag('span', $word, $wordattributes);
                $output .= $afterwordfeedback;
            } else {
                // For non selectable items such as the tags for tables etc.
                $output .= $word;
            }
        }
        /* this ensures that any files inserted through the editor menu will display */
        $output = $question->format_text(
          $output, $question->questiontextformat, $qa, 'question', 'questiontext', $question->id);

        $output .= html_writer::end_div();
        if ($qa->get_state() == question_state::$invalid) {
            $output .= html_writer::div($question->get_validation_error($response), 'validationerror');
        }
        $PAGE->requires->js_call_amd('qtype_wordselect/selection', 'init',
        [$qa->get_outer_question_div_unique_id()]);

        return $output;
    }

    /**
     * Creates the name of the field/checkbox
     * that identifies the selectable item
     *
     * @param question_attempt $qa
     * @param int $place
     * @return string
     */
    protected function get_input_name(question_attempt $qa, $place) {
        /* prefix is the number of this question attempt */
        $qprefix = $qa->get_qt_field_name('');
        $inputname = $qprefix . 'p' . ($place);
        return $inputname;
    }
    /**
     * TODO document the difference to get_input_name
     * @param question_attempt $qa
     * @param int $place
     * @return string
     */
    protected function get_input_id(question_attempt $qa, $place) {
        return $this->get_input_name($qa, $place);
    }

    /**
     * TODO This seems to call a function in the same file with the same
     * parameters implying it could be eliminated
     * @param question_attempt $qa
     * @return string
     */
    protected function specific_feedback(question_attempt $qa) {
        return $this->combined_feedback($qa);
    }

    /**
     * Get feedback for correct/partially correct/incorrect
     * @param question_attempt $qa
     * @return string
     */
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

    /**
     * overriding base class method purely to return a string
     * yougotnrightcount instead of default yougotnright
     *
     * @param question_attempt $qa
     * @return string
     */
    protected function num_parts_correct(question_attempt $qa) {
        $a = new stdClass();
        list($a->num, $a->outof) = $qa->get_question()->get_num_parts_right(
                $qa->get_last_qt_data());
        if (is_null($a->outof)) {
            return '';
        } else {
            return get_string('yougotnrightcount', 'qtype_wordselect', $a);
        }
    }

}