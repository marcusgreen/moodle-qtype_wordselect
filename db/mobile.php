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
 * Wordselect question type upgrade code.
 *
 * @package    qtype_wordselect
 * @copyright  2018 Marcus Green
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();
$addons = array(
    "qtype_wordselect" => array(
        "handlers" => array( // Different places where the add-on will display content.
            'wordselect' => array( // Handler unique name (can be anything).
                'displaydata' => array(
                    'title' => 'Qtype Name',
                    'icon' => '/question/type/wordselect/pix/icon.gif',
                    'class' => '', //What does this do?
                ),
                'delegate' => 'CoreQuestionDelegate', // Delegate (where to display the link to the add-on).
                'method' => 'mobile_get_wordselect',
                'offlinefunctions' => array(
                    'mobile_get_wordselect' => array(),
                ), // Function needs caching for offline.
               'styles' => array(
                    'url' => '/question/type/wordselect/styles_app.css',
                    'version' => '1.00'
                ),
                'lang' => array(
                    array('Question name', 'pluginname')
                )
            )
        ),
    )
);