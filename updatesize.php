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

$PAGE->set_url('/mod/tables/updatesize.php');
$PAGE->requires->jquery();

$PAGE->requires->js(new moodle_url($CFG->wwwroot. '/mod/tables/amd/src/senddata.js'));

$time = time();

$data = array (
    'tableid' => optional_param('table_id', 0, PARAM_INT),
    'timecreated' => $time,
    'imeupdated' => $time
);

$data['width'] = optional_param('width', 0, PARAM_INT);
if (!$DB->record_exists('tables_columns', array('name' => $data['column_name'],
    'tableid' => $data['tableid']))){

    $column_data = array (
        'tableid' => $data['tableid'],
        'name' => $data['column_name'],
        'width' => optional_param('cell_width', 0, PARAM_TEXT),
        'timecreated' => $time,
        'timeupdated' => $time
    );

    $DB->insert_record('tables_columns', $column_data);
}
else {
    $column = $DB->get_record('tables_columns', array('name' => $data['column_name'],
        'tableid' => $data['tableid']), '*', MUST_EXIST);
    $column->width = $data['width'];
    $column->timemodified = $time;
    $DB->update_record('tables_columns', $column);
}

$DB->update_record('tables', (object)array('id' => $column_data['tableid'], 'timemodified' => $time));

//switch(optional_param('update_type', 0, PARAM_TEXT)){
//    case 'resize_h':
//    case 'resize_w':
//        $data['width'] = optional_param('width', 0, PARAM_INT);
//        if (!$DB->record_exists('tables_columns', array('name' => $data['column_name'],
//            'tableid' => $data['tableid']))){
//
//            $column_data = array (
//                'tableid' => $data['tableid'],
//                'name' => $data['column_name'],
//                'width' => optional_param('cell_width', 0, PARAM_TEXT),
//                'timecreated' => $time,
//                'timeupdated' => $time
//            );
//
//            $DB->insert_record('tables_columns', $column_data);
//        }
//        else {
//            $column = $DB->get_record('tables_columns', array('name' => $data['column_name'],
//                'tableid' => $data['tableid']), '*', MUST_EXIST);
//                $column->width = $data['width'];
//                $column->timemodified = $time;
//                $DB->update_record('tables_columns', $column);
//        }
//        break;
//}

// Updating column



// Updating row



