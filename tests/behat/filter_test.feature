@core @qtype @qtype_wordselect @qtype_wordselect_mlang
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
    When the filter_multilang2 plugin is installed
    And I am on the "Course 1" "core_question > course question bank" page logged in as teacher1

  # Create a new question with mlang2 tags
  # Then check that the french words are not displayed
    And I add a "Word Select" question filling the form with:
        | Question name | Word-Select-001                                                  |
        | Introduction  | {mlang fr}deaux{mlang}{mlang other}two{mlang}                    |
        | Question text | [correct] {mlang fr}un{mlang}{mlang other}one{mlang} [selection] |
    Then I should see "Word-Select-001"

  # Preview it.
    And I am on the "Word-Select-001" "core_question > preview" page
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

  @javascript
  Scenario: Create a question and check (core) mlang works as expected
    When I am on the "Course 1" "core_question > course question bank" page logged in as teacher1
    And I add a "Word Select" question filling the form with:
        | Question name | Word-Select-002                                                 |
        | Introduction  | <span lang="en" class="multilang">two</span><span lang="fr" class="multilang">deaux</span>                    |
        | Question text | [correct] <span lang="en" class="multilang">three</span><span lang="fr" class="multilang">trois</span> [word] |
    Then I should see "Word-Select-002"
    #Check that the french words are not displayed
    And I am on the "Word-Select-002" "core_question > preview" page
    And I should see "two"
    And I should not see "deaux"
    And I should not see "trois"
    And I should see "correct"
