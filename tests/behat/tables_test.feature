@mod @mod_tables @_file_upload
Feature: Create a table

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
    And I log in as "teacher1"
    And I am on "Course 1" course homepage with editing mode on
    When I add a "Tables" to section "1" and I fill the form with:
      | Table name       | Test table name        |
      | Description      | Test table description |
      | Columncount      | Test table columns     |
      | Rowcount         | Test table rows        |

  @javascript
  Scenario: Grade cells
    Given The following "activities" exist:
      | activity    | name    | intro               | course | idnumber  | columncount | rowcount |
      | tables      | Table 1 | Table 1 description | C1     | table1    | 10          | 10       |
    When I am on the "Table 1" "mod_tables > View" page logged in as "teacher1"
    Then I click on "attach_to_user" "button"
    And I select "student1" "checkbox"
    And I click on "A1" "textarea"
    And I click on "B2" "textarea"
    And I click on "SubmitAttach" "button"
    Then I log out

    And I am on the "Table 1" "mod_tables > Grades" page logged in as "student1"
    Then I should see "A1" "textarea" has no "disabled" attribute
    Then I should see "A2" "textarea" has no "disabled" attribute
    Then I should see "B1" "textarea" has no "disabled" attribute
    Then I should see "B2" "textarea" has no "disabled" attribute

  @javascript
  Scenario: Fill table
    Given The following "activities" exist:
      | activity    | name    | intro               | course | idnumber  | columncount | rowcount |
      | tables      | Table 1 | Table 1 description | C1     | table1    | 10          | 10       |
    And The following "cells" attached to "user":
      | cell | user     |
      | A1   | student1 |
      | B2   | student1 |
    When I am on the "Table 1" "mod_tables > View" page logged in as "student1"
    And I fill "Table 1" table with text:
      | cell | text   |
      | A1   | Text 1 |
      | B2   | Text 2 |
    Then I log out

    And I am on the "Table 1" "mod_tables > View" page logged in as "teacher1"
    Then I should see "Text 1" in "textarea" "A1"
    And I should see "Text 2" in "textarea" "B2"

  @javascript
  Scenario: Grade cells
    Given The following "activities" exist:
      | activity    | name    | intro               | course | idnumber  | columncount | rowcount |
      | tables      | Table 1 | Table 1 description | C1     | table1    | 10          | 10       |
    And The following "cells" attached to "user":
      | cell | user     |
      | A1   | student1 |
      | B2   | student1 |
    And "Table 1" "cells" contains the following "text":
      | cell | text   |
      | A1   | Text 1 |
      | B2   | Text 2 |
    When I am on the "Table 1" "mod_tables > View" page logged in as "teacher1"
    Then I click on "A1" "textarea"
    And I fill "grade" field with "100"
    And I click on "Send" "button"
    Then I click on "B2" "textarea"
    And I fill "grade" field with "0"
    And I click on "Send" "button"
    Then I log out

    And I am on the "Table 1" "mod_tables > Grades" page logged in as "student1"
    Then I should see "100" in "grade" field in "A1" block
    And I should see "0" in "grade" field in "A1" block
    And I should see "50%" in "activity complete" field


