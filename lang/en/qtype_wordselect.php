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
 * Strings for component 'qtype_wordselect', language 'en', branch 'MOODLE_20_STABLE'
 *
 * @package    qtype_wordselect
 * @copyright  2017 Marcus Green

 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
$string['pluginname'] = 'Word Select';
$string['pluginname_help'] = 'Put delimiters around words considered correct, e.g. The cat [sat] on the mat. At runtime the user can select any of the words apart from the introduction text.';
$string['pluginname_link'] = 'Wordselect_question_type';
$string['pluginnameadding'] = 'Adding a wordselect question';
$string['pluginnameediting'] = 'Editing a wordselect question';
$string['pluginnamesummary'] = 'All words can be selected by clicking on them. Correct words are defined by surrounding with delimiters e.g. The cat [sat] on the mat.';
$string['delimitchars'] = 'Delimit characters';
$string['delimitchars_help'] = 'Change the characters that delimit a field from the default [ ], useful for programming language questions.';
$string['nowordsdefined'] = 'No words to select have been defined ';
$string['correctresponse'] = 'correct response ';
$string['incorrectresponse'] = 'incorrect response ';
$string['correctanswer'] = 'correct answer';
$string['questiontext'] = 'question text';
$string['questiontext_help'] = 'Put square braces around the correct words ';
$string['introduction'] = 'Introduction';
$string['introduction_help'] = 'Introduction to the question, this text will not be selectable';
$string['pleaseselectananswer'] = 'Please select an answer.';
$string['privacy:null_reason'] = 'The Wordselect question type does not effect or store any data itself.';
$string['wordpenalty'] = 'Incorrect selection penalty';
$string['wordpenalty_help'] = 'Decrement mark by this percentage for each incorrectly selected word';
$string['penalty'] = 'Penalty';
$string['wordpenalty_setting'] = 'Penalty for each incorrect text item selected';
$string['yougotnrightcount'] = 'You have selected {$a->num} correct items out of {$a->outof}.';
$string['taptoselect'] = 'Tap to select';