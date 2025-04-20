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

require(__DIR__ . '/../../config.php');
require_once(__DIR__ . '/lib.php');

global $CFG, $PAGE, $DB, $USER;

$PAGE->set_url('/mod/tables/update_cell_focus.php');
$PAGE->requires->jquery();

$PAGE->requires->js(new moodle_url($CFG->wwwroot . '/mod/tables/amd/src/update_data.js?v=8.1'));

$update_type = optional_param('update_type', null, PARAM_TEXT);

$user_data = array(
    'tableid' => optional_param('table_id', 0, PARAM_INT),
    'active_sheet' => optional_param('sheet_id', 0, PARAM_INT),
    'userid' => $USER->id);

// Updating cell

if($update_type == 'focusin'){
    $user_data['focused_cell'] = optional_param('cell_id', null, PARAM_TEXT);
    if($DB->record_exists('tables_users_focus', $user_data)){
        $user_cell = $DB->get_record('tables_users_focus', $user_data, '*', MUST_EXIST);
        $DB->delete_records('tables_users_focus', $user_cell);
    }
    else{
        $user_data['timecreated'] = time();
        $DB->insert_record('tables_users_focus', $user_data);
    }
}
else if($update_type == 'focusout'){
    $user_data['focused_cell'] = optional_param('cell_id', null, PARAM_TEXT);
    $DB->delete_records('tables_users_focus', $user_data);
}
else if($update_type == 'allfocusout'){
    $DB->delete_records('tables_users_focus', $user_data);
}