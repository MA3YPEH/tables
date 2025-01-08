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

global $CFG, $PAGE, $OUTPUT, $DB, $USER;

// Course module id.
$id = optional_param('id', 0, PARAM_INT);
// Activity instance id.
$t = optional_param('t', 0, PARAM_INT);

if ($id) {
    $cm = get_coursemodule_from_id('tables', $id, 0, false, MUST_EXIST);
    $course = $DB->get_record('course', array('id' => $cm->course), '*', MUST_EXIST);
    $moduleinstance = $DB->get_record('tables', array('id' => $cm->instance), '*', MUST_EXIST);
} else {
    $moduleinstance = $DB->get_record('tables', array('id' => $t), '*', MUST_EXIST);
    $course = $DB->get_record('course', array('id' => $moduleinstance->course), '*', MUST_EXIST);
    $cm = get_coursemodule_from_instance('tables', $moduleinstance->id, $course->id, false, MUST_EXIST);
}

$PAGE->set_url('/mod/tables/load_activity.php?id='.$id);
$PAGE->requires->jquery();

require_login($course, false, $cm);

$context = context_module::instance($cm->id);

require_capability('moodle/course:manageactivities', $context);

$user_focus_data = $DB->get_record('tables_users_focus', array('tableid' => $moduleinstance->id, 'userid' => $USER->id));
if($_POST["sheet"]){
    $active_sheet = $_POST["sheet"];
    $user_focus_data->active_sheet = $active_sheet;
}
else{
    $active_sheet =  $user_focus_data->active_sheet;
}

echo $OUTPUT->header();

$type = $_POST['upload'];
$activity_id = $_POST['activity_selector'];
$students = get_enrolled_users($context);

if($DB->record_exists('tables_sheets', array ('name' => $active_sheet, 'tableid' => $moduleinstance->id))){
    $DB->delete_records('tables_sheets_cells', array('sheetid' => $active_sheet));
}

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

redirect('view.php?id='.$id);

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