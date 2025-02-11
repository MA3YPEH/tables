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

$PAGE->set_url('/mod/tables/remove_attached_cells.php');
$PAGE->requires->jquery();
$PAGE->requires->js(new moodle_url($CFG->wwwroot . '/mod/tables/amd/src/attach_cells.js?v=3.2'));

$update_type = optional_param('update_type', 0, PARAM_TEXT);
$cell_id = optional_param('cell_id', 0, PARAM_INT);

switch (optional_param('attached_to', '0', PARAM_TEXT)){
    case 'student':{
        if($update_type == 'delete_all_cells'){
            $sheet = optional_param('sheet_id', 0, PARAM_INT);
            $user = optional_param('user_id', 0, PARAM_INT);
            $DB->delete_records('tables_users_cells', array('sheetid' => $sheet, 'userid' => $user));
        }
        else{
            $DB->delete_records('tables_users_cells', array('id' => $cell_id));
        }

        break;
    }
    case 'group':{
        if($update_type == 'delete_all_cells'){
            $sheet = optional_param('sheet_id', 0, PARAM_INT);
            $user = optional_param('user_id', 0, PARAM_INT);
            $DB->delete_records('tables_groups_cells', array('sheetid' => $sheet, 'groupid' => $user));
        }
        else{
            $DB->delete_records('tables_groups_cells', array('id' => $cell_id));
        }
        break;
    }
}
