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
        $output = $question->introduction;
        $unselectable = $question->get_unselectable_words($question->questiontext);
        foreach ($question->get_words() as $place => $value) {
            $correctresponse = true;
            $qprefix = $qa->get_qt_field_name('');
            $inputname = $qprefix . 'p' . ($place);
            $checked = null;
            $icon = "";
            $class = '';
            $title = '';
            if (!array_key_exists($place, $unselectable)) {
                $class = ' class=selectable ';
            } else {
                $class = '';
            }
            /* if the current word/place exists in the response */
            if (array_key_exists('p' . ($place), $response) && $options->correctness == 1) {
                $checked = 'checked=true';
                $class = ' class=selected';
                if ($this->is_correct_place($correctplaces, $place)) {
                    $icon = $this->feedback_image(1);
                    $title = ' title= "'.get_string('correctresponse', 'qtype_wordselect').'"';
                    $class = ' class = correctresponse ';
                }
                if ($icon == "") {
                    $icon = $this->feedback_image(0);
                    $correctresponse = false;
                    $title = ' title="' .get_string('incorrectresponse', 'qtype_wordselect').'"';
                }
            } else if ($this->is_correct_place($correctplaces, $place)) {
                if ($options->correctness == 1) {
                    if ($options->rightanswer == 1) {
                        /* $options->rightanswer is the setting for the quiz
                         * to show the non selected corrected correct answers
                         * once the attempt is complete.
                         * if the word is a correct answer but not selected
                         * and the marking is complete (correctness==1)
                         */
                        $title = ' title="'.get_string('correctanswer', 'qtype_wordselect').'"';
                        $value = '<span ' . $title . ' class="correct">[' . $value . ']</span>';
                    }
                }
            }

            $readonly = "";
            /* When previewing after a quiz is complete */
            if ($options->readonly) {
                $readonly = " disabled='true' ";
                if ($correctresponse == false) {
                    $class = ' class = incorrect ';
                }
            }
            /* Allows tabbing from word to word */
            $tabindex = "";
            if ($value > "") {
                $tabindex = ' tabindex=99 ';
            }
            $regex = '/' . $value . '/';
            if (@preg_match($regex, $question->selectable)) {
                $output .= '<input class = "checkboxes" hidden = true ' . $checked . ' type = "checkbox" name = '
                        . $inputname . $readonly . ' id=' . $inputname . '></input>';
                $output .= '<span '.$tabindex.' name =' . $inputname . $class . $title . '>' . $value . '</span>' . $icon;
                $output .= ' ';
            } else {
                $output .= ' ' . $value;
            }
        }
        return $output;
    }

    /**
     * @param array $correctplaces
     * @param int $place
     * @return boolean
     * Check if the number represented by place occurs in the
     * array of correct places
     */
    protected function is_correct_place($correctplaces, $place) {
        foreach ($correctplaces as $key => $correctplace) {
            if ($place == $correctplace) {
                return true;
            }
        }
        return false;
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
