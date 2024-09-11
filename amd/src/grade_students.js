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
 * JavaScript for the view.php.
 *
 * @module    mod_tables/updatecell
 * @copyright   2023 Mazur Egor <mazur.eh@edu.spbstu.ru>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Display table
 *
 * @param {object} object html object.
 */
function showTable(object){
    let table_type = object.getAttribute('data-table-type');
    let table_id = object.getAttribute('data-tableid');

    let table = document.getElementById(table_type.concat('_', table_id));
    if(table.style.display === "none"){
        table.style.display = "table";
        object.classList.remove("fa-plus");
        object.classList.add("fa-minus");
    }
    else{
        table.style.display = "none";
        object.classList.remove("fa-minus");
        object.classList.add("fa-plus");
    }
}

/**
 * Send new grade to db
 *
 * @param {object} object html object.
 */
function onclickSubmitGrade(object){
    let update_type = object.getAttribute('data-updatetype');
    let correct_id = object.getAttribute('data-correctid');
    let grade_input = document.getElementById('grade_input_'.concat(correct_id));
    let feedback = document.getElementById('grade_feedback_'.concat(correct_id)).value;

    if(update_type === "update_grade"){
        let data = {
            update_type: "update_grade",
            grade_id: correct_id,
            grade_value: grade_input.value,
            feedback: feedback
        };

        $.ajax({
            method: "POST",
            url: "submit_grade.php",
            data: data
        });
    }

    let group_score = document.getElementById("group_score_".concat(object.getAttribute('data-groupid')));
    let student_score = document.getElementById("student_score_".concat(object.getAttribute('data-studentid')));

    group_score.value = parseInt(group_score.value) - parseInt(grade_input.getAttribute('data-old-value')) + parseInt(grade_input.value);
    student_score.value = parseInt(student_score.value) - parseInt(grade_input.getAttribute('data-old-value')) + parseInt(grade_input.value);
    grade_input.setAttribute('data-old-value', grade_input.value)
    object.style.display = "none";
}

/**
 * Show submit button
 *
 * @param {object} object html object.
 */
function onchangeGrade(object){
    document.getElementById("submit_button_".concat(object.getAttribute("data-correctid"))).style.display = "inline-block";
}