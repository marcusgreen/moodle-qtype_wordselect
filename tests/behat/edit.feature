@qtype @qtype_wordselect @qtype_wordselect_edit
Feature: Test editing a word-select question
  As a teacher
  In order to be able to update my word-select questions
  I need to edit them

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email               |
      | teacher1 | T1        | Teacher1 | teacher1@moodle.com |
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1        | 0        |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |
    And the following "question categories" exist:
      | contextlevel | reference | name           |
      | Course       | C1        | Test questions |
    And the following "questions" exist:
      | questioncategory | qtype      | name        | template |
      | Test questions   | wordselect | Word-select | catmat   |

  @javascript
  Scenario: Edit a wordselect question
  #Changing to plain text mitigates slowness
    When I am on the "Course 1" "core_question > course question bank" page logged in as teacher1
    And I follow "Preferences" in the user menu
    And I follow "Editor preferences"
    And I set the field "Text editor" to "Plain text area"
    And I press "Save changes"
    When I am on the "Course 1" "core_question > course question bank" page logged in as teacher1

    When I choose "Edit question" action for "Word-select" in the question bank
    And I set the following fields to these values:
      | Question name            | Edited question name |
      | For any correct response |                      |
    And I press "id_submitbutton"
    Then I should see "Edited question name"
    And I choose "Edit question" action for "Edited question name" in the question bank
    And the field "For any correct response" matches value ""

  @javascript @_file_upload @editor_tiny
  Scenario: Edit then move a wordselect question.
    Given the following "courses" exist:
      | fullname | shortname | category |
      | Course 2 | C2        | 0        |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C2     | editingteacher |
    And the following "activities" exist:
      | activity | name            | course | idnumber |
      | qbank    | Question bank 2 | C2     | qbank2   |
    When I am on the "Course 1" "core_question > course question bank" page logged in as teacher1
    And I choose "Edit question" action for "Word-select" in the question bank
    And I expand all fieldsets
    And I expand all toolbars for the "For any correct response" TinyMCE editor
    And I click on the "Image" button for the "For any correct response" TinyMCE editor
    And I click on "Browse repositories" "button" in the "Insert image" "dialogue"
    And I upload "lib/editor/tiny/tests/behat/fixtures/moodle-logo.png" to the file picker for TinyMCE
    And I set the field "How would you describe this image to someone who cannot see it?" to "Feedback image"
    And I click on "Save" "button" in the "Image details" "dialogue"
    And I press "id_submitbutton"
    # Move the question from Course 1 (course context) to Course 2's qbank activity (module context).
    And I am on the "Course 1" "core_question > course question bank" page
    And I apply question bank filter "Category" with value "Test questions"
    And I click on "Word-select" "checkbox"
    And I click on "With selected" "button"
    And I click on "move" "button"
    And I open the autocomplete suggestions list in the ".search-banks" "css_element"
    And I click on "C2 - Question bank 2" item in the autocomplete list
    And I click on "Move questions" "button"
    And I should see "Are you sure you want to move these questions?"
    And I click on "Confirm" "button"
    And I wait until the page is ready
    And I should see "Questions successfully moved"
    # Verify image in the per-input feedback is still served correctly.
    And I am on the "Question bank 2" "core_question > question bank" page
    And I choose "Edit question" action for "Word-select" in the question bank
    And I expand all fieldsets
    And I switch to the "For any correct response" TinyMCE editor iframe
    Then "img[alt='Feedback image']" "css_element" should exist
