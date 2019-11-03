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
 * These tests walks a question through the interactive behaviour
 *
 * @package    qtype_wordselect
 * @copyright  2012 Marcus Green
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();

global $CFG;

require_once($CFG->dirroot . '/question/type/wordselect/tests/helper.php');

/**
 * Unit tests for the wordselect question type.
 *
 * @copyright  2018 Marcus Green
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_wordselect_walkthrough_test extends qbehaviour_walkthrough_test_base {

    public function test_interactive_with_correct() {
        // Create a wordselect question.
        $question = qtype_wordselect_test_helper::make_question('wordselect');
        $maxmark = 1;
        $this->start_attempt_at_question($question, 'interactive', $maxmark);

        // Check the initial state.
        $this->check_current_state(question_state::$todo);
        $this->check_current_mark(null);

        $this->check_step_count(1);

        $this->check_current_output(
                $this->get_contains_marked_out_of_summary(),
                $this->get_does_not_contain_try_again_button_expectation(),
                $this->get_does_not_contain_feedback_expectation(),
                $this->get_does_not_contain_validation_error_expectation(),
                $this->get_does_not_contain_try_again_button_expectation(),
                $this->get_no_hint_visible_expectation());

        // Save a  correct response.
        // Default quesiton text is The cat [sat].
        $this->process_submission(array('p4' => 'on'));
        $this->check_step_count(2);

        $this->check_current_state(question_state::$todo);

        $this->check_current_output(
                $this->get_contains_marked_out_of_summary(),
                $this->get_does_not_contain_try_again_button_expectation(),
                $this->get_does_not_contain_feedback_expectation(),
                $this->get_does_not_contain_validation_error_expectation(),
                $this->get_does_not_contain_try_again_button_expectation(),
                $this->get_no_hint_visible_expectation());

        // Submit saved response.
        $this->process_submission(array('-submit' => 1, 'p4' => 'on'));
        $this->check_step_count(3);
        // Verify.
        $this->check_current_state(question_state::$gradedright);

        $this->check_current_output(
                $this->get_does_not_contain_try_again_button_expectation(),
                $this->get_does_not_contain_validation_error_expectation(),
                $this->get_does_not_contain_try_again_button_expectation(),
                $this->get_no_hint_visible_expectation());

        $this->check_current_mark(1);
        // Finish the attempt.
        $this->quba->finish_all_questions();
                $this->check_current_state(question_state::$gradedright);
    }
}
