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

$PAGE->set_url('/mod/tables/update_cell_size.php');
$PAGE->requires->jquery();

$PAGE->requires->js(new moodle_url($CFG->wwwroot. '/mod/tables/amd/src/update_data.js?v=3.6'));

$data = array (
    'tableid' => optional_param('table_id', 0, PARAM_INT),
    'name' => optional_param('name', null, PARAM_TEXT));

switch(optional_param('update_type', null, PARAM_TEXT)){
    case 'resize_h':
        if ($DB->record_exists('tables_rows', $data)){
            $row = $DB->get_record('tables_rows', $data, '*', MUST_EXIST);
            $row->height = optional_param('height', 0, PARAM_INT);
            $row->timemodified = time();
            $DB->update_record('tables_rows', $row);
        }
        else {
            $data['height'] = optional_param('height', 0, PARAM_INT);
            $data['timecreated'] = time();
            $DB->insert_record('tables_rows', $data);
        }

        break;
    case 'resize_w': // Updating column
        if ($DB->record_exists('tables_columns', $data)){
            $column = $DB->get_record('tables_columns', $data, '*', MUST_EXIST);
            $column->width = optional_param('width', 0, PARAM_INT);
            $column->timemodified = time();
            $DB->update_record('tables_columns', $column);
        }
        else {
            $data['width'] = optional_param('width', 0, PARAM_INT);
            $data['timecreated'] = time();
            $DB->insert_record('tables_columns', $data);
        }

        break;
}

$DB->update_record('tables', (object)array('id' => $data['tableid'], 'timemodified' => time()));

// Updating row
