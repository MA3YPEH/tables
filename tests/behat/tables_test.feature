@mod @mod_tables @_file_upload
Feature: tests

  @javascript
  Scenario: Add a table and a discussion attaching files
    Given the following "users" exist:
      | username | firstname | lastname | email |
      | teacher1 | Teacher | 1 | teacher1@example.com |
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1 | 0 |
    And the following "course enrolments" exist:
      | user | course | role |
      | teacher1 | C1 | editingteacher |
    And I log in as "teacher1"
    And I am on "Course 1" course homepage with editing mode on
    And I add a "Tables" to section "1" and I fill the form with:
      | Table name | Test table name |
      | Description | Test table description |
      | Columns     | Test table columns     |
      | Rows     | Test table rows     |
    Then I should see table with "Columns" columns and "Rows" rows

  @javascript
  Scenario: Fill table cell and test collaborative
    Given the following "users" exist:
      | username | firstname | lastname | email |
      | teacher1 | Teacher | 1 | teacher1@example.com |
      | student1 | Student | 1 | student1@example.com |
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1 | 0 |
    And the following "course enrolments" exist:
      | user | course | role |
      | teacher1 | C1 | editingteacher |
      | student1 | C1 | student |
    And the following "Tables" exist:
      | Table name | Test table name |
      | Rows | 10 |
      | Columns | 10 |
    And I log in as "teacher1"
    And I fill "cell" with "text"
    And "student1" should see "text" in "cell"
