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
 * Mobile output class for qtype_wordselect
 *
 * @package    qtype_wordselect
 * @copyright  2021 Marcus Green
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace qtype_wordselect\output;

defined('MOODLE_INTERNAL') || die();

/**
 * Mobile output class for wordselect question type
 *
 * @package    qtype_wordselect
 * @copyright  2021 Marcus Green
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class mobile {

    /**
     * Returns the wordselect quetion type for the quiz the mobile app.
     *
     * @return void
     */
    public static function mobile_get_wordselect($args) {
        global $CFG;
        $args = (object) $args;
        $folder = $args->appversioncode >= 3950 ? 'latest' : 'ionic3';
        $templatepath = $CFG->dirroot."/question/type/wordselect/mobile/$folder/addon-qtype-wordselect.html";
        $template = file_get_contents($templatepath);
        $jsfilepath = $CFG->dirroot . '/question/type/wordselect/mobile/mobile.js';
        $jscontent = file_get_contents($jsfilepath);
        return [
            'templates' => [
                [
                    'id' => 'main',
                    'html' => $template
                ]
            ],
            'javascript' => $jscontent
        ];
    }
}
