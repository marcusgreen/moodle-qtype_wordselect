@qtype @qtype_wordselect @_switch_window
Feature: Test all the basic functionality of this question type
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
    And I follow "Course 1"
    And I navigate to "Question bank" node in "Course administration"

    # Create a new question.
    And I add a "Word Select" question filling the form with:
      | Question name             | Word-Select-001                   |
      | Introduction              | Select the verb in the following text  |
      | Question text             | The cat [sat] on the mat.     |
      | General feedback          | The cat sat on the mat.       |
      | Hint 1                    | First hint                    |
      | Hint 2                    | Second hint                   |
    Then I should see "Word-Select-001"

    # Preview it.
    When I click on "Preview" "link" in the "Word-Select-001" "table_row"
    And I switch to "questionpreview" window

   
    # Set display and behaviour options
    And I set the following fields to these values:
      | How questions behave | Interactive with multiple tries |
      | Marked out of        | 1                               |
      | Marks                | Show mark and max               |
      | Specific feedback    | Shown |
      | Right answer         | Shown |
    And I press "Start again with these options"
    And I click on "sat" "text" 
    And I press "Check"      
    And I should see "Your answer is correct."
    And I should see "Mark 1.00 out of 1.00"
