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

$url = new moodle_url('/mod/tables/users.php', array('id' => $id));

$PAGE->set_url($url);

$PAGE->requires->jquery();
$PAGE->requires->js(new moodle_url($CFG->wwwroot . '/mod/tables/amd/src/attach_cells.js?v=1.5'));

require_login($course, false, $cm);

$context = context_module::instance($cm->id);

require_capability('moodle/course:manageactivities', $context);

echo $OUTPUT->header();
$students = get_enrolled_users($context);

    echo '
    <table class="m-userbox">
        <thead>
            <tr>
                <td>'
                    . get_string('user') .
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

                if(str_contains($roles, $viewableroles[4]) || str_contains($roles, $viewableroles[5]) || str_contains($roles, $viewableroles[6]) || str_contains($roles, $viewableroles[7]) || str_contains($roles, $viewableroles[8])){
                    if($DB->record_exists('tables_users_cells', array('userid' => $student->id, 'tableid' => $moduleinstance->id))){
                        if($DB->get_record('tables_users_cells', array('userid' => $student->id, 'tableid' => $moduleinstance->id), '*', MUST_EXIST)->attached_cells == null){
                            $user_attached_cells = get_string('attachedcellsnoone', 'mod_tables');
                        }
                        else{
                            $user_attached_cells = $DB->get_record('tables_users_cells', array('userid' => $student->id, 'tableid' => $moduleinstance->id), '*', MUST_EXIST)->attached_cells;
                        }
                    }
                    else {
                        $user_attached_cells = get_string('attachedcellsnoone', 'mod_tables');
                    }

                    if($DB->record_exists('groups_members', array('userid' => $student->id))) {
                        $groups = $DB->get_records('groups_members', array('userid' => $student->id));
                        foreach ($groups as $group){
                            $group_names = $DB->get_record('groups', array('id' => $group->id), '*', MUST_EXIST)->name;
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
                    <td>' . $user_attached_cells . '
                        <span class="m-attach-cells-btn">
                            <i class="fa fa-pencil" id="'.$student->id.'" onclick="switchAttachCellsBar(this)" ></i>
                        </span>
                        <div id="p'.$student->id.'" class="m-attach-cells-bar" style="display: none;">
                            Введите диапазон привязываемых ячеек <input id="first_cell-'.$student->id.'" type="text"> - <input id="last_cell-'.$student->id.'" type="text"> 
                            <span class="m-save-attached-cells"><i class="fa fa-floppy-o" id="s'.$student->id.'-m'.$moduleinstance->id.'" onclick="attachCells(this)"></i></span>
                        </div>
                    </td>
                </tr>';
                }

            }
        echo '</tbody>
    </table>';

echo $OUTPUT->footer();
