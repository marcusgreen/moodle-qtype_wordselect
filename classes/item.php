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
 * wordselect item definition class.
 *
 * @package    qtype_wordselect
 * @copyright  Marcus Green 2018
 *
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace qtype_wordselect;

/**
 * Items to be processed by the question type.
 *
 * Typically this is a word or a group of words
 * @copyright Marcus Green 2018
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class item {
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
     * @var bool
     */
    private $multiword;

    /**
     * is this item selectable. Only makes
     * sense if multiword is true
     * @var bool
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
        $this->multiword = $multiword;
    }

    /**
     * Set the place id of the item
     *
     * @return void
     */
    public function get_id() {
        return $this->id;
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
            $candidate = \qtype_wordselect_question::strip_some_tags(trim($this->text));
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
