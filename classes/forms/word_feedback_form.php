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
namespace qtype_wordselect\forms;

use moodleform;

defined('MOODLE_INTERNAL') || die();

require_once $CFG->dirroot . '/lib/formslib.php';

class word_feedback_form extends moodleform
{

    public function get_protected_form()
    {
        return $this->_form;
    }

    /**
     * Define this form - is called from parent constructor.
     */
    public function definition()
    {
        global $USER, $CFG, $COURSE;

        $mform = $this->_form;

        $context = \context_system::instance();

        $mform->addElement('text', 'correctfeedback', 'Correct feedback', array('size' => '64'));
        $this->add_action_buttons(true);
    }
}
