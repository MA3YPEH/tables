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

class mod_tables_generator extends testing_module_generator {

    public function create_instance($record = null, array $options = null) {
        global $CFG;

        require_once($CFG->dirroot.'/mod/tables/lib.php');
        $record = (object)(array)$record;

        $defaulttablesettings = array(
            'name'               => 'Tables',
            'course'              => 0,
            'columncount'           => 10,
            'rowcount'              => 10,
            'timecreated'            => time(),
            'timemodified'           => time()
        );

        foreach ($defaulttablesettings as $name => $value) {
            if (!isset($record->{$name})) {
                $record->{$name} = $value;
            }
        }

        if (isset($record->gradepass)) {
            $record->gradepass = unformat_float($record->gradepass);
        }

        return parent::create_instance($record, (array)$options);
    }

    public function cell_input_text_test(){
        global $DB, $SITE;

        $this->resetAfterTest(true);

        /** @var \mod_tables_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('mod_tables');
        $tables = $generator->create_instance(array('course' => $SITE->id, 'timecreated' => 0, 'name' => 'test name'));

        $cell_data = array(
            'tableid' => $tables->id,
            'name' => 'A1',
            'content' => "Hello world",
            'font_family' => 'Arial',
            'font_size' => 14,
            'bold' => 'bold',
            'italic' => 'italic',
            'text_align' => 'left',
            'timecreated' => time());

        $DB->insert_record('tables_sheets_cells', (object) $cell_data);

        $cell = $DB->get_record('tables_sheets_cells', array('name' => $cell_data['name'],
            'tableid' => $tables->id), '*', MUST_EXIST);

        $this->assertEquals($cell_data['content'], $cell->content);
        $this->assertEquals($cell_data['font_family'], $cell->font_family);
        $this->assertEquals($cell_data['font_size'], $cell->font_size);
        $this->assertEquals($cell_data['bold'], $cell->bold);
        $this->assertEquals($cell_data['italic'], $cell->italic);
        $this->assertEquals($cell_data['text_align'], $cell->text_align);

    }

    public function cell_grade_test(){
        global $DB, $SITE;

        $this->resetAfterTest(true);

        /** @var \mod_tables_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('mod_tables');
        $tables = $generator->create_instance(array('course' => $SITE->id, 'timecreated' => 0, 'name' => 'test name'));
        $user = $this->getDataGenerator()->create_user();

        $cell_data = array(
            'tableid' => $tables->id,
            'name' => 'A1',
            'content' => "Hello world",
            'font_family' => 'Arial',
            'font_size' => 14,
            'bold' => 'bold',
            'italic' => 'italic',
            'text_align' => 'left',
            'timecreated' => time());

        $DB->insert_record('tables_sheets_cells', (object) $cell_data);
        $cell = $DB->get_record('tables_sheets_cells', array('name' => $cell_data['name'],
            'tableid' => $tables->id), '*', MUST_EXIST);

        $grade_data = array(
            'userid' => $user->id,
            'cellid' => $cell->id,
            'grade' => 80,
            'feedback' => 'test feedback',
            'timecreated' => time()
        );

        $DB->insert_record('tables_cells_grade', (object) $grade_data);
        $grade = $DB->get_record('tables_cells_grade', array('userid' => $user->id,
            'cellid' => $cell->id), '*', MUST_EXIST);

        $this->assertEquals($cell_data['grade'], $cell->grade);
        $this->assertEquals($cell_data['feedback'], $cell->feedback);
    }
}