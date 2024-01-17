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
 * Prints an instance of mod_tables.
 *
 * @package     mod_tables
 * @copyright   2023 Mazur Egor <mazur.eh@edu.spbstu.ru>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require(__DIR__.'/../../config.php');
require_once(__DIR__.'/lib.php');

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

require_login($course, true, $cm);

$modulecontext = context_module::instance($cm->id);

$event = \mod_tables\event\course_module_viewed::create(array(
    'objectid' => $moduleinstance->id,
    'context' => $modulecontext
));
$event->add_record_snapshot('course', $course);
$event->add_record_snapshot('tables', $moduleinstance);
$event->trigger();

$PAGE->set_url('/mod/tables/view.php', array('id' => $cm->id));
$PAGE->set_title(format_string($moduleinstance->name));
$PAGE->set_heading(format_string($course->fullname));
$PAGE->set_context($modulecontext);
$PAGE->requires->jquery();

$PAGE->requires->js(new moodle_url($CFG->wwwroot. '/mod/tables/senddata.js'));

echo $OUTPUT->header();

$rows = $moduleinstance->rowcount;
$columns = $moduleinstance->columncount;

echo '

<script type="text/javascript">
    let conn = new WebSocket("ws://localhost:8081");
    $(document).ready(function(){
        conn.onopen = function(e) {
            console.log("Connection established!");
        };

        conn.onmessage = function(e) {
            console.log(e.data);
            let data = JSON.parse(e.data);
            document.getElementById(data["cell_id"]).value = data["cell_content"];
        };
    })
</script>

<div class="m-tables-settings">
    <table id="colltable">
        <tbody>';
            for ($row = 1; $row <= $rows; $row++) {
            echo '<tr>';
                for ($column = 0; $column < $columns; $column++) {
                    $cellname = generate_column_name($column) . $row;

                    if($DB->record_exists('tables_cells', array('name' => $cellname, 'tableid' => $moduleinstance->id))){
                        $value = $DB->get_record('tables_cells', array('name' => $cellname, 'tableid' => $moduleinstance->id), '*', MUST_EXIST)->content;
                    }
                    else{
                        $value = '';
                    }

                    echo '<td><textarea class="m-tables-cell" name="cell_module_'.$moduleinstance->id.'" onchange="update_cell(this, conn)" id='.$cellname . '>'. $value .'</textarea></td>';
                }
            echo '</tr>';
            }
        echo '</tbody>
    </table>
    
</div>';

echo $OUTPUT->footer();

