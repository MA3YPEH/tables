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

global $CFG, $PAGE, $DB, $USER;

$PAGE->set_url('/mod/tables/submit_grade.php');
$PAGE->requires->jquery();

$data = array('id' => optional_param('grade_id', 0, PARAM_INT));

if($DB->record_exists('tables_cells_grade', $data)){
    $grade = $DB->get_record('tables_cells_grade', $data, '*', MUST_EXIST);
    $grade->grade = optional_param('grade_value', 0, PARAM_INT);
    $grade->feedback = optional_param('feedback', 0, PARAM_TEXT);
    $grade->timemodified = time();

    $DB->update_record('tables_cells_grade', $grade);
};


