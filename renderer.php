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
 * @copyright  THEYEAR YOURNAME (YOURCONTACTINFO)

 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();
require_once('Kint/Kint.class.php');

/**
 * Generates the output for wordselect questions.
 *
 * @copyright  THEYEAR YOURNAME (YOURCONTACTINFO)

 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
require_once('Kint/Kint.class.php');

class qtype_wordselect_renderer extends qtype_with_combined_feedback_renderer {

    public function formulation_and_controls(question_attempt $qa, question_display_options $options) {
        global $PAGE;
        $question = $qa->get_question();
        $response = $qa->get_last_qt_data();

       $output=$question->introduction;
       $output .= '<div class="selectable">';
        foreach ($question->get_all_words() as $place => $value) {
            $qprefix = $qa->get_qt_field_name('');
            $inputname = $qprefix . 'p' . ($place);
            $checked=null;
            if(array_key_exists('p'.($place),$response)){
               $checked='checked=true';
            }            
            $output.="<input hidden=true ".$checked ." type='checkbox' name=" . $inputname . " id=" . $inputname . "></input>";
            $output.="<label for=" . $inputname . ">" . $value . "</label>";
        }
        $output.="</div>";
        return $output;
    }

}
