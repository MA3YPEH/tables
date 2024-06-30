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

$(document).ready(function () {
    let data = {
        update_type: "focusout",
        cell_id: document.getElementById("prev_element").value
    };
    conn.onopen = function (e) {
        conn.send(JSON.stringify(data));
    }

});

/**
 * Update cell information for page and database.
 *
 * @param {object} object html object.
 */
function onInputSearch(object) {
    let children = document.querySelectorAll("#dropdown-content p");
    let searchstr = document.getElementById('search_students').value.toLowerCase();

    for (let i = 0; i < children.length; i++) {
        let value = children[i].querySelector("label").innerHTML.toLowerCase();

        if (value.includes(searchstr)) {
            children[i].style.display = 'block';
            console.log(searchstr)
            console.log(value)
        } else {
            children[i].style.display = 'none';
            console.log(searchstr)
            console.log(value)
        }
    }

}

/**
 * Update cell information for page and database.
 *
 * @param {object} object html object.
 * @param {WebSocket} conn connection to websocket.
 */
function updateTablesCell(object, conn) {
    let data = {
        update_type: "input",
        table_id: object.name.replace("cell_module_", ""),
        cell_id: object.id,
        cell_content: object.value
    };

    document.getElementById("focused_cell_content").value = data['cell_content'];

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
 * @param {object} object html object (textarea cell).
 * @param {WebSocket} conn connection to websocket.
 */
function onFocusInCell(object, conn) {
    let data = {
        update_type: "focusin",
        table_id: object.name.replace("cell_module_", ""),
        cell_id: object.id,
        cell_content: object.value
    };

    let prev_cell = document.getElementById("prev_cell").value;

    if (prev_cell !== object.id && prev_cell !== "") {
        onFocusOutCell(prev_cell, conn);
    }

    document.getElementById(object.id).style.border = "1px solid black";
    document.getElementById("focused_cell").value = object.id;
    document.getElementById("prev_cell").value = object.id;
    document.getElementById("focused_cell_content").value = object.value;
    document.getElementById("font-family-selector").value = object.style.fontFamily;
    document.getElementById("font-size-selector").value = object.style.fontSize.replace("pt", "");

    if (object.style.fontWeight === "bold") {
        document.getElementById("font-bold-button").style.border = "1px solid black";
    } else {
        document.getElementById("font-bold-button").style.border = "";
    }
    if (object.style.fontStyle === "italic") {
        document.getElementById("font-italic-button").style.border = "1px solid black";
    } else {
        document.getElementById("font-italic-button").style.border = "";
    }
    if (object.style.textDecoration === "underline") {
        document.getElementById("font-underline-button").style.border = "1px solid black";
    } else {
        document.getElementById("font-underline-button").style.border = "";
    }
    switch (object.style.textAlign) {
        case "left":
            document.getElementById("text-left-button").style.border = "1px solid black";
            document.getElementById("text-center-button").style.border = "";
            document.getElementById("text-right-button").style.border = "";
            break;
        case "center":
            document.getElementById("text-left-button").style.border = "";
            document.getElementById("text-center-button").style.border = "1px solid black";
            document.getElementById("text-right-button").style.border = "";
            break;
        case "right":
            document.getElementById("text-left-button").style.border = "";
            document.getElementById("text-center-button").style.border = "";
            document.getElementById("text-right-button").style.border = "1px solid black";
            break;
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
 * Send on WS unfocused cell id
 *
 * @param {string} cell_id cell name.
 * @param {WebSocket} conn connection to websocket.
 */
function onFocusOutCell(cell_id, conn) {
    let data = {
        update_type: "focusout",
        cell_id: cell_id
    };

    document.getElementById(cell_id).style.border = "";

    // Send information to other users
    conn.send(JSON.stringify(data));
}

/**
 * Update cell focus information for page and database.
 *
 * @param {Object} object html object.
 * @param {WebSocket} conn connection to websocket.
 */
function onChangeInputCell(object, conn) {
    try {
        if (object.value === "") {
            let prev_cell = document.getElementById("prev_cell").value;

            document.getElementById(prev_cell).style.border = "";
            document.getElementById("focused_cell").value = "";
            document.getElementById("prev_cell").value = "";
            document.getElementById("focused_cell_content").value = "";

            onFocusOutCell(prev_cell, conn);
        } else {
            let cell = document.getElementById(object.value);

            onFocusInCell(cell, conn);
        }
    } catch (e) {
        alert("Incorrect cell name");
    }

}

/**
 * Update cell information for page and database.
 *
 * @param {Object} object html object.
 * @param {WebSocket} conn connection to websocket.
 */
function onChangeInputContent(object, conn) {
    let cell_id = document.getElementById("focused_cell").value;
    let cell = document.getElementById(cell_id);
    cell.value = object.value;

    updateTablesCell(cell, conn);
}

/**
 * Update cells height for page and database.
 *
 * @param {object} object html object.
 * @param {WebSocket} conn connection to websocket.
 */
function updateHeight(object, conn) {
    let row_id = "row_".concat(object.value);

    let data = {
        update_type: "resize_h",
        table_id: object.name.replace("cell_module_", ""),
        name: row_id,
        height: document.getElementById(row_id).offsetHeight
    };

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

    let data = {
        update_type: "resize_w",
        table_id: object.name.replace("cell_module_", ""),
        name: col_id,
        width: document.getElementById(col_id).offsetWidth
    };

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
    let data = {
        update_type: "font",
        button_type: object.id,
        table_id: object.name.replace("cell_module_", ""),
        cell_id: document.getElementById("focused_cell").value,
        input_content: object.value
    };

    if (data['cell_id']) {
        switch (object.id) {
            case "font-family-selector":
                document.getElementById(data['cell_id']).style.fontFamily = object.value;
                break;
            case "font-size-selector":
                document.getElementById(data['cell_id']).style.fontSize = object.value.concat("pt");
                break;
            case "font-bold-button":
                if (document.getElementById(data['cell_id']).style.fontWeight !== "bold") {
                    document.getElementById(object.id).value = "bold";
                    document.getElementById(object.id).style.border = "1px solid black";
                    document.getElementById(data['cell_id']).style.fontWeight = "bold";
                    data['input_content'] = "bold";
                } else {
                    document.getElementById(object.id).value = "normal";
                    document.getElementById(object.id).style.border = "";
                    document.getElementById(data['cell_id']).style.fontWeight = "normal";
                    data['input_content'] = "normal";
                }
                break;
            case "font-italic-button":
                if (document.getElementById(data['cell_id']).style.fontStyle !== "italic") {
                    document.getElementById(object.id).value = "italic";
                    document.getElementById(object.id).style.border = "1px solid black";
                    document.getElementById(data['cell_id']).style.fontStyle = "italic";
                    data['input_content'] = "italic";
                } else {
                    document.getElementById(object.id).value = "normal";
                    document.getElementById(object.id).style.border = "";
                    document.getElementById(data['cell_id']).style.fontStyle = "normal";
                    data['input_content'] = "normal";
                }
                break;
            case "font-underline-button":
                if (document.getElementById(data['cell_id']).style.textDecoration !== "underline") {
                    document.getElementById(object.id).value = "underline";
                    document.getElementById(object.id).style.border = "1px solid black";
                    document.getElementById(data['cell_id']).style.textDecoration = "underline";
                    data['input_content'] = "underline";
                } else {
                    document.getElementById(object.id).value = "none";
                    document.getElementById(object.id).style.border = "";
                    document.getElementById(data['cell_id']).style.textDecoration = "none";
                    data['input_content'] = "none";
                }
                break;
            case "text-left-button":
                document.getElementById(object.id).style.border = "1px solid black";
                document.getElementById("text-center-button").style.border = "";
                document.getElementById("text-right-button").style.border = "";
                document.getElementById(data['cell_id']).style.textAlign = "left";
                data['input_content'] = "left";
                break;
            case "text-center-button":
                document.getElementById("text-left-button").style.border = "";
                document.getElementById(object.id).style.border = "1px solid black";
                document.getElementById("text-right-button").style.border = "";
                document.getElementById(data['cell_id']).style.textAlign = "center";
                data['input_content'] = "center";
                break;
            case "text-right-button":
                document.getElementById("text-left-button").style.border = "";
                document.getElementById("text-center-button").style.border = "";
                document.getElementById(object.id).style.border = "1px solid black";
                document.getElementById(data['cell_id']).style.textAlign = "right";
                data['input_content'] = "right";
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

/**
 * Save cell change information.
 *
 * @param {object} object html object.
 */
function saveCellHistory(object) {
    let data = {
        update_type: "history",
        table_id: object.name.replace("cell_module_", ""),
        cell_id: object.id,
        cell_content: object.value
    };

    // Send information to updatecell.php for updating database
    $.ajax({
        method: "POST",
        url: "savehistory.php",
        data: data
    });
}