@mod @mod_tables @_file_upload
Feature: Teacher change cell visibility
  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email                |
      | teacher1 | Teacher   | 1        | teacher1@example.com |
      | student1 | Student   | 1        | student1@example.com |
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1        | 0        |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |
      | student1 | C1     | student        |
    And the following "groups" exist:
      | name    | course | idnumber |
      | Group 1 | C1     | G1       |
    And the "student1" attached to following group:
      | group   | user     |
      | Group 1 | student1 |
    And The following "activities" exist:
      | activity    | name    | intro               | course | idnumber  |
      | tables      | Table 1 | Table 1 description | C1     | table1    |
      | quiz        | Quiz 1  | Quiz 1 description  | C1     | quiz1     |
    And the following "question categories" exist:
      | contextlevel | reference | name           |
      | Course       | C1        | Test questions |
    And the following "questions" exist:
      | questioncategory | qtype     | name | questiontext   | questionsummary | rightanswer | maxfraction |
      | Test questions   | truefalse | TF1  | First question | TF1 description | false       | 1           |
    And quiz "Test quiz name" contains the following questions:
      | question | page |
      | TF1      | 1    |
    And user "student1" has attempted "Test quiz name" with responses:
      | slot | response |
      |   1  | True     |

  @javascript
  Scenario: Change visibility
    When I am on the "Table 1" "mod_tables > View" page logged in as "teacher1"
    And I click on "load_from_activity" "button"
    Then I am on the "Table 1" "mod_tables > upload_from_activity" page
    And I select "quiz" in "activity_type_selector"
    And I select "Quiz 1" in "activity_selector"
    Then I am n the "Table 1" "mod_tables > View" page
    And I should see "Group 1" in "A1" cell "textarea"
    And I should see "student1" in "B1" cell "textarea"
    And I should see "Попытка 1" in "C1" cell "textarea"
    And I should see "Вариант 1" in "D1" cell "textarea"
    And I should see "TF1" in "E1" cell "textarea"
    And I should see "TF1 description" in "F1" cell "textarea"
    And I should see "false" in "G1" cell "textarea"
    And I should see "True" in "H1" cell "textarea"
    And I should see "1" in "I1" cell "textarea"
    And I should see "0" in "J1" cell "textarea"
