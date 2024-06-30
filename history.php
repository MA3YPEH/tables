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

$url = new moodle_url('/mod/tables/history.php', array('id' => $id));

$PAGE->set_url($url);

$PAGE->requires->jquery();

require_login($course, false, $cm);

$context = context_module::instance($cm->id);

require_capability('moodle/course:manageactivities', $context);

echo $OUTPUT->header();

if($DB->record_exists('tables_cells_history', array('tableid' => $moduleinstance->id))){
    $cells_history = $DB->get_records('tables_cells_history', array('tableid' => $moduleinstance->id));

    $table = new table_sql("cell-history-table-{$course->id}");

    $table->set_sql('{tables_cells_history}.*, {tables_cells_history}.cellname, {user}.firstname AS firstname, {user}.lastname AS lastname, {tables_cells_history}.content, FROM_UNIXTIME({tables_cells_history}.timecreated) as time',
        "{tables_cells_history} JOIN {user} ON {tables_cells_history}.userid = {user}.id",
        '{tables_cells_history}.tableid='.$moduleinstance->id);

    $table->no_sorting('content');
    $table->define_columns(array('cellname', 'fullname', 'content', 'time'));
    $table->define_headers(array(get_string('cellname', 'mod_tables'), get_string('fullname'),
        get_string('cellcontent', 'mod_tables'), get_string('timemodified', 'mod_tables')));

    // The name column is a header.
    $table->define_header_column('fullname');

    // Make this table sorted by last name by default.
    $table->sortable(true, 'lastname');

    $table->out(40, true);
}
else{
    echo '<span>'. get_string('cellhistorynone', 'mod_tables') .'</span>';
}

echo $OUTPUT->footer();