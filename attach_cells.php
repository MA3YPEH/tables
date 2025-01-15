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
    case 'students':{
        $data = array(
            'sheetid' => optional_param('sheet_id', 0, PARAM_INT),
            'userid' => optional_param('user_id', 0, PARAM_INT));
        $table = 'tables_users_cells';
        break;
    }
    case 'groups':{
        $data = array(
            'sheetid' => optional_param('sheet_id', 0, PARAM_INT),
            'groupid' => optional_param('user_id', 0, PARAM_INT));
        $table = 'tables_groups_cells';
        break;
    }
}

// Updating data

attach_cells($data, $table, optional_param('first_cell', 0, PARAM_TEXT), optional_param('last_cell', 0, PARAM_TEXT));

function attach_cells($data, $table, $fitst_cell, $last_cell){
    global $DB;

    $first_column = preg_replace('/[^a-zA-Z]/', '', $fitst_cell);
    $first_row = preg_replace('/[^0-9]/', '', $fitst_cell);

    $last_column = preg_replace('/[^a-zA-Z]/', '', $last_cell);
    $last_row = preg_replace('/[^0-9]/', '', $last_cell);

    $columns = get_cell_range($first_column, $last_column);
    $rows = get_cell_range($first_row, $last_row);

    $cells = array();

    foreach ($columns as $column){
        foreach ($rows as $row){
            array_push($cells, $column.$row);
        }
    }

    foreach($cells as $cell){
        $data['cellname'] = $cell;
        if(!$DB->record_exists($table, $data)){
            $data['timecreated'] = time();
            $DB->insert_record($table, $data);
        }
        if(!$DB->record_exists('tables_sheets_cells', array('sheetid' => $data['sheetid'], 'name' => $cell))){
            $DB->insert_record('tables_sheets_cells', array('sheetid' => $data['sheetid'], 'name' => $cell));
        }
    }
}