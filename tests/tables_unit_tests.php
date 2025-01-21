<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot.'/mod/tables/lib.php');

class mod_tables_unit_tests extends advanced_testcase {
    public function test_get_column_width()
    {
        global $DB;

        $this->resetAfterTest(true);

        $data = array(
            'name' => "A",
            'sheetid' => 0,
            'width' => 300);

       $DB->insert_record('tables_sheets_columns', $data);

        $test = get_column_width($data['name'], $data['sheetid']);

       $this->assertEquals($data['width'], $test);
    }
    public function test_get_row_height()
    {
        global $DB;

        $this->resetAfterTest(true);

        $data = array(
            'name' => "1",
            'sheetid' => 0,
            'height' => 300);

        $DB->insert_record('tables_sheets_rows', $data);

        $test = get_column_width($data['name'], $data['sheetid']);

        $this->assertEquals($data['width'], $test);
    }

    public function test_get_cell_font_family(){
        global $DB;

        $this->resetAfterTest(true);

        $data = array(
            'name' => "A1",
            'sheetid' => 0,
            'font_family' => 300);

        $DB->insert_record('tables_sheets_cells', $data);

        $test = get_cell_font_family($data['name'], $data['sheetid']);

        $this->assertEquals($data['font_family'], $test);
    }

    public function test_get_cell_font_size(){
        global $DB;

        $this->resetAfterTest(true);

        $data = array(
            'name' => "A1",
            'sheetid' => 0,
            'font_size' => 300);

        $DB->insert_record('tables_sheets_cells', $data);

        $test = get_cell_font_size($data['name'], $data['sheetid']);

        $this->assertEquals($data['font_size'], $test);
    }

    public function test_get_cell_bold(){
        global $DB;

        $this->resetAfterTest(true);

        $data = array(
            'name' => "A1",
            'sheetid' => 0,
            'bold' => 300);

        $DB->insert_record('tables_sheets_cells', $data);

        $test = get_cell_bold($data['name'], $data['sheetid']);

        $this->assertEquals($data['bold'], $test);
    }

    public function test_get_cell_italic(){
        global $DB;

        $this->resetAfterTest(true);

        $data = array(
            'name' => "A1",
            'sheetid' => 0,
            'italic' => 300);

        $DB->insert_record('tables_sheets_cells', $data);

        $test = get_cell_italic($data['name'], $data['sheetid']);

        $this->assertEquals($data['italic'], $test);
    }

    public function test_get_cell_underline(){
        global $DB;

        $this->resetAfterTest(true);

        $data = array(
            'name' => "A1",
            'sheetid' => 0,
            'underline' => 300);

        $DB->insert_record('tables_sheets_cells', $data);

        $test = get_cell_underline($data['name'], $data['sheetid']);

        $this->assertEquals($data['underline'], $test);
    }

    public function test_get_cell_align(){
        global $DB;

        $this->resetAfterTest(true);

        $data = array(
            'name' => "A1",
            'sheetid' => 0,
            'text_align' => 300);

        $DB->insert_record('tables_sheets_cells', $data);

        $test = get_cell_align($data['name'], $data['sheetid']);

        $this->assertEquals($data['text_align'], $test);
    }

    public function test_get_cell_range() {
        $data_first_cell = 'A';
        $data_last_cell = 'B';
        $test = get_cell_range($data_first_cell, $data_last_cell);
        $this->assertEquals($data_first_cell, $test[0]);
        $this->assertEquals($data_first_cell, $test[1]);

        $data_first_cell = 'B';
        $data_last_cell = 'A';
        $test = get_cell_range($data_first_cell, $data_last_cell);
        $this->assertEquals($data_first_cell, $test[1]);
        $this->assertEquals($data_first_cell, $test[0]);

        $data_first_cell = '1';
        $data_last_cell = '2';
        $test = get_cell_range($data_first_cell, $data_last_cell);
        $this->assertEquals($data_first_cell, $test[0]);
        $this->assertEquals($data_first_cell, $test[1]);

        $data_first_cell = '2';
        $data_last_cell = '1';
        $test = get_cell_range($data_first_cell, $data_last_cell);
        $this->assertEquals($data_first_cell, $test[1]);
        $this->assertEquals($data_first_cell, $test[0]);
    }

