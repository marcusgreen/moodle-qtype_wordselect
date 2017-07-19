<?php
// This file is part of Moodle - http://moodle.org/

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
 * @copyright  Marcus Green 2017

 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();

/**
 * Represents a wordselect question.
 *
 * @copyright  2016 Marcus Green

 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_wordselect_question extends question_graded_automatically_with_countback {
    
    /**
     * @var string How many of the selections are of incorrect items
     */
    public $wrongresponsecount;
    /**
     * How many responses were correct
     * @var string
     */
    public $rightresponsecount;
    
    /**
     * Do all selectable items need to be marked with delimiters 
     * @var boolean
     */
    public $markedselectables = false;
    
    /** 
     * TODO
     * I am not sure this is necessary
     * @var string 
     */
    public $questiontextsplit;

    /**
     * array of "word" items
     * @var array
     */
    public $items;
    
    /**
     * Words that could be selectable (i.e. not html tags)
     * @var string 
     */
    public $eligables;

    /**
     * the characters indicating a field to fill i.e. [cat] creates
     * a field where the correct answer is cat
     * @var string 
     */
    public $delimitchars = "[]";

     /**
     * Convert index into how a paramter would be returned
     *
     * @param int $place
     * @return string
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
        $this->delmitchars = $delimitchars;
        $l = substr($this->delimitchars, 0, 1);
        $r = substr($this->delimitchars, 1, 1);
        if (strpos($questiontext, $l . $l) !== false) {
            $this->markedselectables = true;
        }
        $this->eligables = strip_tags($this->questiontext);
    }

    /**
     * TODO fix this comment as the purpose/return values have probably changed.
     * The text with delimiters removed so the user cannot see
     * which words are the ones that should be selected. So The cow [jumped]
     * becomes The cow jumped
     * 
     * @param boolean  $stripdelim (possibly redundant)
     * @return array
     */
    public function get_words($stripdelim = true) {
        $questiontextnodelim = $this->questiontext;
        $l = substr($this->delimitchars, 0, 1);
        $r = substr($this->delimitchars, 1, 1);
        $allwords = array();

        /* strinp html tags otherwise there is all manner of clickable debris */
        $this->selectable = explode(' ', strip_tags($questiontextnodelim));
        if (strpos($questiontextnodelim, $l . $l) !== false) {
            $this->markedselectables = true;
            $fieldregex = ' #\[+.*?\]+\s*|[^ ]+\s*#';
            $questiontextnodelim = $this->pad_angle_brackets($questiontextnodelim);
            $matches = preg_replace("#&nbsp;#", " ", $questiontextnodelim);
            preg_match_all($fieldregex, $matches, $matches);
            $this->items = array();
            foreach ($matches[0] as $key => $match) {
                $item = new wordselect_item($key, $match, $this->delimitchars, true);
                $item->set_is_selectable();
                $this->items[] = $item;
            }
     

            if ($stripdelim == true) {
                $allwords = $this->stripdelim($matches[0]);
            } else {
                $allwords = $matches[0];
            }
        } else {
            $text = $this->pad_angle_brackets($this->questiontext);
            $this->eligables = strip_tags($text);
            $regex = "/(\S+\s+)/";
            $matches= preg_split($regex, $text, null, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);

            $this->items = array();
            foreach ($matches as $key => $text) {
                $item = new wordselect_item($key, $text, $this->delimitchars, $this->markedselectables);
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
   function startsWith($needle, $haystack) {
        return preg_match('/^' . preg_quote($needle, '/') . '/', $haystack);
    }

    /**
     * Add one space to the pointy end of angle brackets.
     * This means that text within table fields can be set
     * as selectable. This ensures the td contents is split
     * from the td. Only makes sense in multi mode (selectables
     * must be marked.
     * 
     * @param string $questiontext
     * @return string
     */
    public static function pad_angle_brackets($questiontext) {
        // Put a space before and after tags so they get split as words.
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

        if ($this->markedselectables == true) {
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
            $summary .= " " . $allwords[substr($index, 1)]->get_without_delim() . " ";
        }
        return $summary;
    }

    /**
     * Was the word/s selected when the question was attempted
     *  
     * @param int $place
     * @param array $response e.g. p0=>'on, p1=>'on'
     * These match the positions of the words that were selected
     * @return boolean
     */
    public function is_word_selected($place, array $response) {
        $responseplace = 'p' . $place;
        if (isset($response[$responseplace]) && (($response[$responseplace] == "on" ) || ($response[$responseplace] == "true" ) )) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Have words have been selected
     * @param array $response
     * @return boolean
     */
    public function is_complete_response(array $response) {
        if (count($response) > 0) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Required because this class extends question_automatically_gradable
     * 
     * @param array $response
     * @return stringr
     */
    public  function get_validation_error(array $response) {
        // TODO.
        return '';
   }

    /**
     * 
     * If you are moving from viewing one question to another this will
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
     * @return question_answer an answer that
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
     * TODO Work out why this is this necessary in the sense of what does it 
     * do that the parent version does not do 
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
        } else {
            return parent::check_file_access($qa, $options, $component, $filearea, $args, $forcedownload);
        }
    }

   /**
    * Is this place a correct place?
    * 
    * @param array $correctplaces
    * @param int $place
    * @return boolean
    */
    public function is_correct_place(array $correctplaces, $place) {
        if (in_array($place, $correctplaces)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Complete grade for this attempt at the question
     * 
     * @param array $response responses, as returned by
     * {@link question_attempt_step::get_qt_data()}.
     * @return array (number, integer) the fraction, and the state.
     */
    public function grade_response(array $response) {
        $totalscore = 0;
        $correctplaces = $this->get_correct_places($this->questiontext, $this->delimitchars);
        $this->wrongresponsecount = $this->get_wrong_responsecount($correctplaces, $response);
        foreach ($correctplaces as $place) {
            if (isset($response['p' . $place])) {
                if (( $response['p' . $place] === 'on') || ( $response['p' . $place] === 'true')) {
                    $this->rightresponsecount++;
                }
            }
        }
        $wrongfraction = @($this->wrongresponsecount / count($correctplaces));
        $fraction = @($this->rightresponsecount / count($correctplaces));
        $fraction = max(0, $fraction - $wrongfraction);
        $grade = array($fraction, question_state::graded_state_for_fraction($fraction));
        return $grade;
    }

    /**
     * Work out the final grade, not called in interactive mode
     * TODO explain that further
     * @param array $response
     * @param int  $totaltries
     * @return int 
     * TODO find out if it really is an int that is returned or if it can
     * be fractional.
     */
    public function compute_final_grade($response, $totaltries) {
        $totalscore = 0;
        $correctplaces = $this->get_correct_places($this->questiontext, $this->delimitchars);
        $wrongresponsecount = $this->get_wrong_responsecount($correctplaces, $response[0]);
        foreach ($correctplaces as $place) {
            $lastwrongindex = -1;
            $finallyright = false;
            foreach ($response as $i => $r) {
                if (!array_key_exists(('p' . $place), $r)) {
                    $lastwrongindex = $i;
                    $finallyright = false;
                    continue;
                } else {
                    $finallyright = true;
                }
            }
            if ($finallyright) {
                $totalscore += max(0, 1 - ($lastwrongindex + 1) * $this->penalty);
            }
        }
        $wrongfraction = @($wrongresponsecount / count($correctplaces));
        $totalscore = $totalscore / count($correctplaces);
        $totalscore = max(0, $totalscore - $wrongfraction);
        return $totalscore;
    }

    /**
     * Used when calculating the final grade
     * @param array $correctplaces
     * @param array $response
     * @return int
     */
       public function get_wrong_responsecount($correctplaces, $response) {
        $wrongresponsecount = 0;
        foreach ($response as $key => $value) {
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
 * items that will be processed by the question type. Typically this is a word
 * or a group of words
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
    private $markedselectables;
    
    /** 
     * is this item selectable. Only makes
     * sense if markedselectables is true
     * @var boolean
     */
    public $is_selectable;

    
    /**
     * Initialise this instance of question chunk
     * @param int $id
     * @param string $text
     * @param string $delimitchars
     * @param boolean $markedselectables
     */
    public function __construct($id, $text, $delimitchars, $markedselectables = false) {
        $this->l = substr($delimitchars, 0, 1);
        $this->r = substr($delimitchars, 1, 1);
        $this->id = $id;
        $this->text = $text;
        $this->delimitchars = $delimitchars;
        $this->markedselectables = $markedselectables;
    }

    /**
     * Get white space after the "word" or group of words delimited
     * by double delimiting characters
     * 
     * @param string $eligables
     * @return string
     */
    public function get_space_after($eligables) {
        if ($this->text == "") {
            return "";
        }
        if (strpos($eligables, $this->text) == false) {
            /* if this is nevery eligable for selection (typically 
             * a piece of html e.g. <p>, then tag the original space back on to
             * the end of it
             */
            preg_match('/\s+/', $this->text, $matches);
            if (isset($matches[0])) {
                return $matches[0];
            } else {
                return "";
            }
        } else {
            preg_match('/\s+/', $this->text, $matches);
            if (isset($matches[0])) {
                $len = strlen($matches[0]);
                if ($len > 1) {
                    print "returning  " . $len . " spaces";
                    return " " . str_repeat('&nbsp;', $len);
                } else {
                    return " ";
                }
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
     * @param string $eligables
     */
    public function set_is_selectable($eligables = "") {
        if ($this->markedselectables == true) {
            $regex = '/\\' . $this->l . '([^\\' . $this->l . '\\' . $this->r . ']*)\\' . $this->r . '/';
            if (preg_match($regex, $this->text) > 0) {
                $this->is_selectable = true;
            }
        } else {
            if (($eligables > "") && ($this->get_without_delim() > "")) {
                if (strpos($eligables, $this->get_without_delim()) !== false) {
                    if ($this->markedselectables == false) {
                        $this->is_selectable = true;
                    } else {
                        $regex = '/\\' . $this->l . '([^\\' . $this->l . '\\' . $this->r . ']*)\\' . $this->r . '/';
                        if (preg_match($regex, $this->text) > 0) {
                            $this->is_selectable = true;
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
        /* trim trailing html space characters */
        $matches = preg_replace("#(^(&nbsp;|\s)+|(&nbsp;|\s)+$)#", "", $matches);
        return $matches;
    }

}
