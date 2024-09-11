<?php
// This file is part of Moodle - http://moodle.org/
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
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * This file is responsible for producing the survey reports
 *
 * @package   mod_survey
 * @copyright 1999 onwards Martin Dougiamas  {@link http://moodle.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once("../../config.php");
require_once("lib.php");

// Check that all the parameters have been provided.
global $DB, $USER, $CFG, $OUTPUT, $PAGE;

require_once($CFG->libdir.'/gradelib.php');

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

$url = new moodle_url('/mod/tables/send_grades.php', array('id' => $id));

$PAGE->set_url($url);

require_login($course, false, $cm);

$modulecontext = context_module::instance($cm->id);

require_capability('moodle/course:manageactivities', $modulecontext);

if(isset($_POST['send_grades'])){

    $students = get_enrolled_users($modulecontext, '', $_POST['send_grades'], 'u.id, u.firstname, u.lastname');

    foreach($students as $student){

        $student_score = 0;
        $grade_count = 0;
        $feedback = '';

        $grades = $DB->get_records('tables_cells_grade', array('userid' => $student->id));

        foreach($grades as $grade){
            $cell = $DB->get_record('tables_sheets_cells', array('id' => $grade->cellid), 'sheetid, name, content', MUST_EXIST);
            $sheet = $DB->get_record('tables_sheets', array('id' => $cell->sheetid), 'tableid', MUST_EXIST);
            if($sheet->tableid == $moduleinstance->id){
                $student_score += $grade->grade;
                $grade_count += 1;
                $feedback = $feedback.$cell->name.': '.$grade->feedback.' ';
            }
        }

        grade_update('mod/tables', $course->id, 'mod', 'tables', $moduleinstance->id, 0,
            array('userid' => $student->id, 'rawgrade' => $student_score, 'feedback' => $feedback, 'aggregationstatus' => 'used', 'aggregationweight' => 1),
            array('itemname' => $moduleinstance->name, 'needsupdate' => 0, 'gradetype' => GRADE_TYPE_VALUE, 'grademax' => $grade_count * 100, 'grademin' => 0));

    }
}

header('Location: view.php?id='.$id);
