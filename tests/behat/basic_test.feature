@core @qtype @qtype_wordselect @qtype_wordselect_basic @_switch_windown
Feature: Test the basic functionality of wordselect type
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
    | Question name               | Word-Select-001                        |
    | Introduction                | Select the verbs in the following text |
    | Question text               | The cat [sat] and the cow [jumped]     |
    | Incorrect selection penalty | 100%                                   |
    | General feedback            | This is general feedback               |
    | Hint 1                      | First hint                             |
    | Hint 2                      | Second hint                            |
    Then I should see "Word-Select-001"

  # Preview it.

  #When I choose "Preview" action for "Word-Select-001" in the question bank
    And I follow "Preview"

    And I switch to "questionpreview" window

  #################################################
  #Adaptive Mode
  #################################################
    And I set the following fields to these values:
    | How questions behave | Adaptive mode     |
    | Marked out of        | 2                 |
    | Marks                | Show mark and max |
    | Specific feedback    | Shown             |
    | Right answer         | Shown             |
    And I press "Start again with these options"

  #User does not select any word and press button Check
    And I press "Check"
    And I should see "Please select an answer."

  #Select all (both) correct options
    And I click on "sat" "text"
    And I click on "jumped" "text"
    And I press "Check"
    And I should see "Your answer is correct."
    And I should see "Mark 2.00 out of 2.00"
  #And I wait "15" seconds
    And I should see "You have selected 2 correct items out of 2"

  #################################################
  #Interactive with multiple tries
  #################################################
    And I set the following fields to these values:
    | How questions behave | Interactive with multiple tries |
    | Marked out of        | 2                               |
    | Marks                | Show mark and max               |
    | Specific feedback    | Shown                           |
    | Right answer         | Shown                           |
    And I press "Start again with these options"

  #User does not select any word and press button Check
    And I press "Check"
    And I should see "Please select an answer."

  #Select all (both) correct options
    And I click on "sat" "text"
    And I click on "jumped" "text"
    And I press "Check"
    And I should see "Your answer is correct."
    And I should see "Mark 2.00 out of 2.00"

  #Select one incorrect option on the first attempt
  #and all/both correct options on the second attempt
  ################################################
  #first attempt
    And I press "Start again with these options"
    And I click on "sat" "text"
    And I press "Check"
    And I should see "Your answer is partially correct."

  ################################################
  #second attempt
    And I press "Try again"
  #sat should remain selected so no need to select again
    And I click on "jumped" "text"
    And I press "Check"
    And I should see "Your answer is correct."
  #second attempt 33% penalty for this being a second attempt
    And I should see "Mark 1.67 out of 2.00"

  ##################################################
  # Immediate Feedback behaviour
    And I set the following fields to these values:
    | How questions behave | Immediate feedback |
    | Marked out of        | 2                  |
    | Marks                | Show mark and max  |
    | Specific feedback    | Shown              |
    | Right answer         | Shown              |

    And I press "Start again with these options"
    And I click on "sat" "text"
    And I click on "jumped" "text"
    And I press "Check"
    And I should see "Your answer is correct."
    And I should see "Mark 2.00 out of 2.00"
    And I wait "2" seconds

    And I press "Start again with these options"
    And I click on "sat" "text"
    And I press "Check"
    And I should see "Your answer is partially correct."
    And I should see "Mark 1.00 out of 2.00"
    And I wait "2" seconds

  ##################################################
  # Deferred Feedback behaviour
    And I set the following fields to these values:
    | How questions behave | Deferred feedback |
    | Marked out of        | 2                 |
    | Marks                | Show mark and max |
    | Specific feedback    | Shown             |
    | Right answer         | Shown             |

    And I press "Start again with these options"
    And I click on "sat" "text"
    And I click on "jumped" "text"
    And I press "Submit and finish"
    And I should see "Your answer is correct."
    And I should see "Mark 2.00 out of 2.00"
    And I wait "5" seconds

    And I press "Start again with these options"
    And I click on "sat" "text"
    And I press "Submit and finish"
    And I should see "Your answer is partially correct."
    And I should see "Mark 1.00 out of 2.00"
    And I wait "5" seconds

    And I press "Start again with these options"
    And I click on "sat" "text"
    And I click on "cow" "text"
    And I click on "jumped" "text"

    And I press "Submit"
    And I should see "Your answer is partially correct."
    And I should see "Mark 1.00 out of 2.00"
    And I wait "5" seconds

  #This doesn't work for some reason and needs fixing
  #And I press "Start again with these options"
  #And I click on "cat" "text"
  #And I click on "mat" "text"
  #And I click on "cow" "text"

  #And I press "Submit and finish"
  #And I should see "Your answer is incorrect."
  #And I should see "Mark 0.00 out of 2.00"
  #And I wait "2" seconds

    And I switch to the main window
    And I log out

  @javascript
  #Feature: Change penalty %age for incorrect selections
  @qtype_wordselect_penalty
  Scenario: Create question and test wordpenalty.
    Given I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I navigate to "Questions" in current page administration

  # Create a new question.
    And I add a "Word Select" question filling the form with:
    | Question name               | Word-Select-Penalty                            |
    | Introduction                | Select the verbs in the following text         |
    | Question text               | The wordpenalty cat [sat] and the cow [jumped] |
    | Incorrect selection penalty | 0.5                                            |
    | General feedback            | This is general feedback                       |

    Then I should see "Word-Select-Penalty"

  # Preview it.
  #When I choose "Preview" action for "Word-Select-Penalty " in the question bank
    And I follow "Preview"
    And I switch to "questionpreview" window

  ##########################################################
  #Test Incorrect selection penalty reduced from the default 100%
  ##########################################################
    And I set the following fields to these values:
    | How questions behave | Interactive with multiple tries |
    | Marked out of        | 2                               |
    | Marks                | Show mark and max               |
    | Specific feedback    | Shown                           |
    | Right answer         | Shown                           |
    And I press "Start again with these options"

  #Select all (both) correct options and  an incorrect
  #option (cow)
    And I click on "sat" "text"
    And I click on "cow" "text"
    And I click on "jumped" "text"
    And I press "Check"

    And I should see "Your answer is partially correct."
  # It is 1.5 because the penalty for cow is only .5 not the default 1
    And I should see "Mark 1.50 out of 2.00"
    And I wait "0" seconds
