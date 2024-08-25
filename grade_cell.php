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
 * Update cell content for mod_tables.
 *
 * @package     mod_tables
 * @copyright   2023 Mazur Egor <mazur.eh@edu.spbstu.ru>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require(__DIR__.'/../../config.php');
require_once(__DIR__.'/lib.php');

global $CFG, $PAGE, $DB, $USER;

$PAGE->set_url('/mod/tables/grade_cell.php');
$PAGE->requires->jquery();

$data = array('sheetid' => optional_param('sheet_id', 0, PARAM_INT),
    'name' => optional_param('cell_name',0, PARAM_TEXT));

$cell_id = $DB->get_record('tables_sheets_cells', $data, '*', MUST_EXIST)->id;

$data = array('userid' => optional_param('user_id', 0, PARAM_INT),
    'cellid' => $cell_id);

if($DB->record_exists('tables_cells_grade', $data)){
    $cell_grade = $DB->get_record('tables_cells_grade', $data, '*', MUST_EXIST);
    $cell_grade->grade = optional_param('grade', 0, PARAM_INT);
    $cell_grade->timemodified = time();

    $DB->update_record('tables_cells_grade', $cell_grade);
}
else{
    $data['grade'] = optional_param('grade', 0, PARAM_INT);
    $data['timecreated'] = time();

    $DB->insert_record('tables_cells_grade', $data);
}