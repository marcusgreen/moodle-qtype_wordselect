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
 * Question type class for the wordselect question type.
 *
 * @package    qtype
 * @subpackage wordselect
 * @copyright  THEYEAR YOURNAME (YOURCONTACTINFO)

 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/questionlib.php');
require_once($CFG->dirroot . '/question/engine/lib.php');
require_once($CFG->dirroot . '/question/type/wordselect/question.php');


/**
 * The wordselect question type.
 *
 * @copyright  THEYEAR YOURNAME (YOURCONTACTINFO)

 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_wordselect extends question_type {
    
      /* data used by export_to_xml (among other things possibly */
    public function extra_question_fields() {
      return array('question_wordselect', 'introduction','delimitchars');
    }
        /**
     * Utility method used by {@link qtype_renderer::head_code()}
     * It looks for any of the files script.js or script.php that
     * exist in the plugin folder and ensures they get included.
     * It also includes the jquery files required for this plugin
     */
    public function find_standard_scripts() {
        global $CFG, $PAGE;
        parent::find_standard_scripts();
        $PAGE->requires->jquery();
        $PAGE->requires->jquery_plugin('ui');
        // Include "script.js" and/or "script.php" in the normal way.
    }

    public function move_files($questionid, $oldcontextid, $newcontextid) {
        parent::move_files($questionid, $oldcontextid, $newcontextid);
        $this->move_files_in_hints($questionid, $oldcontextid, $newcontextid);
    }

    protected function delete_files($questionid, $contextid) {
        parent::delete_files($questionid, $contextid);
        $this->delete_files_in_hints($questionid, $contextid);
    }

    public function save_question_options($question) {
        global $DB;
        $context = $question->context;
        $options = $DB->get_record('question_wordselect', array('questionid' => $question->id));
        $this->update_question_wordselect($question, $options, $context);
        $this->save_hints($question);
    }
    
     /* runs from question editing form */

    public function update_question_wordselect($question,$options,$context) {
        global $DB;
        $options = $DB->get_record('question_wordselect', array('questionid' => $question->id));
        if (!$options) {
            $options = new stdClass();
            $options->questionid = $question->id;
            $options->introduction = '';
            $options->delimitchars = '';
            $options->correctfeedback = '';
            $options->partiallycorrectfeedback = '';
            $options->incorrectfeedback = '';
            $options->id = $DB->insert_record('question_wordselect', $options);
        }
        $options->introduction =$question->introduction['text'];
        $options->delimitchars = $question->delimitchars;
        $options = $this->save_combined_feedback_helper($options, $question, $context, true);
        $DB->update_record('question_wordselect', $options);
    }


    protected function initialise_question_instance(question_definition $question, $questiondata) {
        // TODO.
        parent::initialise_question_instance($question, $questiondata);
    }

    public function get_random_guess_score($questiondata) {
        // TODO.
        return 0;
    }

    public function get_possible_responses($questiondata) {
        // TODO.
        return array();
    }
}
