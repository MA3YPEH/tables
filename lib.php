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
    global $DB, $PAGE;

    $cm = $PAGE->cm;
    if (!$cm) {
        return;
    }

    if (has_capability('moodle/course:manageactivities', $cm->context)) {

        $tables = $DB->get_record("tables", ["id" => $cm->instance]);
        $urlUsers = new moodle_url('/mod/tables/users.php', ['id' => $PAGE->cm->id]);
        $urlHistory = new moodle_url('/mod/tables/history.php', ['id' => $PAGE->cm->id]);
        $urlGrades = new moodle_url('/mod/tables/grades.php', ['id' => $PAGE->cm->id]);
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

function get_cell_range($lower, $upper):array {
    $arr = array();
    for ($i = $lower; $i != $upper; $i++) {
        array_push($arr, $i);
    }
    array_push($arr, $upper);
    return $arr;
}

function loadCell($sheetid, $name, $content, $bold = "normal")
{
    global $DB;

    $cell_data = array (
        'sheetid' => $sheetid,
        'name' => $name);

    if($DB->record_exists('tables_sheets_cells', $cell_data)){
        $cell_data = $DB->get_record('tables_sheets_cells', $cell_data, '*', MUST_EXIST);
        $cell_data->content = $content;
        $cell_data->bold = $bold;
        $cell_data->timmodified = time();
        $DB->update_record('tables_sheets_cells', (object)$cell_data);
    }
    else{
        $cell_data['content'] = $content;
        $cell_data['bold'] = $bold;
        $cell_data['timecreated'] = time();
        $DB->insert_record('tables_sheets_cells', $cell_data);
    }
}

function load_from_activity($context, $course, $active_sheet, $type, $activity_id)
{
    global $DB;
    $students = get_enrolled_users($context);

    $DB->delete_records('tables_sheets_cells', array('sheetid' => $active_sheet));

    $sheet = $DB->get_record('tables_sheets', array ('id' => $active_sheet), '*', MUST_EXIST);
    $sheet->activityloadtype = $type;
    $sheet->activityloadid = $activity_id;
    $sheet->updateonreloadpage = "true";
    $sheet->timemodified = time();

    $DB->update_record("tables_sheets", $sheet);

    switch ($type){
        case "quiz":{
            loadCell($active_sheet, 'A1', get_string('group'), "bold");
            loadCell($active_sheet, 'B1', get_string('user'), "bold");
            loadCell($active_sheet, 'C1', get_string('attempt', 'mod_scorm'), "bold");
            loadCell($active_sheet, 'D1', get_string('variant', 'quiz_statistics'), "bold");
            loadCell($active_sheet, 'E1', get_string('question'), "bold");
            loadCell($active_sheet, 'F1', get_string('summary'), "bold");
            loadCell($active_sheet, 'G1', get_string('rightanswer', 'mod_tables'), "bold");
            loadCell($active_sheet, 'H1', get_string('answer', 'mod_tables'), "bold");
            loadCell($active_sheet, 'I1', get_string('maxfraction', 'mod_tables'), "bold");
            loadCell($active_sheet, 'J1', get_string('fraction', 'mod_tables'), "bold");

            $s_rows = 2;

            foreach ($students as $student){
                loadCell($active_sheet, 'A'.$s_rows, $group_names);
                loadCell($active_sheet, 'B'.$s_rows, $student->firstname." ".$student->lastname);

                $groups = $DB->get_records('groups_members', array('userid' => $student->id));
                $group_names = "";
                foreach ($groups as $group){
                    $group_names .= $DB->get_record('groups', array('id' => $group->groupid), '*', MUST_EXIST)->name;
                    $group_names .= " ";
                }

                $quiz_attempts = quiz_get_user_attempts($activity_id, $student->id);
                $attempt_number = 1;

                $have_attempts = false;

                foreach ($quiz_attempts as $quiz_attempt){
                    if($quiz_attempt->userid == $student->id){
                        $have_attempts = true;
                        loadCell($active_sheet, 'C'.$s_rows, "Попытка ".$attempt_number);

                        $module_id = $DB->get_record('modules', array('name' => 'quiz'), '*', MUST_EXIST)->id;
                        $quiz_module_instance = $DB->get_record('course_modules',
                            array('course' => $course->id, 'module' => $module_id, 'instance' => $quiz_attempt->quiz), '*', MUST_EXIST)->id;
                        $quiz_context = $DB->get_record('context',
                            array('contextlevel' => $context->contextlevel, 'instanceid' => $quiz_module_instance), '*', MUST_EXIST)->id;

                        $sql = "SELECT qu.contextid, qa.*, qas.fraction, qas.userid, qas.questionattemptid
                            FROM {question_attempts} qa
                            JOIN {question_usages} qu
                            ON qu.id = qa.questionusageid
                            JOIN {question_attempt_steps} qas
                            ON qa.id = qas.questionattemptid
                            WHERE qu.contextid = $quiz_context AND qas.userid = $student->id";
                        $q_usages = $DB->get_records_sql($sql);

                        foreach($q_usages as $q_usage){
                            $question_attempts = $DB->get_records('question_attempts', array('questionusageid' => $q_usage->questionusageid));
                            loadCell($active_sheet, 'D'.$s_rows, "Вариант ".$q_usage->variant);
                            foreach ($question_attempts as $qa){
                                $question_name = $DB->get_record('question', array('id' => $qa->questionid), '*', MUST_EXIST)->name;
                                loadCell($active_sheet, 'E'.$s_rows, $question_name);
                                loadCell($active_sheet, 'F'.$s_rows, $qa->questionsummary);
                                loadCell($active_sheet, 'G'.$s_rows, $qa->rightanswer);
                                loadCell($active_sheet, 'H'.$s_rows, $qa->responsesummary);
                                loadCell($active_sheet, 'I'.$s_rows, round($qa->maxfraction, 2));
                                $fraction = $DB->get_record('question_attempt_steps',
                                    array('questionattemptid' => $qa->id, 'sequencenumber' => 2), '*', MUST_EXIST)->fraction;

                                loadCell($active_sheet, 'J'.$s_rows, round($fraction, 2));
                                $s_rows++;
                            }
                        }
                        $attempt_number++;
                    }
                    else{
                        $have_attempts = false;
                    }
                }
                if($have_attempts == false){
                    loadCell($active_sheet, 'A'.$s_rows, $student->firstname." ".$student->lastname);
                    loadCell($active_sheet, 'B'.$s_rows, get_string('noattempts', 'mod_tables'));
                    $s_rows+=2;
                }
                else if($have_attempts == true){
                    loadCell($active_sheet, 'A'.$s_rows, get_string('sumresult', 'mod_tables'));
                    loadCell($active_sheet, 'B'.$s_rows, round($quiz_attempt->sumgrades, 2));
                    $s_rows+=2;
                }
            }

            break;
        }
    }
}