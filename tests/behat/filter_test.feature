@core @qtype @qtype_wordselect @qtype_wordselect_mlang @_switch_window
Feature: Test the mlang and mlang2 filters work with qtype_wordselect
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
    And the "multilang2" filter is "on"
    And the "multilang" filter is "on"

  @javascript
  Scenario: Create a question and check mlang2 works as expected
    When I am on the "Course 1" "core_question > course question bank" page logged in as teacher1

  # Create a new question with mlang2 tags
  # Then check that the french words are not displayed
    And I add a "Word Select" question filling the form with:
        | Question name | Word-Select-001                                                  |
        | Introduction  | {mlang fr}deaux{mlang}{mlang other}two{mlang}                    |
        | Question text | [correct] {mlang fr}un{mlang}{mlang other}one{mlang} [selection] |
    Then I should see "Word-Select-001"

  # Preview it.
    When I am on the "Word-Select-001" "core_question > preview" page
    And I should see "two"
    And I should see "one"
    And I should see "selection"
    And I should not see "deaux"
    And I should not see "un"

    And I click on "correct" "text"
    And I click on "selection" "text"
    And I press "Submit"
    And I should see "Mark 2.00 out of 2.00"
    And I should see "Your answer is correct"

  # This is supposed to test the core mlang filter, however I cannot get that filter to work
  # at all under any circumstance
  # @javascript
  # Scenario: Create a question and check (core) mlang works as expected
  #   Given I log in as "teacher1"
  #   And I am on "Course 1" course homepage
  #   And I navigate to "Questions" in current page administration
  #   And I add a "Word Select" question filling the form with:
  #       | Question name | Word-Select-002                                                 |
  #       | Introduction  | <span lang="fr" class="multilang">deaux</span> two              |
  #       | Question text | [correct] <span lang="fr" class="multilang">trois</span> [word] |
  #   Then I should see "Word-Select-002"
  #   #Check that the french words are not displayed
  #   When I choose "Preview" action for "Word-Select-002" in the question bank
  #   And I switch to "questionpreview" window
  #   And I should see "two"
  #   And I should not see "deaux"
  #   And I should not see "trois"
  #   And I should not see "correct"
