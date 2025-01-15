@mod @mod_tables @_file_upload
Feature: Teacher change cell parameters
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
    And The following "cells" attached to "us
    And "Table 1" "cells" contains the following "text":
      | cell | text   |
      | A1   | Text 1 |

  @javascript
  Scenario: Change cell font
    When I am on the "Table 1" "mod_tables > View" page logged in as "teacher1"
    And I click on "A1" "textarea"
    Then I select "font_family" in "select_font_family" "input"
    And I should see "font_family" "Text 1" in "A1" "textarea
    Then I click on "bold" "button"
    And I should see "bold" "Text 1" in "A1" "textarea
    Then I click on "italic" "button"
    And I should see "italic" "Text 1" in "A1" "textarea
    Then I click on "underline" "button"
    And I should see "underline" "Text 1" in "A1" "textarea
    Then I select "font_size" in "select_font_size" "input"
    And I should see "font_size" "Text 1" in "A1" "textarea
