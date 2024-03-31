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

$PAGE->set_url('/mod/tables/updatefont.php');
$PAGE->requires->jquery();

$PAGE->requires->js(new moodle_url($CFG->wwwroot. '/mod/tables/amd/src/update_data.js'));

$time = time();

$cell_data = array (
    'tableid' => optional_param('table_id', null, PARAM_INT),
    'name' => optional_param('cell_id', null, PARAM_TEXT),
    'timecreated' => $time,
    'timeupdated' => $time);

switch (optional_param('button_type', 0, PARAM_TEXT)){
    case "font-family-selector":
        $cell_data['font_family'] = optional_param('input_content', 'Calibri', PARAM_TEXT);

        if(!$DB->record_exists('tables_cells', array('name' => $cell_data['name'],
            'tableid' => $cell_data['tableid']))){
            $DB->insert_record('tables_cells', $cell_data);
        }
        else{
            $cell = $DB->get_record('tables_cells', array('name' => $cell_data['name'],
                'tableid' => $cell_data['tableid']), '*', MUST_EXIST);
            $cell->font_family = $cell_data['font_family'];
            $cell->timemodified = $time;
            $DB->update_record('tables_cells', $cell);
        }

        break;

    case "font-size-selector":
        $cell_data['font_size'] = optional_param('input_content', 11, PARAM_INT);

        if(!$DB->record_exists('tables_cells', array('name' => $cell_data['name'],
            'tableid' => $cell_data['tableid']))){
            $DB->insert_record('tables_cells', $cell_data);
        }
        else{
            $cell = $DB->get_record('tables_cells', array('name' => $cell_data['name'],
                'tableid' => $cell_data['tableid']), '*', MUST_EXIST);
            $cell->font_size = $cell_data['font_size'];
            $cell->timemodified = $time;
            $DB->update_record('tables_cells', $cell);
        }

        break;

    case "font-bold-button":
        $cell_data['bold'] = optional_param('input_content', 'normal', PARAM_TEXT);

        if(!$DB->record_exists('tables_cells', array('name' => $cell_data['name'],
            'tableid' => $cell_data['tableid']))){
            $DB->insert_record('tables_cells', $cell_data);
        }
        else{
            $cell = $DB->get_record('tables_cells', array('name' => $cell_data['name'],
                'tableid' => $cell_data['tableid']), '*', MUST_EXIST);
            $cell->bold = $cell_data['bold'];
            $cell->timemodified = $time;
            $DB->update_record('tables_cells', $cell);
        }

        break;

    case "font-italic-button":
        $cell_data['italic'] = optional_param('input_content', 'normal', PARAM_TEXT);

        if(!$DB->record_exists('tables_cells', array('name' => $cell_data['name'],
            'tableid' => $cell_data['tableid']))){
            $DB->insert_record('tables_cells', $cell_data);
        }
        else{
            $cell = $DB->get_record('tables_cells', array('name' => $cell_data['name'],
                'tableid' => $cell_data['tableid']), '*', MUST_EXIST);
            $cell->italic = $cell_data['italic'];
            $cell->timemodified = $time;
            $DB->update_record('tables_cells', $cell);
        }

        break;

    case "font-underline-button":
        $cell_data['underline'] = optional_param('input_content', 'none', PARAM_TEXT);

        if(!$DB->record_exists('tables_cells', array('name' => $cell_data['name'],
            'tableid' => $cell_data['tableid']))){
            $DB->insert_record('tables_cells', $cell_data);
        }
        else{
            $cell = $DB->get_record('tables_cells', array('name' => $cell_data['name'],
                'tableid' => $cell_data['tableid']), '*', MUST_EXIST);
            $cell->underline = $cell_data['underline'];
            $cell->timemodified = $time;
            $DB->update_record('tables_cells', $cell);
        }

        break;
    case "text-left-button":
    case "text-center-button":
    case "text-right-button":
        $cell_data['text_align'] = optional_param('input_content', 'center', PARAM_TEXT);

        if(!$DB->record_exists('tables_cells', array('name' => $cell_data['name'],
            'tableid' => $cell_data['tableid']))){
            $DB->insert_record('tables_cells', $cell_data);
        }
        else{
            $cell = $DB->get_record('tables_cells', array('name' => $cell_data['name'],
                'tableid' => $cell_data['tableid']), '*', MUST_EXIST);
            $cell->text_align = $cell_data['text_align'];
            $cell->timemodified = $time;
            $DB->update_record('tables_cells', $cell);
        }
        break;
}

// Updating table

$DB->update_record('tables', (object)array('id' => $cell_data['tableid'], 'timemodified' => $time));
