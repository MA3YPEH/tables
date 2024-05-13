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

$PAGE->requires->js(new moodle_url($CFG->wwwroot . '/mod/tables/amd/src/attach_cells.js?v=1.8'));

$data = array(
    'tableid' => optional_param('table_id', 0, PARAM_INT),
    'userid' => optional_param('user_id', 0, PARAM_INT));

$user_cell = $DB->get_record('tables_users_cells', $data, '*', MUST_EXIST);
$attached_cells = explode(', ',$user_cell->attached_cells);
$removed_cells = array_search(optional_param('removed_cells', 0, PARAM_TEXT), $attached_cells);
array_splice($attached_cells, $removed_cells, 1);
$user_cell->attached_cells = implode(', ',$attached_cells);
$user_cell->timemodified = time();

$DB->update_record('tables_users_cells', $user_cell);

// Updating table

$DB->update_record('tables', (object)array('id' => $data['tableid'], 'timemodified' => time()));
