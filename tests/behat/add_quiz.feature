@mod @mod_quiz @javascript @qtype @qtype_wordselect @qtype_wordselect_add_quiz @core @_switch_window

Feature: Add a wordselect quiz
    In order to evaluate students
    As a teacher I need to create a quiz with wordselect questions
  @javascript
  Scenario: Add and configure small quiz and perform an attempt as a student with Javascript enabled
        Background:

    Given the following "users" exist:
        | username | firstname | lastname | email                |
        | teacher1 | Terry1    | Teacher1 | teacher1@example.com |
        | student1 | Sam1      | Student1 | student1@example.com |
    And the following "courses" exist:
        | fullname | shortname | category |
        | Course 1 | C1        | 0        |
    And the following "course enrolments" exist:
        | user     | course | role           |
        | teacher1 | C1     | editingteacher |
        | student1 | C1     | student        |
    And the following "question categories" exist:
        | contextlevel | reference | name           |
        | Course       | C1        | Test questions |

    And the following "questions" exist:
        | questioncategory | qtype      | name            | questiontext                     | generalfeedback           |
        | Test questions   | wordselect | First question  | The [bigcat] sat on the mat      | General feedback cat mat  |
        | Test questions   | wordselect | Second question | The [cow] jumped over the [moon] | General feedback cow moon |

    And the following "activity" exists:
        | activity           | quiz                                                 |
        | course             | C1                                                   |
        | name               | Wordselect single page quiz                          |
        | description        | Test Wordselect with more than one question per page |
        | idnumber           | 0001                                                 |
        | section            | 0                                                    |
        | preferredbehaviour | interactive                                          |

    And quiz "Wordselect single page quiz" contains the following questions:
        | question        | page | requireprevious |
        | First question  | 1    | 0               |
        | Second question | 1    | 0               |

    And I log in as "student1"
    And I am on "Course 1" course homepage
    And I follow "Wordselect single page quiz"
    And I press "Attempt quiz"
    Then I should see "Question 1"

    And I click on "bigcat" "text"
    And I click on "cow" "text"

    And I press "Finish attempt"
    And I press "Submit all and finish"
