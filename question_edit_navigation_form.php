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
 * @package    qtype
  /* * @subpackage wordselect
 * @copyright  2016 Marcus Green

 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();

/**
 * wordselect question editing form definition.
 *
 * @copyright  2016 Marcus Green

 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class question_edit_navigation_form extends question_edit_form {

    /**
     * Build the form definition.
     *
     * This adds all the form fields that the default question type supports.
     * If your question type does not support all these fields, then you can
     * override this method and remove the ones you don't want with $mform->removeElement().
     */
    public $simplenav=true;
    protected function definition() {
        
        $this->simplenav=true;

        if($this->simplenav==false){
            parent::definition();
            return;
        }

        global $COURSE, $CFG, $DB, $PAGE;

        $qtype = $this->qtype();
        $langfile = "qtype_{$qtype}";

        $mform = $this->_form;
        $mform->addElement('html','</span>');


        // Standard fields at the start of the form.
        // $mform->addElement('header', 'generalheader', get_string("general", 'form'));
        //$menu[0] = new navmenu('name', "Question Name");
        $menu[1] = new navmenu('questiontext', "Question Text");
        $menu[2] = new navmenu('generalfeedback', "General Feedback");
        $menu[3] = new navmenu('combinedfeedback', "Combined Feedback");
        $menu[4] = new navmenu('multitries', "Multiple Tries");
        $menu[5] = new navmenu('complete', "Complete");

        $m = new navmenu('introduction', "Introduction");
        $menu = $this->array_insert($menu, $m, 0);

        $arrow = "►";
        $navbar = "<span id=question_nav>";
        for ($i = 0; $i < count($menu); $i++) {
            $navbar.="<a href=# id=a_" . $menu[$i]->id . "><span id=" . $menu[$i]->id . ">";
            $navbar.=$menu[$i]->value . "</></a>" . $arrow;
        }
        $navbar.="</span>";

        $mform->addElement('html', $navbar);


        if (!isset($this->question->id)) {
            if (!empty($this->question->formoptions->mustbeusable)) {
                $contexts = $this->contexts->having_add_and_use();
            } else {
                $contexts = $this->contexts->having_cap('moodle/question:add');
            }

            // Adding question.
            $mform->addElement('questioncategory', 'category', get_string('category', 'question'), array('contexts' => $contexts));
        } else if (!($this->question->formoptions->canmove ||
                $this->question->formoptions->cansaveasnew)) {
            // Editing question with no permission to move from category.
            $mform->addElement('questioncategory', 'category', get_string('category', 'question'), array('contexts' => array($this->categorycontext)));
            $mform->addElement('hidden', 'usecurrentcat', 1);
            $mform->setType('usecurrentcat', PARAM_BOOL);
            $mform->setConstant('usecurrentcat', 1);
        } else {
            // Editing question with permission to move from category or save as new q.
            $currentgrp = array();
            $currentgrp[0] = $mform->createElement('questioncategory', 'category', get_string('categorycurrent', 'question'), array('contexts' => array($this->categorycontext)));
            if ($this->question->formoptions->canedit ||
                    $this->question->formoptions->cansaveasnew) {
                // Not move only form.
                $currentgrp[1] = $mform->createElement('checkbox', 'usecurrentcat', '', get_string('categorycurrentuse', 'question'));
                $mform->setDefault('usecurrentcat', 1);
            }
            $currentgrp[0]->freeze();
            $currentgrp[0]->setPersistantFreeze(false);
            $mform->addGroup($currentgrp, 'currentgrp', get_string('categorycurrent', 'question'), null, false);

            $mform->addElement('questioncategory', 'categorymoveto', get_string('categorymoveto', 'question'), array('contexts' => array($this->categorycontext)));
            if ($this->question->formoptions->canedit ||
                    $this->question->formoptions->cansaveasnew) {
                // Not move only form.
                $mform->disabledIf('categorymoveto', 'usecurrentcat', 'checked');
            }
        }


        $mform->addElement('text', 'name', get_string('questionname', 'question'), array('size' => 50, 'maxlength' => 255));
        $mform->setType('name', PARAM_TEXT);
        $mform->addRule('name', null, 'required', null, 'client');

        $mform->addElement('editor', 'questiontext', get_string('questiontext', 'question'), array('rows' => 15), $this->editoroptions);
        $mform->setType('questiontext', PARAM_RAW);
        $mform->addRule('questiontext', null, 'required', null, 'client');

        $mform->addElement('text', 'defaultmark', get_string('defaultmark', 'question'), array('size' => 7));
        $mform->setType('defaultmark', PARAM_FLOAT);
        $mform->setDefault('defaultmark', 1);
        $mform->addRule('defaultmark', null, 'required', null, 'client');

        $mform->addElement('editor', 'generalfeedback', get_string('generalfeedback', 'question'), array('rows' => 10), $this->editoroptions);
        $mform->setType('generalfeedback', PARAM_RAW);
        $mform->addHelpButton('generalfeedback', 'generalfeedback', 'question');

        // Any questiontype specific fields.
        $this->definition_inner($mform);

        /* mavg */
        /* if (core_tag_tag::is_enabled('core_question', 'question')) {
          $mform->addElement('header', 'tagsheader', get_string('tags'));
          }

         */
        $mform->addElement('tags', 'tags', get_string('tags'), array('itemtype' => 'question', 'component' => 'core_question'));

        if (!empty($this->question->id)) {
            //    $mform->addElement('header', 'createdmodifiedheader',
            //            get_string('createdmodifiedheader', 'question'));
            $a = new stdClass();
            if (!empty($this->question->createdby)) {
                $a->time = userdate($this->question->timecreated);
                $a->user = fullname($DB->get_record(
                                'user', array('id' => $this->question->createdby)));
            } else {
                $a->time = get_string('unknown', 'question');
                $a->user = get_string('unknown', 'question');
            }
            $mform->addElement('static', 'created', get_string('created', 'question'), get_string('byandon', 'question', $a));
            if (!empty($this->question->modifiedby)) {
                $a = new stdClass();
                $a->time = userdate($this->question->timemodified);
                $a->user = fullname($DB->get_record(
                                'user', array('id' => $this->question->modifiedby)));
                $mform->addElement('static', 'modified', get_string('modified', 'question'), get_string('byandon', 'question', $a));
            }
        }

        parent::add_hidden_fields();
        $mform->removeElement('combinedfeedbackhdr');

        $mform->addElement('hidden', 'qtype');
        $mform->setType('qtype', PARAM_ALPHA);

        $mform->addElement('hidden', 'makecopy');
        $mform->setType('makecopy', PARAM_INT);

        $mform->addElement('html', '</span>');
        
        $mform->addElement('html', '<span id="savecontinuepreview" style="display:none">');

        $buttonarray = array();

        $buttonarray[] = $mform->createElement('submit', 'updatebutton', get_string('savechangesandcontinueediting', 'question'));
        if ($this->can_preview()) {
            $previewlink = $PAGE->get_renderer('core_question')->question_preview_link(
                    $this->question->id, $this->context, true);
            $buttonarray[] = $mform->createElement('static', 'previewlink', '', $previewlink);
        }

        $mform->addGroup($buttonarray, 'updatebuttonar', '', array(' '), false);


        $this->add_action_buttons(true, get_string('savechanges'));


        if ((!empty($this->question->id)) && (!($this->question->formoptions->canedit ||
                $this->question->formoptions->cansaveasnew))) {
            $mform->hardFreezeAllVisibleExcept(array('categorymoveto', 'buttonar', 'currentgrp'));
        }
        $mform->addElement('html', '</span>');
    }

    protected function add_combined_feedback_fields_simplenav($withshownumpartscorrect = false) {
        $mform = $this->_form;
        $fields = array('correctfeedback', 'partiallycorrectfeedback', 'incorrectfeedback');
        foreach ($fields as $feedbackname) {
            $element = $mform->addElement('editor', $feedbackname, get_string($feedbackname, 'question'), array('rows' => 5), $this->editoroptions);
            $mform->setType($feedbackname, PARAM_RAW);
            // Using setValue() as setDefault() does not work for the editor class.
            $element->setValue(array('text' => get_string($feedbackname . 'default', 'question')));

            if ($withshownumpartscorrect && $feedbackname == 'partiallycorrectfeedback') {
                $mform->addElement('advcheckbox', 'shownumcorrect', get_string('options', 'question'), get_string('shownumpartscorrectwhenfinished', 'question'));
                $mform->setDefault('shownumcorrect', true);
            }
        }
    }

    protected function add_interactive_settings($withclearwrong = false, $withshownumpartscorrect = false) {
        $mform = $this->_form;

        $penalties = array(
            1.0000000,
            0.5000000,
            0.3333333,
            0.2500000,
            0.2000000,
            0.1000000,
            0.0000000
        );
        if (!empty($this->question->penalty) && !in_array($this->question->penalty, $penalties)) {
            $penalties[] = $this->question->penalty;
            sort($penalties);
        }
        $penaltyoptions = array();
        foreach ($penalties as $penalty) {
            $penaltyoptions["{$penalty}"] = (100 * $penalty) . '%';
        }
        $mform->addElement('select', 'penalty', get_string('penaltyforeachincorrecttry', 'question'), $penaltyoptions);
        $mform->addHelpButton('penalty', 'penaltyforeachincorrecttry', 'question');
        $mform->setDefault('penalty', 0.3333333);

        if (isset($this->question->hints)) {
            $counthints = count($this->question->hints);
        } else {
            $counthints = 0;
        }

        if ($this->question->formoptions->repeatelements) {
            $repeatsatstart = max(self::DEFAULT_NUM_HINTS, $counthints);
        } else {
            $repeatsatstart = $counthints;
        }

        list($repeated, $repeatedoptions) = $this->get_hint_fields(
                $withclearwrong, $withshownumpartscorrect);
        $this->repeat_elements($repeated, $repeatsatstart, $repeatedoptions, 'numhints', 'addhint', 1, get_string('addanotherhint', 'question'), true);
    }

    public function qtype() {
        return 'wordselect';
    }

    function array_insert($arr, $insert, $position) {
        $i = 0;
        $ret = array();
        foreach ($arr as $key => $value) {
            if ($i == $position) {
                $ret[] = $insert;
            }
            $ret[] = $value;
            $i++;
        }
        return $ret;
    }

    /**
     * 
     * Copied from formslib
     * Use this method to a cancel and submit button to the end of your form. Pass a param of false
     * if you don't want a cancel button in your form. If you have a cancel button make sure you
     * check for it being pressed using is_cancelled() and redirecting if it is true before trying to
     * get data with get_data().
     *
     * @param bool $cancel whether to show cancel button, default true
     * @param string $submitlabel label for submit button, defaults to get_string('savechanges')
     */
    function add_action_buttons($cancel = true, $submitlabel = null) {
        if (is_null($submitlabel)) {
            $submitlabel = get_string('savechanges');
        }
        $mform = & $this->_form;
        if ($cancel) {
            //when two elements we need a group
            $buttonarray = array();
            $buttonarray[] = &$mform->createElement('submit', 'submitbutton', $submitlabel);
            $buttonarray[] = &$mform->createElement('cancel');
            $mform->addGroup($buttonarray, 'buttonar', '', array(' '), false);
            //$mform->closeHeaderBefore('buttonar');
        } else {
            //no group needed
            $mform->addElement('submit', 'submitbutton', $submitlabel);
            $mform->closeHeaderBefore('submitbutton');
           
        }
    }
    public function add_hidden_fields(){
        return;
    }

}

class navmenu {

    public $value = '';
    public $id = '';

    public function __construct($id, $value) {
        $this->id = $id;
        $this->value = $value;
    }

}
