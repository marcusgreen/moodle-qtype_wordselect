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
require_once('Kint/Kint.class.php');

/**
 * The wordselect question type.
 *
 * @copyright  THEYEAR YOURNAME (YOURCONTACTINFO)

 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_wordselect extends question_type {
    /* data used by export_to_xml (among other things possibly */

    public function extra_question_fields() {
        return array('question_wordselect', 'introduction', 'delimitchars');
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
        /* Save the extra data to your database tables from the
          $question object, which has all the post data from editquestion.html
          although it is called question it seems to actually be the form
         * */

        $gaps = $this->get_gaps($question->delimitchars, $question->questiontext);
        
         /* answerwords are words that need to be selected to get a mark */
        $answerwords = $this->get_answer_fields($gaps, $question);    
       
        global $DB;

        $context = $question->context;
        // Fetch old answer ids so that we can reuse them.
        $this->update_question_answers($question, $answerwords);

        $options = $DB->get_record('question_wordselect', array('questionid' => $question->id));
        $this->update_question_wordselect($question, $options, $context);
        $this->save_hints($question, true);
        return true;
    }

    /* it really does need to be static */

    public static function get_gaps($delimitchars, $questiontext) {
        /* l for left delimiter r for right delimiter
         * defaults to []
         * e.g. l=[ and r=] where question is
         * The [cat] sat on the [mat]
         */
        $delim = self::get_delimit_array($delimitchars);
        $fieldregex = '/.*?\\' . $delim["l"] . '(.*?)\\' . $delim["r"] . '/';
        $matches = array();
        preg_match_all($fieldregex, $questiontext, $matches);
        return $matches[1];
    }

    /* chop the delimit string into a two element array
     * this might be better done on initialisation
     */

    public static function get_delimit_array($delimitchars) {
        $delimitarray = array();
        $delimitarray["l"] = substr($delimitchars, 0, 1);
        $delimitarray["r"] = substr($delimitchars, 1, 1);
        return $delimitarray;
    }

    
    /**
     * Set up all the answer fields with respective fraction (mark values)
     * This is used to update the question_answers table. Answerwords has
     * been pulled from within the delimitchars e.g. the cat within [cat]
     * Wronganswers (distractors) has been pulled from a comma delimited edit
     * form field
     * wro
     * @param array $answerwords
     * @param type $question
     * @return type array
     */
    public function get_answer_fields(array $answerwords, $question) {
        /* this code runs both on saving from a form and from importing and needs
         * improving as it mixes pulling information from the question object which
         * comes from the import and from $question->wronganswers field which
         * comes from the question_editing form.
         */
        $answerfields = array();
        /* this next block runs when importing from xml */
        if (property_exists($question, 'answer')) {
            foreach ($question->answer as $key => $value) {
                if ($question->fraction[$key] == 0) {
                    $answerfields[$key]['value'] = $question->answer[$key];
                    $answerfields[$key]['fraction'] = 0;
                } else {
                    $answerfields[$key]['value'] = $question->answer[$key];
                    $answerfields[$key]['fraction'] = 1;
                }
            }
        }
         /* the rest of this function runs when saving from edit form */
        if (!property_exists($question, 'answer')) {
            foreach ($answerwords as $key => $value) {
                $answerfields[$key]['value'] = $value;
                $answerfields[$key]['fraction'] = 1;
            }
        }
        return $answerfields;
    }
    
  

    public function update_question_answers($question, array $answerfields) {
        global $DB;
        $oldanswers = $DB->get_records('question_answers', array('question' => $question->id), 'id ASC');
        // Insert all the new answers.
        foreach ($answerfields as $field) {
            // Save the true answer - update an existing answer if possible.
            if ($answer = array_shift($oldanswers)) {
                $answer->question = $question->id;
                $answer->answer = $field['value'];
                $answer->feedback = '';
                $answer->fraction = 1;
                $DB->update_record('question_answers', $answer);
            } else {
                // Insert a blank record.
                $answer = new stdClass();
                $answer->question = $question->id;
                $answer->answer = $field['value'];
                $answer->feedback = '';
                $answer->correctfeedback = '';
                $answer->partiallycorrectfeedback = '';
                $answer->incorrectfeedback = '';
                $answer->fraction = 1;
                $answer->id = $DB->insert_record('question_answers', $answer);
            }
        }
        // Delete old answer records.
        foreach ($oldanswers as $oa) {
            $DB->delete_records('question_answers', array('id' => $oa->id));
        }
    }

    /* runs from question editing form */

    public function update_question_wordselect($question, $options, $context) {
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
        $options->introduction = $question->introduction['text'];
        $options->delimitchars = $question->delimitchars;
        $options = $this->save_combined_feedback_helper($options, $question, $context, true);
        $DB->update_record('question_wordselect', $options);
    }

    protected function initialise_question_instance(question_definition $question, $questiondata) {
        // TODO.
        parent::initialise_question_instance($question, $questiondata);
        /* not in template */
        $this->initialise_question_answers($question, $questiondata);
        
    }
    protected function initialise_question_answers(question_definition $question, $questiondata, $forceplaintextanswers = true) {
       
    //    $question->allwords = array();
        if (empty($questiondata->options->answers)) {
            return;
        }
        foreach ($questiondata->options->answers as $a) {
            if (strstr($a->fraction, '1') == false) {
                /* if this is a wronganswer/distractor strip any
                 * backslahses, this allows escaped backslashes to
                 * be used i.e. \, and not displayed in the draggable
                 * area
                 */
                $a->answer = stripslashes($a->answer);
            }
           // array_push($question->allwords, $a->answer);
         
            /* answer in this context means correct answers, i.e. where
             * fraction contains a 1 */
            if (strpos($a->fraction, '1') !== false) {
                $question->answers[$a->id] = new question_answer($a->id, $a->answer, $a->fraction, $a->feedback, $a->feedbackformat);
                if (!$forceplaintextanswers) {
                    $question->answers[$a->id]->answerformat = $a->answerformat;
                }
            }
        }
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
