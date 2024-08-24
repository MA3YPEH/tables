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
require_once(__DIR__.'/classes/form/filepicker.php');

// Check that all the parameters have been provided.
global $DB, $USER, $CFG, $OUTPUT, $PAGE;

require_once("$CFG->libdir/formslib.php");

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

$url = new moodle_url('/mod/tables/upload_from_xlsx.php?id='.$id);

$PAGE->set_url($url);
$PAGE->set_title(format_string($moduleinstance->name));
$PAGE->set_heading(format_string($course->fullname));

$PAGE->requires->jquery();

require_login($course, false, $cm);

$context = context_module::instance($cm->id);

require_capability('moodle/course:manageactivities', $context);

$mform = new pick_excel_file('upload_from_xlsx.php?id='.$id);

echo $OUTPUT->header();

if ($fromform = $mform->get_data()) {
    $mform->save_file('xlsxfile', 'upload/upload.xlsx', true);

    redirect('load_excel.php?id='.$id);
}
else {
    $mform->display();
}
echo $OUTPUT->footer();