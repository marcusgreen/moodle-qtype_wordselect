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
 * Behat steps definitions for the wordselect question type.
 *
 * @package    qtype_wordselect
 * @category   test
 * @copyright  2026 Marcus Green
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// NOTE: no MOODLE_INTERNAL test here, this file may be required by behat before including /config.php.

require_once(__DIR__ . '/../../../../../lib/behat/behat_base.php');

/**
 * Steps definitions related to the wordselect question type.
 *
 * @copyright  2026 Marcus Green
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class behat_qtype_wordselect extends behat_base {
    /**
     * Click a question bank bulk action.
     *
     * Moodle 5.2 and earlier hide bulk actions behind a "With selected" menu,
     * which was removed in Moodle 5.3, so open it only when present.
     *
     * @When I click on the :action question bulk action
     * @param string $action the name of the bulk action button, e.g. move.
     */
    public function i_click_on_the_question_bulk_action(string $action): void {
        $toggle = $this->getSession()->getPage()->find('named_exact', ['button', 'With selected']);
        if ($toggle && $toggle->isVisible()) {
            $toggle->click();
        }
        $this->execute('behat_general::i_click_on', [$action, 'button']);
    }
}
