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

$PAGE->set_url('/mod/tables/create_sheet.php');
$PAGE->requires->jquery();

$update_type = optional_param('update_type', 0, PARAM_TEXT);
$data = array('tableid' => optional_param('table_id', 0, PARAM_INT));

switch ($update_type){
    case "add_sheet":{
        $sheets = $DB->get_records('tables_sheets', $data);
        $data['name'] = "".count($sheets) + 1;
        $data['timecreated'] = time();
        $DB->insert_record('tables_sheets', $data);
        break;
    }
    case "delete_sheet":{
        $data['id'] = optional_param('sheet_id', 0, PARAM_INT);
        $DB->delete_records('tables_sheets', $data);
        break;
    }
}

// Updating table
$DB->update_record('tables', (object)array('id' => optional_param('table_id', 0, PARAM_INT), 'timemodified' => time()));
