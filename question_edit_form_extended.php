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
 * Defines the editing form for the wordselect question type.
 *
 * @package    qtype_wordselect
 * @copyright  2017 Marcus Green

 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();
/**
 * wordselect question editing form definition.
 *
 * @copyright  2016 Marcus Green

 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

class question_edit_form_extended extends question_edit_form {
    
        protected function definition(){
            
        global $PAGE;
        $PAGE->requires->jquery();
        $PAGE->requires->js('/question/type/wordselect/navigation.js');
           $mform = $this->_form;
         //  $mform->addElement('html','<div style="display:none">');
            parent::definition();
          //  $mform->addElement('html','<div style="display:block">');
        }
       
       
      /**
     * Name of this question type
     * @return string
     */
    public function qtype(){}
    
}




