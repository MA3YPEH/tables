<?php
// This file is part of Moodle - https://moodle.org/
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
// along with Moodle.  If not, see <https://www.gnu.org/licenses/>.

/**
 * Update cell for mod_tables.
 *
 * @package     mod_tables
 * @copyright   2023 Mazur Egor <mazur.eh@edu.spbstu.ru>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require(__DIR__.'/../../config.php');
require_once(__DIR__.'/lib.php');

$PAGE->set_url('/mod/tables/updatecell.php');
$PAGE->requires->jquery();

$PAGE->requires->js(new moodle_url($CFG->wwwroot. '/mod/tables/updatecell.js'));

$time = time();

$cell_data = array (
    'tableid' => optional_param('table_id', 0, PARAM_INT),
    'name' => optional_param('cell_id', 0, PARAM_TEXT),
    'content' => optional_param('cell_content', 0, PARAM_TEXT),
    'timecreated' => $time
);

if($DB->record_exists('tables_cells', array('name' => $cell_data['name'],
    'tableid' => $cell_data['tableid']))){

    $cell = $DB->get_record('tables_cells', array('name' => $cell_data['name'],
        'tableid' => $cell_data['tableid']), '*', MUST_EXIST);

    $cell -> content = $cell_data['content'];
    $cell -> timemodified = $time;
    $DB->update_record('tables_cells', $cell);

    $DB->update_record('tables', array('id' => $cell_data['tableid'], 'timemodified' => $time));
}
else{
    $DB->insert_record('tables_cells', $cell_data);

    $DB->update_record('tables', array('id' => $cell_data['tableid'], 'timemodified' => $time));
}


