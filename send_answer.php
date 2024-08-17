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

$PAGE->set_url('/mod/tables/send_answer.php');
$PAGE->requires->jquery();

$PAGE->requires->js(new moodle_url($CFG->wwwroot. '/mod/tables/amd/src/update_data.js?v=5.5'));

$data = array (
    'sheetid' => optional_param('sheet_id', 0, PARAM_INT),
    'userid' => optional_param('user_id', null, PARAM_TEXT));

if($DB->record_exists('tables_users_cells', $data)){
    $user_data = $DB->get_record('tables_users_cells', $data);
    $user_data->attached_cells = null;
    $user_data->timemodified = time();
    $DB->update_record('tables_users_cells', $user_data);
}

$DB->update_record('tables', (object)array('id' => optional_param('table_id', null, PARAM_INT), 'timemodified' => time()));