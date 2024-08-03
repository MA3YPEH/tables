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
 * Update cell size content for mod_tables.
 *
 * @package     mod_tables
 * @copyright   2023 Mazur Egor <mazur.eh@edu.spbstu.ru>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require(__DIR__.'/../../config.php');
require_once(__DIR__.'/lib.php');

global $CFG, $PAGE, $DB, $USER;

$PAGE->set_url('/mod/tables/attach_cells.php');
$PAGE->requires->jquery();

$PAGE->requires->js(new moodle_url($CFG->wwwroot . '/mod/tables/amd/src/attach_cells.js?v=2.5'));

$update_type = optional_param('update_type', null, PARAM_TEXT);

switch($update_type){
    case 's':{
        $data = array(
            'sheetid' => optional_param('sheet_id', 0, PARAM_INT),
            'userid' => optional_param('user_id', 0, PARAM_INT));
        $table = 'tables_users_cells';
        break;
    }
    case 'g':{
        $data = array(
            'sheetid' => optional_param('sheet_id', 0, PARAM_INT),
            'groupid' => optional_param('user_id', 0, PARAM_INT));
        $table = 'tables_groups_cells';
        break;
    }
}

// Updating data

if($DB->record_exists($table, $data)){
    $cell = $DB->get_record($table, $data, '*', MUST_EXIST);
    if($cell->attached_cells != null){
        $attached_cells = explode(", ", $cell->attached_cells);

        $cells_to_attach = optional_param('attach', null, PARAM_TEXT);

        if(!isAttached($attached_cells, $cells_to_attach)){
            array_push($attached_cells, $cells_to_attach);
            $cell->attached_cells = implode(', ', $attached_cells);

            $cell->timemodified = time();

            $DB->update_record($table, $cell);
        }
    }
    else{
        $cell->attached_cells = optional_param('attach', null, PARAM_TEXT);

        $cell->timemodified = time();

        $DB->update_record($table, $cell);
    }
}
else{
    $data['timecreated'] = time();
    $data['attached_cells'] = optional_param('attach', null, PARAM_TEXT);
    $DB->insert_record($table, $data);
}

// Updating table
$DB->update_record('tables_sheets', (object)array('id' => $data['sheetid'], 'timemodified' => time()));
$DB->update_record('tables', (object)array('id' => optional_param('table_id', 0, PARAM_INT), 'timemodified' => time()));
