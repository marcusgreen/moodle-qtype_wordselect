@core @qtype @qtype_wordselect @qtype_wordselect_review_options @_switch_windown @wsel_d_feedback
Feature: Test wordselect showiing of correctness with direct feedback
    In order to inform students whether each response was correct and which word
    was the right answer, apply classes.  Classes with colours are applied with supporting titles.
    The titles are not tested.

    This tests how the qtype works with the Review options
    in Quiz editing page, but does it through question preview.

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
  Scenario: Show correctness when using deferred feedback.
    Given I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I navigate to "Questions" in current page administration

  # Create a new question.
    And I add a "Word Select" question filling the form with:
        | Question name               | Word-Select-001                        |
        | Introduction                | Select the verbs in the following text |
        | Question text               | The cat [sat] and the cow [jumped]     |
        | Incorrect selection penalty | 100%                                   |
        | General feedback            | This is general feedback               |
    Then I should see "Word-Select-001"

  # Preview it.

    When I choose "Preview" action for "Word-Select-001" in the question bank
    And I switch to "questionpreview" window

  #################################################
  #Interactive with multiple tries (no hints set up)
  #################################################
    And I set the following fields to these values:
        | How questions behave | Deferred feedback |
        | Marked out of        | 2                 |
        | Marks                | Show mark and max |
        | Specific feedback    | Shown             |
        | Right answer         | Shown             |
        | Whether correct      | Shown             |

    And I press "Start again with these options"
    #Select all (both) correct options
    And I click on "sat" "text"
    And I click on "jumped" "text"
    And I press "Submit and finish"

    Then the "class" attribute of "//span[text()='sat']" "xpath_element" should contain "correctresponse"
    Then the "class" attribute of "//span[text()='jumped']" "xpath_element" should contain "correctresponse"

    And I press "Start again with these options"
    #Select two incorrect options and show which ones should have been selected
    And I click on "The" "text"
    And I click on "cow" "text"
    And I press "Submit and finish"
    Then the "class" attribute of "//span[text()='The']" "xpath_element" should contain "incorrect"
    Then the "class" attribute of "//span[text()='cow']" "xpath_element" should contain "incorrect"
    Then the "class" attribute of "//span[text()='sat']" "xpath_element" should contain "correct"
    Then the "class" attribute of "//span[text()='jumped']" "xpath_element" should contain "correct"

    # Dont show the right answers or if the selected options were correct.
    And I set the following fields to these values:
        | How questions behave | Interactive with multiple tries |
        | Marked out of        | 2                               |
        | Marks                | Show mark and max               |
        | Specific feedback    | Shown                           |
        | Right answer         | Not shown                       |
        | Whether correct      | Not shown                       |

    And I press "Start again with these options"

    #Select two incorrect options and show which ones should have been selected
    And I click on "The" "text"
    And I click on "cow" "text"
    And I press "Submit and finish"
    Then the "class" attribute of "//span[text()='The']" "xpath_element" should not contain "incorrect"
    Then the "class" attribute of "//span[text()='cow']" "xpath_element" should not contain "incorrect"
    # Check all text as specific words don't have a class attribute to test
    Then the "class" attribute of "//div[contains(@class, 'qtext')]" "xpath_element" should not contain "correct"

    And I press "Start again with these options"
    #Select all (both) correct options
    And I click on "sat" "text"
    And I click on "jumped" "text"
    And I press "Submit and finish"
    # Dont show which responses were correct
    Then the "class" attribute of "//span[text()='sat']" "xpath_element" should not contain "correctresponse"
    Then the "class" attribute of "//span[text()='jumped']" "xpath_element" should not contain "correctresponse"
    Then the "class" attribute of "//span[text()='sat']" "xpath_element" should contain "selected"
    Then the "class" attribute of "//span[text()='jumped']" "xpath_element" should contain "selected"
