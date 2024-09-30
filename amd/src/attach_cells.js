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
 * Update attached cells database.
 *
 * @param {object} object html object.
 * @param {string[]} messages array of messages.
 */
function attachCells(object, messages){

    let data = {
        update_type: object.id.split("_")[0],
        user_id: object.id.split("_")[1],
        table_id: object.id.split("_")[2],
        sheet_id: object.id.split("_")[3]
    };

    let first_cell = document.getElementById("first_cell-".concat(data["user_id"]));
    let last_cell = document.getElementById("last_cell-".concat(data["user_id"]));
    let regex = new RegExp("^(?:[A-Z]|[A-Z][A-Z]|[A-X][A-F][A-D])(?:[1-9]|[1-9][0-9]|[1-9][0-9][0-9]|[1-9][0-9][0-9][0-9]|[1-9][0-9][0-9][0-9][0-9]|[1-9][0-9][0-9][0-9][0-9][0-9]|10[0-3][0-9][0-9][0-9][0-9]|104[0-7][0-9][0-9][0-9]|1048[0-4][0-9][0-9]|10485[0-6][0-9]|104857[0-6])$");

    if(regex.test(first_cell.value) && regex.test(last_cell.value)){
        data["first_cell"] = first_cell.value;
        data["last_cell"] = last_cell.value;

        // Send information to update_cell_focus.php for updating database
        $.ajax({
            method: "POST",
            url: "attach_cells.php",
            data: data
        });
    }
    else{
        alert(messages[1]);
    }
}

/**
 * Delete attached cell from student.
 *
 * @param {object} object html object.
 */

function deleteAttachedCell(object){
    let data = {
        attached_cell_id: object.getAttribute('data-attached-id')
    };

    $.ajax({
        method: "POST",
        url: "remove_attached_cells.php",
        data: data
    });

    document.getElementById(data['attached_cell_id']).remove();
}