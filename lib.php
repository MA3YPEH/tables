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

    $exists = $DB->get_record('tables', array('id' => $id));
    if (!$exists) {
        return false;
    }

    $DB->delete_records('tables_cells', array('tableid' => $id));
    $DB->delete_records('tables', array('id' => $id));

    return true;
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
function get_column_width(string $name, int $tableid): int
{
    global $DB;

    if(!$DB->record_exists('tables_columns', array('name' => $name,
        'tableid' => $tableid))) {

        return 120;
    }
    else{
        return $DB->get_record('tables_columns', array('name' => $name,
            'tableid' => $tableid), '*', MUST_EXIST)->width;
    }
}
/**
 * Get height from database table "tables_cell"
 *
 * @param string $cellname name of the cell whose height we are looking for.
 * @return int height of the cell we are looking for.
 */
function get_row_height(string $name, int $tableid): int
{
    global $DB;

    if(!$DB->record_exists('tables_rows', array('name' => $name,
        'tableid' => $tableid))) {

        return 50;
    }
    else{
        return $DB->get_record('tables_rows', array('name' => $name,
            'tableid' => $tableid), '*', MUST_EXIST)->height;
    }
}
