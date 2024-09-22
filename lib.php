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
 * Library of interface functions and constants.
 *
 * @package     mod_tables
 * @copyright   2023 Mazur Egor <mazur.eh@edu.spbstu.ru>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Return if the plugin supports $feature.
 *
 * @param string $feature Constant representing the feature.
 * @return true | null True if the feature is supported, null otherwise.
 */
function tables_supports($feature) {
    switch ($feature) {
        case FEATURE_MOD_INTRO:
            return true;
        default:
            return null;
    }
}

/**
 * Saves a new instance of the mod_tables into the database.
 *
 * Given an object containing all the necessary data, (defined by the form
 * in mod_form.php) this function will create a new instance and return the id
 * number of the instance.
 *
 * @param object $moduleinstance An object from the form.
 * @param mod_tables_mod_form $mform The form.
 * @return int The id of the newly inserted record.
 */
function tables_add_instance($moduleinstance, $mform = null) {
    global $DB;

    $moduleinstance->timecreated = time();

    $id = $DB->insert_record('tables', $moduleinstance);
    $DB->insert_record('tables_sheets', array('tableid' => $id, 'timecreated' => time()));
    return $id;
}

/**
 * Updates an instance of the mod_tables in the database.
 *
 * Given an object containing all the necessary data (defined in mod_form.php),
 * this function will update an existing instance with new data.
 *
 * @param object $moduleinstance An object from the form in mod_form.php.
 * @param mod_tables_mod_form $mform The form.
 * @return bool True if successful, false otherwise.
 */
function tables_update_instance($moduleinstance, $mform = null) {
    global $DB;

    $moduleinstance->timemodified = time();
    $moduleinstance->id = $moduleinstance->instance;

    return $DB->update_record('tables', $moduleinstance);
}

/**
 * Removes an instance of the mod_tables from the database.
 *
 * @param int $id Id of the module instance.
 * @return bool True if successful, false on failure.
 */
function tables_delete_instance($id) {
    global $DB;
    $sheets = $DB->get_records("tables_sheets", array('tableid' => $id));

    $exists = $DB->get_record('tables', array('id' => $id));
    if (!$exists) {
        return false;
    }

    foreach($sheets as $sheet){
        $cells = $DB->get_records('tables_sheets_cells', array('sheetid' => $sheet));
        foreach($cells as $cell){
            $DB->delete_records('tables_cells_grade', array('cellid' => $cell->id));
        }
        $DB->delete_records('tables_sheets_cells', array('sheetid' => $sheet->id));
        $DB->delete_records('tables_cells_history', array('sheetid' => $sheet->id));
        $DB->delete_records('tables_groups_cells', array('sheetid' => $sheet->id));
        $DB->delete_records('tables_sheets_columns', array('sheetid' => $sheet->id));
        $DB->delete_records('tables_sheets_rows', array('sheetid' => $sheet->id));
        $DB->delete_records('tables_users_cells', array('sheetid' => $sheet->id));
    }
    $DB->delete_records('tables_sheets', array('tableid' => $id));
    $DB->delete_records('tables_users_focus', array('tableid' => $id));
    $DB->delete_records('tables', array('id' => $id));

    return true;
}

/**
 * This function extends the settings navigation block for the site.
 *
 * It is safe to rely on PAGE here as we will only ever be within the module
 * context when this is called
 *
 * @param settings_navigation $settings
 * @param navigation_node $tablesnode
 */
function tables_extend_settings_navigation(settings_navigation $settings, navigation_node $tablesnode) {
    global $DB;
    if (has_capability('moodle/course:manageactivities', $settings->get_page()->cm->context)) {
        $cm = get_coursemodule_from_id('tables', $settings->get_page()->cm->id);
        $tables = $DB->get_record("tables", ["id" => $cm->instance]);
        $urlUsers = new moodle_url('/mod/tables/users.php', ['id' => $settings->get_page()->cm->id]);
        $urlHistory = new moodle_url('/mod/tables/history.php', ['id' => $settings->get_page()->cm->id]);
        $urlGrades = new moodle_url('/mod/tables/grades.php', ['id' => $settings->get_page()->cm->id]);
        $moduleinstance = $DB->get_record('tables', array('id' => $cm->instance), '*', MUST_EXIST);
        if ($tables) {
            $urlUsers->param('t', $moduleinstance->id);
            $urlHistory->param('t', $moduleinstance->id);
            $urlGrades->param('t', $moduleinstance->id);
        } else {
            $urlUsers->param('t', 0);
            $urlHistory->param('t', 0);
            $urlGrades->param('t', 0);
        }
        $tablesnode->add(get_string("usersoncourse", "mod_tables"), $urlUsers);
        $tablesnode->add(get_string("cellhistory", "mod_tables"), $urlHistory);
        $tablesnode->add(get_string("cellgrades", "mod_tables"), $urlGrades);
    }
}

/**
 * Generate column name for cell.
 *
 * @param int $column Number of the column.
 * @return string Alphabetic representation of the column.
 */
function generate_column_name(int $column): string
{
    $codechar = $column % 26;
    $letter = chr(65 + $codechar);
    $charcount = intval($column / 26);
    if($charcount > 0){
        return generate_column_name($charcount - 1).$letter;
    }
    else{
        return $letter;
    }
}
/**
 * Get width from database table "tables_cell"
 *
 * @param string $cellname name of the cell whose width we are looking for.
 * @return int width of the cell we are looking for.
 */
