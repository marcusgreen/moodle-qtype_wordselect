@core @qtype @qtype_wordselect @_switch_window
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
  Scenario: Import example wordselect qestions 
    When I log in as "teacher"
    And I am on "Course 1" course homepage
    And I navigate to "Question bank" node in "Course administration"
    And I navigate to "Import" node in "Course administration"


