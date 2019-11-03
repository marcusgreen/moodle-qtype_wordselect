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
 * @package    qtype_wordselect
 * @copyright  2018 Marcus Green
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/questionlib.php');
require_once($CFG->dirroot . '/question/engine/lib.php');
require_once($CFG->dirroot . '/question/type/wordselect/question.php');

/**
 * The wordselect question type.
 *
 * @copyright  2018 Marcus Green
 *
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_wordselect extends question_type {
    /**
     * data used by export_to_xml (among other things possibly
     * @return array
     */
    public function extra_question_fields() {
        return array('question_wordselect', 'introduction', 'delimitchars', 'wordpenalty');
    }

    /**
     * Move all the files belonging to this question from one context to another.
     * @param int $questionid the question being moved.
     * @param int $oldcontextid the context it is moving from.
     * @param int $newcontextid the context it is moving to.
     *
     */
    public function move_files($questionid, $oldcontextid, $newcontextid) {
        parent::move_files($questionid, $oldcontextid, $newcontextid);
        $fs = get_file_storage();
        $fs->move_area_files_to_new_context($oldcontextid,
                $newcontextid, 'qtype_wordselect', 'introduction', $questionid);
        $this->move_files_in_hints($questionid, $oldcontextid, $newcontextid);
    }

    /**
     * Delete all the files belonging to this question.Seems the same as in the parent
     * @param int $questionid the question being deleted.
     * @param int $contextid the context the question is in.
     */
    protected function delete_files($questionid, $contextid) {
        parent::delete_files($questionid, $contextid);
        $this->delete_files_in_hints($questionid, $contextid);
    }

    /**
     * Save the extra data to your database tables from the
     * $formdata/$data object, which has all the post data from editformdata.html
     * Save the units and the answers associated with this question.
     * @param object $formdata
     * @return boolean
     */
    public function save_question_options($formdata) {
        global $DB;
        $answerwords = $this->get_answerwords($formdata->delimitchars, $formdata->questiontext);
        $context = $formdata->context;
        // Fetch old answer ids so that we can reuse them.
        $this->update_question_answers($formdata, $answerwords);
        $options = $DB->get_record('question_wordselect', array('questionid' => $formdata->id));
        $this->update_question_wordselect($formdata, $options, $context);
        $this->save_hints($formdata, true);
        return true;
    }

    /**
     * It really does need to be static
     * l for left delimiter r for right delimiter
     * defaults to []
     * e.g. l=[ and r=] where question is
     * The [cat] sat on the [mat]
     *
     * @param string $delimitchars
     * @param string $questiontext
     * @return array
     */
    public static function get_answerwords($delimitchars, $questiontext) {
        $delim = self::get_delimit_array($delimitchars);
        $fieldregex = '/.*?\\' . $delim["l"] . '(.*?)\\' . $delim["r"] . '/';
        $matches = array();
        preg_match_all($fieldregex, $questiontext, $matches);
        return $matches[1];
    }

    /**
     * chop the delimit string into a two element array
     * this might be better done on initialisation
     *
     * @param string $delimitchars
     * @return string
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
     * @param array $answerwords
     * @param object $question
     * @return array
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
    /**
     * Used when creating/editing a question
     *
     * @param object $question
     * @param array $answerwords
     */
    public function update_question_answers($question, array $answerwords) {
        global $DB;
        $oldanswers = $DB->get_records('question_answers', array('question' => $question->id), 'id ASC');
        // Insert all the new answers.
        foreach ($answerwords as $word) {
            // Save the true answer - update an existing answer if possible.
            if ($answer = array_shift($oldanswers)) {
                $answer->question = $question->id;
                $answer->answer = $word;
                $answer->feedback = '';
                $answer->fraction = 1;
                $DB->update_record('question_answers', $answer);
            } else {
                // Insert a blank record.
                $answer = new stdClass();
                $answer->question = $question->id;
                $answer->answer = $word;
                $answer->feedback = '';
                $answer->fraction = 1;
                $answer->id = $DB->insert_record('question_answers', $answer);
            }
        }
        // Delete old answer records.
        foreach ($oldanswers as $oa) {
            $DB->delete_records('question_answers', array('id' => $oa->id));
        }
    }

    /**
     * Used in the question editing form
     *
     * @param object $formdata
     * @param object $options
     * @param object $context
     */
    public function update_question_wordselect($formdata, $options, $context) {
        global $DB;
        $options = $DB->get_record('question_wordselect', array('questionid' => $formdata->id));
        if (!$options) {
            $options = new stdClass();
            $options->questionid = $formdata->id;
            $options->introduction = '';
            $options->delimitchars = '';
            $options->wordpenalty = '';
            $options->correctfeedback = '';
            $options->partiallycorrectfeedback = '';
            $options->incorrectfeedback = '';
            $options->incorrectfeedback = '';
            $options->id = $DB->insert_record('question_wordselect', $options);
        }
         /* when coming in from form */
        if (is_array($formdata->introduction)) {
              $options->introduction = $this->import_or_save_files($formdata->introduction,
                $context, 'qtype_wordselect', 'introduction', $formdata->id);
        } else {
            /* when being imported e.g. from an xml import */
            $options->introduction = $formdata->introduction;
            $options->wordpenalty = $formdata->wordpenalty;
        }
        $options->delimitchars = $formdata->delimitchars;
        $options->wordpenalty = $formdata->wordpenalty;

        $options->correctfeedback = "";
        $options = $this->save_combined_feedback_helper($options, $formdata, $context, false);
        $DB->update_record('question_wordselect', $options);
    }

    /**
     * Populates fields such as combined feedback
     *
     * Misleadingly named as it starts with get
     * but doesn't return a value
     * @param object $question
     */
    public function get_question_options($question) {
        global $DB;
        $question->options = $DB->get_record('question_wordselect',
               array('questionid' => $question->id), '*', MUST_EXIST);
        parent::get_question_options($question);
    }

    /**
     * When in a quiz/previewing     *
     *
     * @param question_definition $question
     * @param object $questiondata
     */
    protected function initialise_question_instance(question_definition $question, $questiondata) {
        parent::initialise_question_instance($question, $questiondata);
        $this->initialise_question_answers($question, $questiondata);
        parent::initialise_combined_feedback($question, $questiondata);
    }

    /**
     * Called from within questiontypebase
     *
     * @param  string $hint
     * @return question_hint_with_parts
     */
    protected function make_hint($hint) {
        return question_hint_with_parts::load_from_record($hint);
    }

    /**
     * Initialise question answers
     *
     * @param question_definition $question
     * @param object $questiondata
     * @param boolean $forceplaintextanswers
     * @return boolean
     */
    protected function initialise_question_answers(question_definition $question, $questiondata, $forceplaintextanswers = true) {
        if (empty($questiondata->options->answers)) {
            return false;
        }
        $placecounter = 0;
        foreach ($questiondata->options->answers as $a) {
            $question->places[$placecounter++] = "";
            if (strstr($a->fraction, '1') == false) {
                /* if this is a wronganswer/distractor strip any
                 * backslahses, this allows escaped backslashes to
                 * be used i.e. \, and not displayed in the draggable
                 * area
                 */
                $a->answer = stripslashes($a->answer);
            }
            /* answer in this context means correct answers, i.e. where
             * fraction contains a 1 */
            if (strpos($a->fraction, '1') !== false) {
                $question->answers[$a->id] = new question_answer($a->id, $a->answer, $a->fraction,
                        $a->feedback, $a->feedbackformat);
                if (!$forceplaintextanswers) {
                    $question->answers[$a->id]->answerformat = $a->answerformat;
                }
            }
        }
    }

    /**
     * Import from xml, probably the most useful import formta
     *
     * @param array $data
     * @param object $question
     * @param qformat_xml $format
     * @param object $extra
     * @return boolean
     */
    public function import_from_xml($data, $question, qformat_xml $format, $extra = null) {
        if (!isset($data['@']['type']) || $data['@']['type'] != 'wordselect') {
            return false;
        }
        $question = parent::import_from_xml($data, $question, $format, null);
        $format->import_combined_feedback($question, $data, false);
        $format->import_hints($question, $data, true, false, $format->get_format($question->questiontextformat));
        return $question;
    }

    /**
     * Export to xml format
     *
     * @param object $question
     * @param qformat_xml $format
     * @param object $extra
     * @return string
     */
    public function export_to_xml($question, qformat_xml $format, $extra = null) {
        global $CFG;
        $pluginmanager = core_plugin_manager::instance();
        $wordselectinfo = $pluginmanager->get_plugin_info('qtype_wordselect');
        $output = parent::export_to_xml($question, $format);
        $output .= '    <delimitchars>' . $question->options->delimitchars .
                "</delimitchars>\n";
         $output .= '    <wordpenalty>' . $question->options->wordpenalty .
                "</wordpenalty>\n";
        $output .= '    <!-- Wordselect release:'
                . $wordselectinfo->release . ' version:' . $wordselectinfo->versiondisk . ' Moodle version:'
                . $CFG->version . ' release:' . $CFG->release
                . " -->\n";
        $output .= $format->write_combined_feedback($question->options, $question->id, $question->contextid);
        return $output;
    }

    /**
     * TODO Not sure what this does
     * @param object $questiondata
     * @return int
     */
    public function get_random_guess_score($questiondata) {
        // TODO.
        return 0;
    }

    /**
     * TODO Not sure what this does
     * @param objet $questiondata
     * @return array
     */
    public function get_possible_responses($questiondata) {
        // TODO.
        return array();
    }

    /**
     * Save the question (TODO investigate what this does)
     * @param array $question The current question
     * @param array $form The question editing form data
     * @return object qtype_wordselect
     */
    public function save_question($question, $form) {
        $ws = new qtype_wordselect_question();
        $ws->init($form->questiontext['text'], $form->delimitchars);
        $correctplaces = $ws->get_correct_places($form->questiontext['text'], $form->delimitchars);
        $form->defaultmark = count($correctplaces);
        return parent::save_question($question, $form);
    }
}
