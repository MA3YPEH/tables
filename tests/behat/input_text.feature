@mod @mod_tables @_file_upload
Feature: Student input text into cells
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
    And The following "activities" exist:
      | activity    | name    | intro               | course | idnumber  | columncount | rowcount |
      | tables      | Table 1 | Table 1 description | C1     | table1    | 10          | 10       |
    And The following "tables_sheet_cells" exist:
      | tqableid | cellname | visibility |
      | table1   | A1       | all        |
      | table1   | B2       | all        |
    And The following "cells" attached to "us
    And The following "cells" attached to "user":
      | cell | user     |
      | A1   | student1 |
      | B2   | student1 |

  @javascript
  Scenario: Fill cells
    When I am on the "Table 1" "mod_tables > View" page logged in as "student1"
    And I fill "Table 1" table with text:
      | cell | text   |
      | A1   | Text 1 |
      | B2   | Text 2 |
    Then I log out

    And I am on the "Table 1" "mod_tables > View" page logged in as "teacher1"
    Then I should see "Text 1" in "textarea" "A1"
    And I should see "Text 2" in "textarea" "B2"