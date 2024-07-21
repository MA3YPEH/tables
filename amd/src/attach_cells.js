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
 * Open or close input boxes.
 *
 * @param {object} object html object.
 */
function switchAttachCellsBar(object){
    try{
        if(document.getElementById("pencil_button".concat(object.id)).style.display==="none"){
            document.getElementById("pencil_button".concat(object.id)).style.display="";
            document.getElementById(object.id).removeAttribute("class", "fa fa-pencil");
            document.getElementById(object.id).setAttribute("class", "fa fa-times");
            document.getElementById('attached_cells'.concat(object.id)).style.display="none";
            document.getElementById('remove_attached_cells'.concat(object.id)).style.display="";
        }
        else{
            document.getElementById("pencil_button".concat(object.id)).style.display="none"
            document.getElementById(object.id).removeAttribute("class", "fa fa-times");
            document.getElementById(object.id).setAttribute("class", "fa fa-pencil");
            document.getElementById('attached_cells'.concat(object.id)).style.display="";
            document.getElementById('remove_attached_cells'.concat(object.id)).style.display="none";
        }
    }
    catch (e){
        console.log("There are no buttons that can be changed with this function")
        console.log(e)
    }

}

/**
 * Update attached cells database.
 *
 * @param {object} object html object.
 * @param {string[]} messages array of messages.
 */
function attachCells(object, messages){
    console.log(object)
    let data = {
        update_type: object.id.split("_")[0],
        user_id: object.id.split("_")[1],
        table_id: object.id.split("_")[2],
        sheet_id: object.id.split("_")[3]
    };

    console.log(data)

    let first_cell = document.getElementById("first_cell-".concat(data["user_id"]));
    let last_cell = document.getElementById("last_cell-".concat(data["user_id"]));
    let regex = new RegExp("^(?:[A-Z]|[A-Z][A-Z]|[A-X][A-F][A-D])(?:[1-9]|[1-9][0-9]|[1-9][0-9][0-9]|[1-9][0-9][0-9][0-9]|[1-9][0-9][0-9][0-9][0-9]|[1-9][0-9][0-9][0-9][0-9][0-9]|10[0-3][0-9][0-9][0-9][0-9]|104[0-7][0-9][0-9][0-9]|1048[0-4][0-9][0-9]|10485[0-6][0-9]|104857[0-6])$");

    if(regex.test(first_cell.value) && regex.test(last_cell.value)){
        if(first_cell.value === last_cell.value){
            data["attach"] = first_cell.value;
        }
        else{
            let first_cell_char = Array.from(first_cell.value.split(/([0-9]+)/)[0]);
            let first_cell_num = first_cell.value.split(/([0-9]+)/)[1];

            let last_cell_char = Array.from(last_cell.value.split(/([0-9]+)/)[0]);
            let last_cell_num = last_cell.value.split(/([0-9]+)/)[1];

            if(columnCharToInt(first_cell_char) > columnCharToInt(last_cell_char)){
                let buf = first_cell_char;
                first_cell_char = last_cell_char;
                last_cell_char = buf;
            }
            if(first_cell_num > last_cell_num){
                let buf = first_cell_num;
                first_cell_num = last_cell_num;
                last_cell_num = buf;
            }

            first_cell.value = first_cell_char + first_cell_num;
            last_cell.value = last_cell_char + last_cell_num;

            data["attach"] = first_cell.value + "-" + last_cell.value;
        }

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

    first_cell.value = "";
    last_cell.value = "";
    let object_switch_btn = {id:data["user_id"]};
    switchAttachCellsBar(object_switch_btn);
}

/**
 * Convert column name to number.
 *
 * @param {string} column column name.
 */
function columnCharToInt(column) {
    let sum = 0;

    for (let i = 0; i < column.length; i++) {
        sum += parseInt(column[i], 36) - 9;
        if(i>0){
            sum+=25;
        }
    }

    return sum;
}

/**
 * Convert column name to number.
 *
 * @param {string} attached_cells range of attached cells.
 * @param {string} cell checked cell.
 */
function isAttachedCell(attached_cells, cell){
    let cells = attached_cells.split(', ');

    if(attached_cells === 'teacher'){
        return true;
    }

    for(let i = 0; i < cells.length; i++){
        let att_cell = cells[i].split('-');

        if(att_cell.length<=1){
            att_cell[1] = att_cell[0];
        }

        let focus_out_cell_char = Array.from(cell.split(/([0-9]+)/)[0]);
        let focus_out_cell_num = cell.split(/([0-9]+)/)[1];

        let first_cell_char = Array.from(att_cell[0].split(/([0-9]+)/)[0]);
        let first_cell_num = att_cell[0].split(/([0-9]+)/)[1];
        let last_cell_char= Array.from(att_cell[1].split(/([0-9]+)/)[0]);
        let last_cell_num = att_cell[1].split(/([0-9]+)/)[1];

        if((columnCharToInt(focus_out_cell_char) >= columnCharToInt(first_cell_char)) && (columnCharToInt(focus_out_cell_char) <= columnCharToInt(last_cell_char))
            && (Number(focus_out_cell_num) >= Number(first_cell_num)) && (Number(focus_out_cell_num) <= Number(last_cell_num))){
            return true;
        }
    }
    return false;
}

/**
 * Delete attached cells from page and database.
 *
 * @param {object} object html object.
 */
function removeAttachedCells(object){
    console.log(object.id)

    let data = {
        update_type: object.id.split("_")[0],
        user_id: object.id.split("_")[1],
        table_id: object.id.split("_")[2],
        sheet_id: object.id.split("_")[3],
        removed_cells: object.id.split("_")[4]
    };

    document.getElementById(object.id).remove();
    document.getElementById(data['removed_cells']).remove();

    // Send information to update_cell_focus.php for updating database
    $.ajax({
        method: "POST",
        url: "remove_attached_cells.php",
        data: data
    });
}