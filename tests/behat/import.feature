@mod @mod_quiz @qtype @qtype_wordselect @qtype_wordselect_import @javascript

Feature: Test importing Wordselect questions
  Background:
    Given the following "users" exist:
        | username |
        | teacher  |
    And the following "courses" exist:
        | fullname | shortname | category |
        | Course 1 | C1        | 0        |
    And the following "course enrolments" exist:
        | user    | course | role           |
        | teacher | C1     | editingteacher |
  @javascript @_file_upload
  Scenario: import wordselect question.
    When I am on the "Course 1" "core_question > course question import" page logged in as teacher
    And I set the field "id_format_xml" to "1"
    And I upload "question/type/wordselect/tests/fixtures/wordselect_examples.xml" file to "Import" filemanager
    And I press "id_submitbutton"
    Then I should see "Parsing questions from import file."
    And I should see "Importing 10 questions from file"
    And I press "Continue"
    # This is just the name of one of the example questions imported that will be listed
    And I should see "CatMat"
