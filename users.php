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

$active_sheet = $DB->get_record('tables_users_focus', array('tableid' => $moduleinstance->id, 'userid' => $USER->id))->active_sheet;
if(isset($_POST['switch_sheet'])){
    $active_sheet = $_POST['switch_sheet'];
}

$url = new moodle_url('/mod/tables/users.php', array('id' => $id));

$PAGE->set_url($url);
$PAGE->set_title(format_string($moduleinstance->name));
$PAGE->set_heading(format_string($course->fullname));

$PAGE->requires->jquery();
$PAGE->requires->js(new moodle_url($CFG->wwwroot . '/mod/tables/amd/src/attach_cells.js?v=3.3'));
$PAGE->requires->js(new moodle_url($CFG->wwwroot . '/mod/tables/amd/src/connect_to_websocket.js?v=3.7'));

require_login($course, false, $cm);

$context = context_module::instance($cm->id);

require_capability('moodle/course:manageactivities', $context);

echo $OUTPUT->header();

$selector = "groups";
if(isset($_POST['attach_to'])){
    $selector = $_POST['attach_to'];
}

echo '
    <form name="select_attach_table" action="" method="post">
        <select class="m-attach-cells-selector" name="attach_to" onchange="this.form.submit()">
            <option value="students"';if($selector == "students"){ echo ' selected'; } echo'>
                '.get_string("students").'
            </option>
            <option value="groups"';if($selector == "groups"){ echo ' selected'; } echo'>
                '.get_string("groups").'
            </option>
        </select>
        <select class="m-attach-cells-selector" name="switch_sheet" onchange="this.form.submit()">';
            $all_sheets = $DB->get_records('tables_sheets', array('tableid' => $moduleinstance->id));
            foreach($all_sheets as $sheet){
                echo'
                <option value="'.$sheet->id.'"';if($active_sheet == $sheet->id){ echo ' selected'; } echo'>
                    '.get_string("sheet", "mod_tables")." ".$sheet->name.'
                </option>';
            }
            echo'
        </select>
    </form>';

switch($selector){
    case 'students':
    {
        $students = get_enrolled_users($context);

        echo '
        <table class="m-userbox">
            <thead>
                <tr>
                    <td>'
                        . get_string('students') .
                    '</td>
                    <td>'
                        . get_string('email') .
                    '</td>
                    <td>'
                        . get_string('role') .
                    '</td>
                    <td>'
                        . get_string('group') .
                    '</td>
                    <td>'
                        . get_string('attachedcells', 'mod_tables') .
                    '</td>
                </tr>
            </thead>
            <tbody>';
            foreach ($students as $student) {
                $viewableroles = get_viewable_roles($context, $student->id);
                $roles = get_user_roles_in_course($student->id, $course->id);

                if((strpos($roles, $viewableroles[4]) !== false) || (strpos($roles, $viewableroles[5]) !== false) || (strpos($roles, $viewableroles[6]) !== false) || (strpos($roles, $viewableroles[7]) !== false) || (strpos($roles, $viewableroles[8]) !== false)){
                    if($DB->record_exists('tables_users_cells', array('userid' => $student->id, 'sheetid' => $active_sheet))){
                        $user_attached_cells = $DB->get_records('tables_users_cells', array('userid' => $student->id, 'sheetid' => $active_sheet));
                    }
                    else {
                        $user_attached_cells = get_string('attachedcellsnoone', 'mod_tables');
                    }

                    if($DB->record_exists('groups_members', array('userid' => $student->id))) {
                        $groups = $DB->get_records('groups_members', array('userid' => $student->id));
                        foreach ($groups as $group){
                            $group_names = $DB->get_record('groups', array('id' => $group->groupid), '*', MUST_EXIST)->name;
                            $group_names .= " </br>";
                        }
                    }
                    else{
                        $group_names = get_string('groupsnone');
                    }

                    echo '<tr>
                        <td>' . $student->firstname  . " " . $student->lastname . '</td>
                        <td>' . $student->email . '</td>
                        <td>' . $roles . '</td>
                        <td>' . $group_names . '</td>
                        <td>';
                            $attached_cells = $DB->get_records('tables_users_cells', array('sheetid' => $active_sheet, 'userid' => $student->id));
                            foreach ($attached_cells as $attached_cell){
                                echo'
                                    <span id="'.$attached_cell->id.'">'.$attached_cell->cellname.' <span class="m-tables-blue-btn"><i class="fa fa-trash" data-attached-id="'.$attached_cell->id.'" data-user-id="'.$student->id.'" name="delete_cell" onclick="deleteAttachedCell(this)"></i></span></span>
                                ';
                            }
                        echo'</td>
                    </tr>';
                }

            }
            echo '</tbody>
        </table>';
        break;
    }
    case 'groups':
    {
        echo'Not available';
        break;
    }
}

echo '<script> let messages = ["'.get_string('alertselectcellss', 'mod_tables').'"] </script>';

echo $OUTPUT->footer();
