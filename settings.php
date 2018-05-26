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
 * Data to control defaults when creating and running a question
 *
 * @package    qtype_wordselect
 * @copyright  2018 Marcus Green
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

require_once($CFG->libdir . '/formslib.php');

if ($ADMIN->fulltree) {
     $penalties = array(
            1.0000000,
            0.5000000,
            0.3333333,
            0.2500000,
            0.2000000,
            0.1000000,
            0.0000000
        );

    $penaltyoptions = array();
    foreach ($penalties as $penalty) {
        $penaltyoptions["{$penalty}"] = (100 * $penalty) . '%';
    }

    $settings->add(new admin_setting_configselect('qtype_wordselect/wordpenalty',
            new lang_string('penalty', 'qtype_wordselect'),
            new lang_string('wordpenalty_setting', 'qtype_wordselect'),
            1, $penaltyoptions));
}