function get_column_width(string $name, int $sheetid): int
{
    global $DB;

    if($DB->record_exists('tables_sheets_columns', array('name' => $name, 'sheetid' => $sheetid))) {
        return $DB->get_record('tables_sheets_columns', array('name' => $name, 'sheetid' => $sheetid), '*', MUST_EXIST)->width;
    }
    else{
        return 120;
    }
}
/**
 * Get height from database table "tables_cell"
 *
 * @param string $cellname name of the cell whose height we are looking for.
 * @return int height of the cell we are looking for.
 */
function get_row_height(string $name, int $sheetid): int
{
    global $DB;

    if($DB->record_exists('tables_sheets_rows', array('name' => $name, 'sheetid' => $sheetid))) {
        return $DB->get_record('tables_sheets_rows', array('name' => $name, 'sheetid' => $sheetid), '*', MUST_EXIST)->height;
    }
    else{
        return 50;
    }
}

function get_cell_font_family(string $name, int $sheetid): string{
    global $DB;

    if($DB->record_exists('tables_sheets_cells', array('name' => $name, 'sheetid' => $sheetid))) {
        return $DB->get_record('tables_sheets_cells', array('name' => $name, 'sheetid' => $sheetid), '*', MUST_EXIST)->font_family;
    }
    else{
        return "Calibri";
    }
}

function get_cell_font_size(string $name, int $sheetid): int{
    global $DB;

    if($DB->record_exists('tables_sheets_cells', array('name' => $name, 'sheetid' => $sheetid))) {
        return $DB->get_record('tables_sheets_cells', array('name' => $name, 'sheetid' => $sheetid), '*', MUST_EXIST)->font_size;
    }
    else{
        return 11;
    }
}

function get_cell_bold(string $name, int $sheetid): string{
    global $DB;

    if($DB->record_exists('tables_sheets_cells', array('name' => $name, 'sheetid' => $sheetid))) {
        return $DB->get_record('tables_sheets_cells', array('name' => $name, 'sheetid' => $sheetid), '*', MUST_EXIST)->bold;
    }
    else{
        return "normal";
    }
}

function get_cell_italic(string $name, int $sheetid): string{
    global $DB;

    if($DB->record_exists('tables_sheets_cells', array('name' => $name, 'sheetid' => $sheetid))) {
        return $DB->get_record('tables_sheets_cells', array('name' => $name, 'sheetid' => $sheetid), '*', MUST_EXIST)->italic;
    }
    else{
        return "normal";
    }
}

function get_cell_underline(string $name, int $sheetid): string{
    global $DB;

    if($DB->record_exists('tables_sheets_cells', array('name' => $name, 'sheetid' => $sheetid))) {
        return $DB->get_record('tables_sheets_cells', array('name' => $name, 'sheetid' => $sheetid), '*', MUST_EXIST)->underline;
    }
    else{
        return "none";
    }
}

function get_cell_align(string $name, int $sheetid): string{
    global $DB;

    if($DB->record_exists('tables_sheets_cells', array('name' => $name, 'sheetid' => $sheetid))) {
        return $DB->get_record('tables_sheets_cells', array('name' => $name, 'sheetid' => $sheetid), '*', MUST_EXIST)->text_align;
    }
    else{
        return "center";
    }
}

function column_to_number($column): int
{
    $sum = 0;

    for($i = 0; $i < strlen($column); $i++){
        $sum += ord($column) - 64;
        if($i>0){
            $sum += 25;
        }
    }
    return $sum;

}

function isAttached($attached_cells, $cells_to_attach):bool{

    $cells_to_attach = explode("-", $cells_to_attach);
    if(count($cells_to_attach)<=1){
        $cells_to_attach[1] = $cells_to_attach[0];
    }

    foreach($attached_cells as $cells){
        $cells_arr = explode("-", $cells);

        if(count($cells_arr)<=1){
            $cells_arr[1] = $cells_arr[0];
        }

        $min_attached_column = column_to_number(preg_replace('/[^a-zA-Z]/', '', $cells_arr[0]));
        $max_attached_column = column_to_number(preg_replace('/[^a-zA-Z]/', '', $cells_arr[1]));
        $min_attached_row = (int)preg_replace('/[^0-9]/', '', $cells_arr[0]);
        $max_attached_row = (int)preg_replace('/[^0-9]/', '', $cells_arr[1]);

        $min_to_attach_column = column_to_number(preg_replace('/[^a-zA-Z]/', '', $cells_to_attach[0]));
        $max_to_attach_column = column_to_number(preg_replace('/[^a-zA-Z]/', '', $cells_to_attach[1]));
        $min_to_attach_row = (int)preg_replace('/[^0-9]/', '', $cells_to_attach[0]);
        $max_to_attach_row = (int)preg_replace('/[^0-9]/', '', $cells_to_attach[1]);

        if(($min_to_attach_column >= $min_attached_column) && ($max_to_attach_column <= $max_attached_column)
        && ($min_to_attach_row >= $min_attached_row) && ($max_to_attach_row <= $max_attached_row)){
            return true;
        }
    }

    return false;
}