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
 * @package    qtype_wordselect
 * @copyright  Marcus Green 2018

 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();

/**
 * Represents a wordselect question.
 *
 * @copyright  2018 Marcus Green

 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_wordselect_question extends question_graded_automatically_with_countback {

    /**
     *
     * @var number how many items clicked on are not correct answers
     */
    public $wrongresponsecount = '0.0';

    /**
     *
     * @var number how many items clicked on are  correct answers
     */
    public $rightresponsecount;

    /**
     * Do all selectable items need to be marked with delimiters
     * @var boolean
     */
    public $multiword = false;

    /**
     * is this item selectable. Only makes
     * sense if multiword is true
     * @var boolean
     */
    public $isselectable;

    /**
     * fraction to deduct for each incorrectly selected text item
     * Wrong response is multiplied by this, i.e. 2 wrong responses
     * and wordpenalty of .5 means 1 penalty, default is 1, i.e. no
     * change 1*1=1
     * @var number
     */
    public $wordpenalty = 1.0;

    /**
     * TODO
     * I am not sure this is necessary
     * @var string
     */
    public $questiontextsplit;

    /**
     * the place number with p appended, i.e. p0 p1 etc
     * @param number $place
     * @return string the question-type variable name
     */
    public function field($place) {
        return 'p' . $place;
    }

    /**
     * Initialise the question. This ought really to be done via the constructor
     *
     * @param string $questiontext
     * @param string $delimitchars short array that gest split in to the 2 dlimiters
     */
    public function init($questiontext, $delimitchars) {
        $this->questiontext = $questiontext;
        $this->delimitchars = $delimitchars;
        $l = substr($this->delimitchars, 0, 1);
        // If there are two opening left delimiters, e.g. [[cat]] multiword is true
        // Will assume that closing delimters are also doubled.
        if (strpos($questiontext, $l . $l) !== false) {
            $this->multiword = true;
        }
        $this->eligables = self::strip_some_tags($this->questiontext);
    }

    /**
     * strip all but the given html tags. Used to ensure that things
     * like table element tags are not displayed as clickable.
     *
     * @param string $questiontext
     * @return string
     */
    public static function strip_some_tags($questiontext) {
        return strip_tags($questiontext, '<h1>,<h2>,<h3>,<sub>,<sup>,<i>,<u>,<b>');
    }

    /**
     * TODO fix this comment as the purpose/return values have probably changed.
     * The text with delimiters removed so the user cannot see
     * which words are the ones that should be selected. So The cow [jumped]
     * becomes The cow jumped
     *
     * @return array
     */
    public function get_words() {
        $questiontextnodelim = $this->questiontext;
        $l = substr($this->delimitchars, 0, 1);
        $r = substr($this->delimitchars, 1, 1);
        $fieldregex = '/(\\s+)|(\\' . $l . '{1,2}[^\\'.$r.']*\\' . $r . '{1,2})|(&nbsp;)|(\s)/';
        $allwords = array();
        if (strpos($questiontextnodelim, $l . $l) !== false) {
            $this->multiword = true;
            $matches = preg_split($fieldregex, $questiontextnodelim, null, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);
            $this->items = [];
            foreach ($matches as $key => $match) {
                $item = new wordselect_item($key, $match, $this->delimitchars, true);
                $item->set_is_selectable();
                $this->items[] = $item;
            }
            $allwords = $matches[0];
        } else {
            $text = $this->pad_angle_brackets($questiontextnodelim);
            $this->eligables = self::strip_some_tags($text);

            $matches = preg_split($fieldregex, $text, null, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);
            $this->items = array();
            foreach ($matches as $key => $text) {
                $item = new wordselect_item($key, $text, $this->delimitchars, $this->multiword);
                $item->set_is_selectable($this->eligables);
                $this->items[] = $item;
            }
            $allwords = $matches[0];
        }
        return $this->items;
    }

    /**
     * The text with delimiters removed so the user cannot see
     * which words are the ones that should be selected. So The cow [jumped]
     * becomes The cow jumped
     *
     * @param string $text
     * @return string
     */
    public function stripdelim($text) {
        $l = substr($this->delimitchars, 0, 1);
        $r = substr($this->delimitchars, 1, 1);
        $matches = preg_replace('/\\' . $l . '/', '', $text);
        $matches = preg_replace('/\\' . $r . '/', '', $matches);
        return $matches;
    }

    /**
     * Confirm if the the provided haystack has a first character
     * matching the given needle. Handy for checking if the first
     * character is a delimiter.
     *
     * @param string $needle
     * @param string $haystack
     * @return string
     */
    public function startswith($needle, $haystack) {
        return preg_match('/^' . preg_quote($needle, '/') . '/', $haystack);
    }

    /**
     * Add one space to the pointy end of angle brackets.
     * This means that text within table fields can be set
     * as selectable. This ensures the td contents is split
     * from the td. Only makes sense in multi word mode (selectables
     * must be marked.
     *
     * @param string $questiontext
     * @return string
     */
    public static function pad_angle_brackets($questiontext) {
        /* Put a space before and after tags so they get split as words.
        will also accept <span style="color:colorname">word</span> */
        $tags = ['sub', 'sup', 'i', 'u', 'b', 'strike', 'em', 'strong', 'code', 'kbd', 'var', 'del', 'ins', 'small'];
        // Put a space before and after open tag html.
        $questiontext = preg_replace_callback('/(\<([a-zA-Z]*)[^>]*\>)/', function($a) use ($tags) {
            if (!empty($a[2]) && !in_array($a[2], $tags)) {
                return ' ' . $a[1] . ' ';
            }
            return $a[0];
        }, $questiontext);
        // Put a space before and after close tag html.
        $questiontext = preg_replace_callback('/\<\/([a-zA-Z]*)\>/', function($a) use ($tags) {
            if (!empty($a[1]) && !in_array($a[1], $tags)) {
                return ' ' . $a[0] . ' ';
            }
            return $a[0];
        }, $questiontext);
        return trim($questiontext);
    }

    /**
     * Put a space before and after tags so they get split as words
     * This allows the use of tables amongst other html things
     *
     * @param string $questiontext
     * @return string
     */
    public static function get_questiontext_exploded($questiontext) {
        $text = str_replace('>', '> ', $questiontext);
        $text = str_replace('<', ' <', $text);
        return $text;
    }

    /**
     *
     * Split the question text into words delimited by spaces
     * then return an array of all the words that are correct
     * i.e. surrounded by the delimit chars. Note that
     * word in this context means any string that can be separated
     * by a space marker so that will include html etc
     * @param string $questiontext raw text of question with delim
     * @param string $delimitchars delimiting characters e.g. [ and ]
     * @return array index places in array of correct words
     */
    public function get_correct_places($questiontext, $delimitchars) {
        $correctplaces = array();
        $items = $this->get_words(false);

        $l = substr($delimitchars, 0, 1);
        $r = substr($delimitchars, 1, 1);
        if ($this->multiword == true) {
            $regex = '/\\' . $l . '\\' . $l . '.*\\' . $r . '\\' . $r . '/';
        } else {
            $regex = '/\\' . $l . '.*\\' . $r . '/';
        }
        foreach ($items as $key => $item) {
            $word = $item->get_text();
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
        $wordcount = count($this->get_words());
        for ($key = 0; $key < $wordcount; $key++) {
            $data['p' . $key] = PARAM_RAW_TRIMMED;
        }
        return $data;
    }

    /**
     *
     * summary of response shown in the responses report
     *
     * @param array $response
     * @return string allwords
     */
    public function summarise_response(array $response) {
        $summary = '';
        $allwords = $this->get_words();
        foreach ($response as $index => $value) {
            if (!isset($allwords[substr($index, 1)])) {
                continue;
            }
            $summary .= " " . $allwords[substr($index, 1)]->get_without_delim() . " ";
        }
        return $summary;
    }

    /**
     * At runtime, decide if a word has been clicked on to select
     *
     * @param number $place
     * @param array $response
     * @return boolean
     */
    public function is_word_selected($place, $response) {
        $responseplace = 'p' . $place;
        if (isset($response[$responseplace]) && (($response[$responseplace] == "on" ) || ($response[$responseplace] == "true" ) )) {
            return true;
        } else {
            return false;
        }
    }

    /**
     *
     * Have any words been selected?
     *
     * @param array $response
     * @return boolean
     */
    public function is_complete_response(array $response) {
        foreach ($response as $item) {
            if ($item == "on") {
                return true;
            }
        }

        return false;
    }

    /**
     *
     * Get string validation to display for user.
     *
     * @param array $response
     * @return string
     */
    public function get_validation_error(array $response) {
        if (!$this->is_complete_response($response)) {
            return get_string('pleaseselectananswer', 'qtype_wordselect');
        }

        return '';
    }

    /**
     * if you are moving from viewing one question to another this will
     * discard the processing if the answer has not changed. If you don't
     * use this method it will constantantly generate new question steps and
     * the question will be repeatedly set to incomplete. This is a comparison of
     * the equality of two arrays. Without this deferred feedback behaviour probably
     * wont work.
     *
     * @param array $prevresponse
     * @param array $newresponse
     * @return boolean
     */
    public function is_same_response(array $prevresponse, array $newresponse) {
        if ($prevresponse === $newresponse) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * contains the a response that would get full marks.
     * used in preview mode. If this doesn't return a
     * correct value the button labeled "Fill in correct response"
     * in the preview form will not work. This value gets written
     * into the rightanswer field of the question_attempts table
     * when a quiz containing this question starts.
     *
     * @return question_answer
     */
    public function get_correct_response() {
        $correctplaces = $this->get_correct_places($this->questiontext, $this->delimitchars);
        $correctresponse = array();
        foreach ($correctplaces as $place) {
            $correctresponse['p' . $place] = 'on';
        }
        return $correctresponse;
    }

    /**
     * Checks whether the users is allow to be served a particular file.
     *
     * @param question_attempt $qa the question attempt being displayed.
     * @param question_display_options $options the options that control display of the question.
     * @param string $component the name of the component we are serving files for.
     * @param string $filearea the name of the file area.
     * @param array $args the remaining bits of the file path.
     * @param bool $forcedownload whether the user must be forced to download the file.
     * @return bool true if the user can access this file.
     */
    public function check_file_access($qa, $options, $component, $filearea, $args, $forcedownload) {
        if ($component == 'question' && $filearea == 'answerfeedback') {
            $currentanswer = $qa->get_last_qt_var('answer');
            $answer = $this->get_matching_answer(array('answer' => $currentanswer));
            $answerid = reset($args); // Itemid is answer id.
            return $options->feedback && $answer && $answerid == $answer->id;
        } else if ($component == 'question' && $filearea == 'hint') {
            return $this->check_hint_file_access($qa, $options, $args);
        } else if ($component == 'qtype_wordselect' && $filearea == 'introduction') {
            $question = $qa->get_question();
            if ($question->introduction > "") {
                return true;
            }
        } else {
            return parent::check_file_access($qa, $options, $component, $filearea, $args, $forcedownload);
        }
    }

    /**
     * Is this place correct and so get a mark if selected
     *
     * @param number $correctplaces
     * @param number $place
     * @return boolean
     */
    public function is_correct_place($correctplaces, $place) {
        if (in_array($place, $correctplaces)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Given a response, reset the parts that are wrong. Relevent in
     * interactive with multiple tries
     * @param array $response a response
     * @return array a cleaned up response with the wrong bits reset.
     */
    public function clear_wrong_from_response(array $response) {
        $correctplaces = $this->get_correct_places($this->questiontext, $this->delimitchars);
        foreach ($response as $key => $value) {
            /* chop off the leading p */
            $place = substr($key, 1);
            /* if its not in the correct places and it is turned on */
            if (!in_array($place, $correctplaces) && ($value == 'on')) {
                $response[$key] = 'off';
            }
        }
        return $response;
    }
    /**
     * Complete grade for this attempt at the question
     *
     * @param array $response responses, as returned by
     * {@link question_attempt_step::get_qt_data()}.
     * @return array (number, integer) the fraction, and the state.
     */
    public function grade_response(array $response) {
        $correctplaces = $this->get_correct_places($this->questiontext, $this->delimitchars);
        $this->wrongresponsecount = $this->get_wrong_responsecount($correctplaces, $response);
        foreach ($correctplaces as $place) {
            if (isset($response['p' . $place])) {
                if (( $response['p' . $place] === 'on') || ( $response['p' . $place] === 'true')) {
                    $this->rightresponsecount++;
                }
            }
        }
        $wrongfraction = @(($this->wrongresponsecount * $this->wordpenalty) / count($correctplaces));
        $fraction = @($this->rightresponsecount / count($correctplaces));
        $fraction = max(0, $fraction - $wrongfraction);
        $grade = array($fraction, question_state::graded_state_for_fraction($fraction));
        return $grade;
    }

    /**
     *
     * Called when using interactive with multiple tries question behaviour
     *
     * @param array $responses
     * @param int $totaltries The maximum number of tries allowed, not used here
     * @return numeric  fraction to be awarded for this sequence of responses
     *
     * $this->wordpenalty is the fraction value stored from the editingform.
     * So 1 means that the 100% of the value of each incorrect response
     * should be deducted from the final grade
     *
     * $this->penalty comes from the parent class and is from the multiple
     * tries section of the form, requiring hints to be active
     *
     */
    public function compute_final_grade($responses, $totaltries) {
        $attemptcount = -1;
        $fraction = 0;
        $correctplaces = $this->get_correct_places($this->questiontext, $this->delimitchars);
        foreach ($responses as $response) {
            $attemptcount++;
            $wrongresponsecount = $this->get_wrong_responsecount($correctplaces, $response);
            $valuecount = array_count_values($response);
            $selectedcount = $valuecount['on'];
            $rightresponsecount = $selectedcount - $wrongresponsecount;
            /* penalty for wrong selections on this/final attempt */
            $penalty = $wrongresponsecount * $this->wordpenalty;
            /* add penalty for each hint shown/try */
            $penalty += $attemptcount * $this->penalty;
            $fraction = @(($rightresponsecount - $penalty) / count($correctplaces));
            /*max ensures fraction is always > 0 */
            $fraction = max(0, $fraction);
        }
        return $fraction;
    }
    /**
     *
     * @param array $response Passed in from the runtime submission
     * @return array
     *
     * Find count of correct answers, used for displaying marks
     * for question. Compares answergiven with right/correct answer
     */
    public function get_num_parts_right(array $response) {
        $correctplaces = $this->get_correct_places($this->questiontext, $this->delimitchars);
        $this->wrongresponsecount = $this->get_wrong_responsecount($correctplaces, $response);
        $rightresponsecount = 0;
        foreach ($correctplaces as $place) {
            if (isset($response['p' . $place])) {
                if (( $response['p' . $place] === 'on') || ( $response['p' . $place] === 'true')) {
                    $rightresponsecount++;
                }
            }
        }
        return [$rightresponsecount, count($correctplaces)];
    }



    /**
     * Used when calculating the final grade
     * @param array $correctplaces
     * @param array $responses
     * @return int
     */
    public function get_wrong_responsecount($correctplaces, $responses) {
        $wrongresponsecount = 0;
        foreach ($responses as $key => $value) {
            /* chop off the leading p */
            $place = substr($key, 1);
            /* if its not in the correct places and it is turned on */
            if (!in_array($place, $correctplaces) && ($value == 'on')) {
                $wrongresponsecount++;
            }
        }
        return $wrongresponsecount;
    }
}

/**
 * Items to be processed by the question type.
 *
 * Typically this is a word or a group of words
 * @copyright Marcus Green 2018
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class wordselect_item {

    /**
     * characters that delimit a word or chunk of words
     * @var string
     */
    private $delimitchars;

    /**
     * left delimiter
     * @var string
     */
    private $l;

    /**
     * right delimiter
     * @var string
     */
    private $r;

    /**
     * question instance id
     * @var int
     */
    private $id;

    /**
     * item text including any delimiter
     * @var string
     */
    private $text;

    /**
     * do selectables have to be marked
     * @var boolean
     */
    private $multiword;

    /**
     * is this item selectable. Only makes
     * sense if multiword is true
     * @var boolean
     */
    public $isselectable;

    /**
     * Initialise this instance of question chunk
     *
     * @param number $id
     * @param string $text
     * @param string $delimitchars
     * @param boolean $multiword
     */
    public function __construct($id, $text, $delimitchars, $multiword = false) {
        $this->l = substr($delimitchars, 0, 1);
        $this->r = substr($delimitchars, 1, 1);
        $this->id = $id;
        $this->text = $text;
        $this->delimitchars = $delimitchars;
        $this->multiword = $multiword;
    }

    /**
     * Get white space after the "word" or group of words delimited
     * by double delimiting characters
     *
     * @param string $eligables
     * @return string
     */
    public function get_space_after($eligables) {
        if (strpos($eligables, $this->text) == false) {
            /* if this is never eligable for selection (typically
             * a piece of html e.g. <p>, then tag the original space back on to
             * the end of it
             */
            preg_match('/\s+|&nbsp;/', $this->text, $matches);
            if (isset($matches[0])) {
                return "&nbsp;";
            } else {
                return "";
            }
        } else {
            preg_match('/\s+|&nbsp;/', $this->text, $matches);
            if (isset($matches[0])) {
                return '&nbsp;';
            } else {
                return "";
            }
        }
    }

    /**
     * Work out which strings could be selectable typically anything that
     * is not an HTML tag. $eligables seems to be an awkward name, it could
     * have been called something like non-html but that is also awkward
     * and might be a limitation in the future if some other reason for text
     * being non eligable turns up.
     *
     * @param string $eligables
     **/
    public function set_is_selectable($eligables = "") {
        $this->isselectable = false;
        if ($this->multiword == true) {
            $regex = '/\\' . $this->l . '([^\\' . $this->l . '\\' . $this->r . ']*)\\' . $this->r . '/';
            if (preg_match($regex, $this->text) > 0) {
                $this->isselectable = true;
            }
        } else {
            $candidate = qtype_wordselect_question::strip_some_tags(trim($this->text));
            if (($eligables > "") && ($candidate > "")) {
                if (strpos($eligables, $candidate) !== false) {
                    if ($this->multiword == false) {
                        $this->isselectable = true;
                    } else {
                        $regex = '/\\' . $this->l . '([^\\' . $this->l . '\\' . $this->r . ']*)\\' . $this->r . '/';
                        if (preg_match($regex, $this->text) > 0) {
                            $this->isselectable = true;
                        }
                    }
                }
            }
        }
    }

    /**
     * Get chunk of questiontext for this item that will include
     * any delimiter.
     *
     * @return string
     */
    public function get_text() {
        return $this->text;
    }

    /**
     * Get the word (or set of words) without the delimiters
     * So [cat] will be returned as cat
     * @return string
     */
    public function get_without_delim() {
        $matches = preg_replace('/\\' . $this->l . '/', '', $this->text);
        $matches = preg_replace('/\\' . $this->r . '/', '', $matches);
        return $matches;
    }

}
