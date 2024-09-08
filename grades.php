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

require_once($CFG->libdir.'/tablelib.php');

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

$url = new moodle_url('/mod/tables/grades.php', array('id' => $id));

$PAGE->set_url($url);
$PAGE->set_title(format_string($moduleinstance->name));
$PAGE->set_heading(format_string($course->fullname));

$PAGE->requires->jquery();
$PAGE->requires->js(new moodle_url($CFG->wwwroot . '/mod/tables/amd/src/grade_students.js'));

require_login($course, false, $cm);

$context = context_module::instance($cm->id);

require_capability('moodle/course:manageactivities', $context);

$modulecontext = context_module::instance($cm->id);

$roles = get_default_enrol_roles($modulecontext);
$user_roles = get_user_roles_in_course($USER->id, $course->id);

if(str_contains($user_roles, $roles[1]) || str_contains($user_roles, $roles[3])){
    $user_activity_role = "teacher";
}
elseif(str_contains($user_roles, $roles[4])){
    $user_activity_role = "assistant";
}
else{
    $user_activity_role = "student";
}

echo $OUTPUT->header();

$groups = groups_get_all_groups($course->id, 0, 0, 'id, name');
echo '
    <table class="m-tables-grades-maintable">
        <thead>
            <tr>
                <th>
                    Group
                </th>
                <th>
                    Student
                </th>
                <th>
                    Score
                </th>
            </tr>
        </thead>
        <tbody>
';
foreach($groups as $group){
    echo '
            <tr>
                <td class="m-tables-group-name">
                    '.$group->name.'
                </td>
                <td class="m-tables-group-students">
                    <span  class="m-tables-blue-btn">
                        <i class="fa fa-plus" data-tableid="'.$group->id.'" data-table-type="students_table" onclick="showTable(this)" ></i>
                    </span>
                    <table class="m-tables-students-table" id="students_table_'.$group->id.'" style="display: none">
                        <thead>
                            <tr>
                                <th>
                                    Name
                                </th>
                                <th>
                                    Grades
                                </th>
                                <th>
                                    Score
                                </th>
                            </tr>
                        </thead>
                        <tbody>
    ';
    $group_score = 0;

    $students = get_enrolled_users($context, '', $group->id, 'u.id, u.firstname, u.lastname');

    foreach($students as $student){
        echo'
                <tr>
                    <td class="m-tables-students-name">
                        '.$student->firstname.' '.$student->lastname.'
                    </td>
                    <td class="m-tables-students-grades">
                        <span  class="m-tables-blue-btn">
                            <i class="fa fa-plus" data-tableid="'.$student->id.'" data-table-type="grades_table" onclick="showTable(this)" ></i>
                        </span>
                        <table class="m-tables-grades-table" id="grades_table_'.$student->id.'" style="display: none">
                            <thead>
                                <tr>
                                    <th>
                                        Cell
                                    </th>
                                    <th>
                                        Grade
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
        ';
        $student_score = 0;

        $grades = $DB->get_records('tables_cells_grade', array('userid' => $student->id));

        foreach($grades as $grade){
            $cell = $DB->get_record('tables_sheets_cells', array('id' => $grade->cellid), 'sheetid, name', MUST_EXIST);
            $sheet = $DB->get_record('tables_sheets', array('id' => $cell->sheetid), 'tableid', MUST_EXIST);
            if($sheet->tableid == $moduleinstance->id){
                $student_score += $grade->grade;
                echo '
                                <tr>
                                    <td>
                                        '.$cell->name.'
                                    </td>
                                    <td>
                                       <input class="m-tables-grades-active-input" id="grade_input_'.$grade->id.'" data-correctid="'.$grade->id.'" type="number" oninput="onchangeGrade(this)" data-old-value="'.$grade->grade.'" value="'.$grade->grade.'" '; if($user_activity_role != "teacher"){echo'readonly';} echo'>
                                       <span class="m-tables-green-btn" id="submit_button_'.$grade->id.'" data-groupid="'.$group->id.'" data-studentid="'.$student->id.'" data-updatetype="update_grade" data-correctid="'.$grade->id.'" onclick="onclickSubmitGrade(this)" style="display: none">
                                            <i class="fa fa-check" ></i>
                                        </span>
                                    </td>
                                </tr>
                ';
            }
        }
        $group_score += $student_score;
        echo'
                            </tbody>
                        </table>
                    </td>
                    <td>
                        <input class="m-tables-grades-readonly-input" id="student_score_'.$student->id.'" type="number" readonly value="'.$student_score.'"/>
                    </td>
                </tr>
        ';
    }
    echo '
                        </tbody>
                    </table>
                </td>
                <td class="m-tables-group-grade">
                    <input class="m-tables-grades-readonly-input" id="group_score_'.$group->id.'" type="number" readonly value="'.$group_score.'">
                </td>
            </tr>
    ';
}
echo '
        </tbody>
    </table>
';

//echo grade_update('mod/tables', $id, 'mod', 'tables', $moduleinstance->id, 0,
//    array('userid' => 3, 'rawgrade' => 55, 'feedback' => 'Hi', 'aggregationstatus' => 'used', 'aggregationweight' => 1),
//    array('itemname'=>$moduleinstance->name, 'needsupdate' => 0, 'gradetype' => GRADE_TYPE_VALUE, 'grademax' => 100, 'grademin' => 0));
echo $OUTPUT->footer();