    public function test_generate_column_name(){
        $data = 1;
        $test = generate_column_name($data);
        $this->assertEquals("A", $test);

        $data = 2;
        $test = generate_column_name($data);
        $this->assertEquals("B", $test);

        $data = 27;
        $test = generate_column_name($data);
        $this->assertEquals("AA", $test);

        $data = 28;
        $test = generate_column_name($data);
        $this->assertEquals("AB", $test);
    }

    public function test_attach_cells(){
        global $DB;

        $this->resetAfterTest(true);

        $data = array(
            'sheetid' => 0,
            'groupid' => 0);
        $table = 'tables_groups_cells';
        $first_cell = "A1";
        $last_cell = "B2";

        attach_cells($data, $table, $first_cell, $last_cell);

        $test = $DB->record_exists($table, array('sheetid' => $data['sheetid'],
            'groupid' => $data['groupid'],
            'cellname' => "A1"));

        $this->assertEquals($test, true);

        $test = $DB->record_exists($table, array('sheetid' => $data['sheetid'],
            'groupid' => $data['groupid'],
            'cellname' => "A2"));

        $this->assertEquals($test, true);

        $test = $DB->record_exists($table, array('sheetid' => $data['sheetid'],
            'groupid' => $data['groupid'],
            'cellname' => "B1"));

        $this->assertEquals($test, true);

        $test = $DB->record_exists($table, array('sheetid' => $data['sheetid'],
            'groupid' => $data['groupid'],
            'cellname' => "B2"));

        $this->assertEquals($test, true);

        $data = array(
            'sheetid' => 0,
            'userid' => 0);
        $table = 'tables_users_cells';
        $first_cell = "C1";
        $last_cell = "D2";

        attach_cells($data, $table, $first_cell, $last_cell);

        $test = $DB->record_exists($table, array('sheetid' => $data['sheetid'],
            'userid' => $data['userid'],
            'cellname' => "C1"));

        $this->assertEquals($test, true);

        $test = $DB->record_exists($table, array('sheetid' => $data['sheetid'],
            'userid' => $data['userid'],
            'cellname' => "C2"));

        $this->assertEquals($test, true);

        $test = $DB->record_exists($table, array('sheetid' => $data['sheetid'],
            'userid' => $data['userid'],
            'cellname' => "D1"));

        $this->assertEquals($test, true);

        $test = $DB->record_exists($table, array('sheetid' => $data['sheetid'],
            'userid' => $data['userid'],
            'cellname' => "D2"));

        $this->assertEquals($test, true);
    }

    public function test_grade_cell(){
        global $DB;

        $this->resetAfterTest(true);

        $data = array('userid' => 0,
            'cellid' => 0);

        $grade = 100;
        $feedback = "Test feedback";

        grade_cell($data, $grade, $feedback);
        if($DB->record_exists("tables_cells_grade", $data)){
            $test = $DB->get_record("tables_cells_grade", $data, '*', MUST_EXIST);
        }
        else{
            $test = "Not exist";
        }

        $this->assertEquals($test->grade, $grade);
        $this->assertEquals($test->feedback, $feedback);
    }

    public function test_update_cell(){
        global $DB;

        $this->resetAfterTest(true);

        $cell_data = array (
            'sheetid' => 0,
            'name' => "A1");

        $content = "Test content";
        $visibility = "user";

        update_cell($cell_data, $content, $visibility);

        if($DB->record_exists("tables_sheets_cells", $cell_data)){
            $test = $DB->get_record('tables_sheets_cells', $cell_data, '*', MUST_EXIST);
        }
        else{
            $test = "Not exist";
        }
        $this->assertEquals($test->content, $content);
        $this->assertEquals($test->visibility, $visibility);
    }

    public function test_create_sheet(){
        global $DB;

        $update_type = "add_sheet";
        $data = array(
            'tableid' => 0,
            'name' => "0"
        );

        create_sheet($data, $update_type);

        $test = $DB->record_exists("tables_sheets", $data);

        $this->assertEquals($test, true);

        $update_type = "delete_sheet";

        create_sheet($data, $update_type);

        $test = $DB->record_exists("tables_sheets", $data);

        $this->assertEquals($test, false);
    }
}