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
 */
function updateTablesCell(object, conn) {
    let data = {update_type: "input",
        table_id: object.name.replace("cell_module_", ""),
        cell_id: object.id,
        cell_content: object.value};

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
 * Update cell focus information for page and database.
 *
 * @param {object} object html object.
 * @param {WebSocket} conn connection to websocket.
 * @param {boolean} focus onfocus function?
 */
function focusCell(object, conn, focus) {
    if (focus === undefined) {
        focus = true;
    }

    let data = {update_type: "focus",
        table_id: object.name.replace("cell_module_", ""),
        cell_id: object.id,
        cell_content: object.value,
        cell_focus: focus};

    document.getElementById("focused_cell").value = object.id;
    document.getElementById("focused_cell_content").value = object.value;
    document.getElementById("font-family-selector").value = object.style.fontFamily;
    document.getElementById("font-size-selector").value = object.style.fontSize.replace("pt", "");

    if(object.style.fontWeight !== "bold"){
        document.getElementById("font-bold-button").style.border = "";
    }
    else{
        document.getElementById("font-bold-button").style.border = "1px solid black";
    }
    if(object.style.fontStyle !== "italic"){
        document.getElementById("font-italic-button").style.border = "";
    }
    else{
        document.getElementById("font-italic-button").style.border = "1px solid black";
    }
    if(object.style.textDecoration !== "underline"){
        document.getElementById("font-underline-button").style.border = "";
    }
    else{
        document.getElementById("font-underline-button").style.border = "1px solid black";
    }

    // Send information to other users
    conn.send(JSON.stringify(data));

    // Send information to update_cell_focus.php for updating database
    $.ajax({
        method: "POST",
        url: "update_cell_focus.php",
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
    let row_id = "row_".concat(object.value);

    let data = {update_type: "resize_h",
        table_id: object.name.replace("cell_module_", ""),
        name: row_id,
        height: document.getElementById(row_id).offsetHeight};

    // Send information to other users
    conn.send(JSON.stringify(data));

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
    let col_id = "col_".concat(object.value);

    let data = {update_type: "resize_w",
        table_id: object.name.replace("cell_module_", ""),
        name: col_id,
        width: document.getElementById(col_id).offsetWidth};

    // Send information to other users
    conn.send(JSON.stringify(data));

    //Send information to update_cell_size.php for updating database
    $.ajax({
        method: "POST",
        url: "update_cell_size.php",
        data: data
    });
}

/**
 * Update cell text parameters information for page and database.
 *
 * @param {object} object html object.
 * @param {WebSocket} conn connection to websocket.
 */
function updateFont(object, conn) {
    let data = {update_type: "font",
        button_type: object.id,
        table_id: object.name.replace("cell_module_", ""),
        cell_id: document.getElementById("focused_cell").value,
        input_content: object.value};

    if(data['cell_id']){
        switch (object.id){
            case "font-family-selector":
                document.getElementById(data['cell_id']).style.fontFamily = object.value;
                break;
            case "font-size-selector":
                document.getElementById(data['cell_id']).style.fontSize = object.value.concat("pt");
                break;
            case "font-bold-button":
                if(object.value !== "bold")
                {
                    document.getElementById(object.id).value = "bold";
                    document.getElementById(object.id).style.border = "1px solid black";
                    document.getElementById(data['cell_id']).style.fontWeight = "bold";
                    data['input_content'] = "bold";
                }
                else{
                    document.getElementById(object.id).value = "normal";
                    document.getElementById(object.id).style.border = "";
                    document.getElementById(data['cell_id']).style.fontWeight = "normal";
                    data['input_content'] = "normal";
                }
                break;
            case "font-italic-button":
                if(object.value !== "italic")
                {
                    document.getElementById(object.id).value = "italic";
                    document.getElementById(object.id).style.border = "1px solid black";
                    document.getElementById(data['cell_id']).style.fontStyle = "italic";
                    data['input_content'] = "italic";
                }
                else{
                    document.getElementById(object.id).value = "normal";
                    document.getElementById(object.id).style.border = "";
                    document.getElementById(data['cell_id']).style.fontStyle = "normal";
                    data['input_content'] = "normal";
                }
                break;
            case "font-underline-button":
                if(object.value !== "underline")
                {
                    document.getElementById(object.id).value = "underline";
                    document.getElementById(object.id).style.border = "1px solid black";
                    document.getElementById(data['cell_id']).style.textDecoration = "underline";
                    data['input_content'] = "underline";
                }
                else{
                    document.getElementById(object.id).value = "none";
                    document.getElementById(object.id).style.border = "";
                    document.getElementById(data['cell_id']).style.textDecoration = "none";
                    data['input_content'] = "none";
                }
                break;
        }

        // Send information to other users
        conn.send(JSON.stringify(data));

        // Send information to updatefont.php for updating database
        $.ajax({
            method: "POST",
            url: "updatefont.php",
            data: data
        });
    }
}