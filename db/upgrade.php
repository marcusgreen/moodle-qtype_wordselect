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

/**
 * Upgrade code for the wordselect question type.
 * @param int $oldversion the version we are upgrading from.
 */
function xmldb_qtype_wordselect_upgrade($oldversion = 0) {
    global $DB;
    $dbman = $DB->get_manager();
    if ($oldversion < 2018120300) {
        if (!$dbman->field_exists('question_wordselect', 'wordpenalty')) {
            $field = new xmldb_field('wordpenalty', XMLDB_TYPE_NUMBER, '12, 8',
                    null, XMLDB_NOTNULL, null, '1', 'delimitchars');
            $table = new xmldb_table('question_wordselect');
            $dbman->add_field($table, $field);
        }
        // Wordselect savepoint reached.
        upgrade_plugin_savepoint(true, 2018120300, 'qtype', 'wordselect');
    }
    return true;
}
