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
      | activity    | name    | intro               | course | idnumber  | columncount | rowcount |
      | tables      | Table 1 | Table 1 description | C1     | table1    | 10          | 10       |
    And The following "tables_sheet_cells" exist:
      | tqableid | cellname | visibility |
      | table1   | A1       | all        |
    And The following "cells" attached to "user":
      | cell | user     |
      | A1   | student1 |
    And The following "cells" attached to "group":
      | cell | group   |
      | A1   | Group 1 |
    And The following "cells" contains the following "text":
      | cell | text   |
      | A1   | Text 1 |

  @javascript
  Scenario: Change visibility
    When I am on the "Table 1" "mod_tables > Grades" page logged in as "student1"
    And I should see "Text 1" in "A1" cell "textarea"
    Then I log out

    Then I am on the "Table 1" "mod_tables > View" page logged in as "teacher1"
    And I click on "A1" cell "textarea"
    And I set "teacher" "option" into visibility "select" area
    Then I log out

    Then I am on the "Table 1" "mod_tables > Grades" page logged in as "student1"
    And I should not see "Text 1" in "A1" cell "textarea"
    Then I log out

    Then I am on the "Table 1" "mod_tables > View" page logged in as "teacher1"
    And I click on "A1" cell "textarea"
    And I set "user" "option" into visibility "select" area
    Then I log out

    Then I am on the "Table 1" "mod_tables > Grades" page logged in as "student1"
    And I should see "Text 1" in "A1" cell "textarea"
    Then I log out

    Then I am on the "Table 1" "mod_tables > View" page logged in as "teacher1"
    And I click on "A1" cell "textarea"
    And I set "group" "option" into visibility "select" area
    Then I log out

    Then I am on the "Table 1" "mod_tables > Grades" page logged in as "student1"
    And I should see "Text 1" in "A1" cell "textarea"
    Then I log out

    And I am on the "Table 1" "mod_tables > Grades" page logged in as "student1"
    Then I should see "100" in "grade" field in "A1" block
    And I should see "0" in "grade" field in "A1" block
    And I should see "50%" in "activity complete" field