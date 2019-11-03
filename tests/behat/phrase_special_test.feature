#This test and special character code in qtype was created by https://github.com/lethevinh
@core @qtype @qtype_wordselect @qtype_wordselect_special @_switch_window

Feature: Test that formatting within delimiters is retained
  In order to evaluate students responses, As a teacher I need to
  create and preview wordselect (Select correct words) questions.

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

  @javascript
  Scenario: Create, edit then preview a wordselect question.
    Given I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I navigate to "Questions" in current page administration

    # Create a new question.
    And I add a "Word Select" question filling the form with:
      | Question name             | Word-Select-001                   |
      | Introduction              | Select the verbs in the following text  |
      | Question text             | The cat [[<b>sat</b>]] and the cow [[jumped]] [[<u>meo</u>]]  [[10<sup>3</sup>]]  [[log<sub>3</sub>]] |
      | General feedback          | This is general feedback       |
      | Hint 1                    | First hint                    |
      | Hint 2                    | Second hint                   |
    Then I should see "Word-Select-001"

    # Preview it.
    When I choose "Preview" action for "Word-Select-001" in the question bank
    And I switch to "questionpreview" window

    #################################################
    #Interactive with multiple triddes
    #################################################
    And I set the following fields to these values:
      | How questions behave | Interactive with multiple tries |
      | Marked out of        | 4                               |
      | Marks                | Show mark and max               |
      | Specific feedback    | Shown |
      | Right answer         | Shown |
    And I press "Start again with these options"

    #Select all (both) correct options
    And I click on "sat" "text"
    And I click on "jumped" "text"
    And I click on "meo" "text"
    And I click on "103" "text"
    And I click on "log3" "text"
    And I press "Check"
    And I should see "Your answer is correct."
    And I should see "Mark 4.00 out of 4.00"
