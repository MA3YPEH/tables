@mod @mod_tables @_file_upload
Feature: Teacher attach cells to student
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

  @javascript
  Scenario: Attach cells
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