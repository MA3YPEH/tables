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
 * Update cell information for page and database.
 *
 * @param {object} object html object.
 * @param {WebSocket} conn connection to websocket.
 * @param {boolean} focus onfocus function?
 */
function updateCell(object, conn, focus) {
    // if (focus === undefined) {
    //     focus = true;
    // }
    let update_type = "input";
    let id_field = object.id;
    let value_field = object.value;
    let table_id = object.name.replace("cell_module_", "");
    let data = {update_type: update_type,
        table_id: table_id,
        cell_id: id_field,
        cell_content: value_field,
        cell_focus: focus};

    // Send information to other users
    conn.send(JSON.stringify(data));

    // Send information to updatecell.php for updating database
    $.ajax({
        method: "POST",
        url: "updatecell.php",
        data: data
    });
}

/**
 * Update cells height for page and database.
 *
 * @param {object} object html object.
 * @param {WebSocket} conn connection to websocket.
 */
function updateHeight(object, conn) {
    let update_type = "resize_h";
    let value = object.value;
    let row_id = "row_".concat(value);
    let height = document.getElementById(row_id).offsetHeight;
    let table_id = object.name.replace("cell_module_", "");
    let data = {update_type: update_type,
        table_id: table_id,
        name: row_id,
        height: height};

    // Send information to other users
    //conn.send(JSON.stringify(data));

    // Send information to update_cell_size.php for updating database
    $.ajax({
        method: "POST",
        url: "update_cell_size.php",
        data: data
    });
}

/**
 * Update cells width for page and database.
 *
 * @param {object} object html object.
 * @param {WebSocket} conn connection to websocket.
 */
function updateWidth(object, conn) {
    let update_type = "resize_w";
    let value = object.value;
    let col_id = "col_".concat(value);
    let width = document.getElementById(col_id).offsetWidth;
    let table_id = object.name.replace("cell_module_", "");
    let data = {update_type: update_type,
        table_id: table_id,
        name: col_id,
        width: width};

    // Send information to other users
    //conn.send(JSON.stringify(data));

    //Send information to update_cell_size.php for updating database
    $.ajax({
        method: "POST",
        url: "update_cell_size.php",
        data: data
    });
}