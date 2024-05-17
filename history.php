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

$url = new moodle_url('/mod/tables/history.php', array('id' => $id));

$PAGE->set_url($url);

$PAGE->requires->jquery();

require_login($course, false, $cm);

$context = context_module::instance($cm->id);

require_capability('moodle/course:manageactivities', $context);

echo $OUTPUT->header();

if($DB->record_exists('tables_cells_history', array('tableid' => $moduleinstance->id))){

    $cells_history = $DB->get_records('tables_cells_history', array('tableid' => $moduleinstance->id));

    echo '
    <table class="m-userbox">
        <thead>
            <tr>
                <td>'
        . get_string('cellname', 'mod_tables') .
        '</td>
                <td>'
        . get_string('user') .
        '</td>
                <td>'
        . get_string('cellcontent', 'mod_tables') .
        '</td>
                <td>'
        . get_string('timemodified', 'mod_tables') .
        '</td>
            </tr>
        </thead>
        <tbody>';
            foreach($cells_history as $record){
                $student = $DB->get_record('user', array('id'=>$record->userid), '*', MUST_EXIST);
                echo '
                <tr>
                    <td>'.$DB->get_record('tables_cells', array('name'=>$record->cellname, 'tableid'=>$moduleinstance->id), '*', MUST_EXIST)->name.'</td>
                    <td>'.$student->firstname.' '.$student->lastname.'</td>
                    <td>'.$record->content.'</td>
                    <td>'.date("Y-m-d H:i:s", $record->timecreated).'</td>
                </tr>';
            }
    echo '</tbody>
    </table>';
}
else{
    echo '<span>'. get_string('cellhistorynone', 'mod_tables') .'</span>';
}

echo $OUTPUT->footer();