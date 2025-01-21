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
 * Update cell information for page and database.
 *
 * @param {object} object html object.
 */
function updateTablesCell(object) {
    let data = {
        update_type: "input",
        table_id: document.getElementById('main_table').getAttribute('data-moduleinstance'),
        sheet_id: document.getElementById('main_table').getAttribute('data-sheet'),
        cell_id: object.id,
        cell_content: object.value
    };

    if(document.getElementById('select_cell_visibility')){
        data['cell_visibility'] = document.getElementById('select_cell_visibility').value;
    }
    else{
        data['cell_visibility'] = localStorage[object.id];
    }

    document.getElementById("focused_cell_content").value = data['cell_content'];

    // Send information to other users
    if(localStorage.socket !== "false") {
        console.log("________upd")
        console.log(data)
        console.log("________upd")
        console.log(localStorage[object.id])
        // Send information to other users
        socket.emit('send', {
            room: document.getElementById('main_table').getAttribute('data-moduleinstance'),
            message: data
        });
    }

    // Send information to update_cell.php for updating database
    $.ajax({
        method: "POST",
        url: "update_cell.php",
        data: data
    });
}

/**
 * Show dropdown list of users.
 *
 * @param {object} object html object.
 */
function onInputSearch(object) {
    let children;

    switch (object.getAttribute('data-attach-to')){
        case 'user':{
            children = document.querySelectorAll("#dropdown-content-users p");

            break;
        }
        case 'group':{
            children = document.querySelectorAll("#dropdown-content-groups p");

            break;
        }
    }

    let searchstr = object.value.toLowerCase();

    for (let i = 0; i < children.length; i++) {
        let value = children[i].querySelector("label").innerHTML.toLowerCase();

        if (value.includes(searchstr)) {
            children[i].style.display = 'block';
        }
        else {
            children[i].style.display = 'none';
        }
    }

}

/**
 * Show input for search students to attach.
 *
 * @param {object} object html object.
 */
function onclickAttach(object){
    let main = document.getElementById('main_table');

    let dropdown_block;
    let dopdown_button;
    let cellboxes;
    let submit_btns;

    switch (object.getAttribute('data-attach-to')){
        case 'user':{
            dropdown_block = document.getElementById('dropdown_attach_students');
            dopdown_button = document.getElementById('attach_cell_to_users');
            cellboxes = document.getElementsByClassName('m-dropdown-students-cell');
            submit_btns = 'submit_user_btns';

            if(document.getElementById('first_cell-students').value){
                let first_sell = document.getElementById('first_cell-students');
                document.getElementById(first_sell.value).style.border = "";
                //first_sell.value = null;
            }
            if(document.getElementById('last_cell-students').value){
                let last_sell = document.getElementById('last_cell-students');
                document.getElementById(last_sell.value).style.border = "";
                //last_sell.value = null;
            }

            break;
        }
        case 'group':{
            dropdown_block = document.getElementById('dropdown_attach_groups');
            dopdown_button = document.getElementById('attach_cell_to_groups');
            cellboxes = document.getElementsByClassName('m-dropdown-groups-cell');
            submit_btns = 'submit_group_btns';

            if(document.getElementById('first_cell-groups').value){
                let first_sell = document.getElementById('first_cell-groups');
                document.getElementById(first_sell.value).style.border = "";
                //first_sell.value = null;
            }
            if(document.getElementById('last_cell-groups').value){
                let last_sell = document.getElementById('last_cell-groups');
                document.getElementById(last_sell.value).style.border = "";
                //last_sell.value = null;
            }

            break;
        }
    }

    if(dropdown_block.style.display === 'none'){
        object.value = 'on';
        cellboxes[0].style.display = 'inline-block';
        cellboxes[1].style.display = 'inline-block';
        dropdown_block.style.display = 'inline-block';
        dopdown_button.style.border = "1px solid black";
        Array.prototype.forEach.call(document.getElementsByClassName('m-tables-toolbar-block'), function (element, idx){
            element.classList.add('disabled');
        })
        document.getElementById('input_bar').classList.add('disabled');

        try{
            onFocusOutCell(document.getElementById('prev_cell').value)

            let data = {
                update_type: "focusout",
                table_id: main.getAttribute('data-moduleinstance'),
                sheet_id: main.getAttribute('data-sheet'),
                cell_id: null
            };

            $.ajax({
                method: "POST",
                url: "update_cell_focus.php",
                data: data
            });
        }
        catch(e){
            console.log('No focused cells')
            console.log(e)
        }
    }
    else{
        object.value = 'off';
        dropdown_block.style.display = 'none';
        dopdown_button.style.border = "";
        cellboxes[0].style.display = 'none';
        cellboxes[1].style.display = 'none';
        document.getElementById(submit_btns).style.display = "none";
        Array.prototype.forEach.call(document.getElementsByClassName('m-tables-toolbar-block'), function (element, idx){
            element.classList.remove('disabled');
        })
        document.getElementById('input_bar').classList.remove('disabled');

    }
}

/**
 * Outputs student names to the selection window
 *
 * @param {object} object html object.
 */
function onclickCheckboxAttach(object){
    let label_display;
    let checked_students;

    switch (object.getAttribute('data-attach-to')){
        case 'user':{
            label_display = document.getElementById('display_selected_students');
            checked_students = document.querySelectorAll('.m-user-check:checked');

            break;
        }
        case 'group':{
            label_display = document.getElementById('display_selected_groups');
            checked_students = document.querySelectorAll('.m-group-check:checked');

            break;
        }
    }

    if(checked_students.length > 1){
        label_display.value = checked_students[0].getAttribute('data-attach-name').concat(" +", checked_students.length-1);
    }
    else if(checked_students.length === 0){
        label_display.value = null;
    }
    else{
        label_display.value = checked_students[0].getAttribute('data-attach-name');
    }
}

/**
 * Submit the selected cells for the attachment.
 *
 * @param {object} object html object.
 * @param {string[]} messages array of messages.
 */
function onclickSubmitAttach(object, messages){
    let selected;
    let first_cell_input_id;
    let last_cell_input_id;
    let checked;
    let submit_btns;
    let attach_cell_to;
    let update_type;

    switch (object.getAttribute('data-attach-to')){
        case 'user':{
            selected = document.getElementById('display_selected_students');
            checked = document.querySelectorAll('.m-user-check:checked');
            first_cell_input_id = 'first_cell-students';
            last_cell_input_id = 'last_cell-students';
            submit_btns = 'submit_user_btns';
            attach_cell_to = 'attach_cell_to_users';
            update_type = 'students';

            break;
        }
        case 'group':{
            selected = document.getElementById('display_selected_groups');
            checked = document.querySelectorAll('.m-group-check:checked');
            first_cell_input_id = 'first_cell-groups';
            last_cell_input_id = 'last_cell-groups';
            submit_btns = 'submit_group_btns';
            attach_cell_to = 'attach_cell_to_groups';
            update_type = 'groups';

            break;
        }
    }

    let first_cell_input = document.getElementById(first_cell_input_id);
    let last_cell_input = document.getElementById(last_cell_input_id);

    if(selected.value !== ""){
        let module_id = document.getElementById('main_table').getAttribute('data-moduleinstance');
        let sheet_id = document.getElementById('main_table').getAttribute('data-sheet');

        object.setAttribute('data-table', module_id);
        object.setAttribute('data-sheet', sheet_id);
        object.setAttribute('data-first-cell', first_cell_input_id);
        object.setAttribute('data-last-cell', last_cell_input_id);
        object.setAttribute('data-update-type', update_type);

        for(let i = 0; i < checked.length; i ++){
            object.setAttribute('data-user', checked[i].value);

            attachCells(object, messages)
        }

        let first_cell = document.getElementById(first_cell_input.value);
        first_cell.style.border = "";
        let last_cell = document.getElementById(last_cell_input.value);
        last_cell.style.border = "";

        first_cell_input.value = null;
        last_cell_input.value = null;

        document.getElementById(submit_btns).style.display = "none";

        onclickAttach(document.getElementById(attach_cell_to))
    }
    else{
        alert(messages[0])
    }
}

/**
 * Cancels the selected cells for the attachment.
 *
 * @param {object} object html object.
 *
 */
function onclickCanselAttach(object){
    let first_cell_input;
    let last_cell_input;
    let submit_btns;

    switch (object.getAttribute('data-attach-to')){
        case 'user':{
            first_cell_input = document.getElementById('first_cell-students');
            last_cell_input = document.getElementById('last_cell-students');
            submit_btns = 'submit_user_btns';

            break;
        }
        case 'group':{
            first_cell_input = document.getElementById('first_cell-groups');
            last_cell_input = document.getElementById('last_cell-groups');
            submit_btns = 'submit_group_btns';

            break;
        }
    }

    let first_cell = document.getElementById(first_cell_input.value);
    first_cell.style.border = "";
    first_cell_input.value = "";

    if(last_cell_input.value !== ""){
        let last_cell = document.getElementById(last_cell_input.value);
        last_cell.style.border = "";
        last_cell_input.value = ""
    }
    document.getElementById(submit_btns).style.display = "none";
}

/**
 * On focus cell when attach button active.
 *
 * @param {object} object html object (textarea cell).
 */
function onFocusInCellAttach(object){
    if(document.getElementById('attach_cell_to_users').value === 'on'){
        let first_cell = document.getElementById('first_cell-students');
        let last_cell = document.getElementById('last_cell-students');

        if(first_cell.value === "" && object.id === last_cell.value){
            first_cell.value = object.id;
            last_cell.value = "";
            object.style.border = "1px solid #27a7d8";
        }
        else if(first_cell.value === ""){
            first_cell.value = object.id;
            object.style.border = "1px solid #27a7d8";
        }
        else if(last_cell.value === "" && first_cell.value === object.id){
            last_cell.value = object.id;
            object.style.border = "1px solid #ff9a00";
            object.style.borderLeftColor = "#27a7d8";
            object.style.borderTopColor = "#27a7d8";
        }
        else if(last_cell.value === ""){
            last_cell.value = object.id;
            object.style.border = "1px solid #ff9a00";
        }
        else if(first_cell.value === last_cell.value && first_cell.value !== object.id){
            document.getElementById(last_cell.value).style.border = "1px solid #27a7d8";
            last_cell.value = object.id;
            object.style.border = "1px solid #ff9a00";
        }
        else if(first_cell.value === object.id){
            object.style.border = null;
            first_cell.value = "";
        }
        else{
            document.getElementById(last_cell.value).style.border = null;
            last_cell.value = object.id;
            object.style.border = "1px solid #ff9a00";
        }

        if(first_cell.value !== "" && last_cell.value !== ""){
            document.getElementById('submit_user_btns').style.display = "inline-block";
        }
        else{
            document.getElementById('submit_user_btns').style.display = "none";
        }

        document.activeElement.blur();
    }
    else if(document.getElementById('attach_cell_to_groups').value === 'on'){
        let first_cell = document.getElementById('first_cell-groups');
        let last_cell = document.getElementById('last_cell-groups');

        if(first_cell.value === "" && object.id === last_cell.value){
            first_cell.value = object.id;
            last_cell.value = "";
            object.style.border = "1px solid #27a7d8";
        }
        else if(first_cell.value === ""){
            first_cell.value = object.id;
            object.style.border = "1px solid #27a7d8";
        }
        else if(last_cell.value === "" && first_cell.value === object.id){
            last_cell.value = object.id;
            object.style.border = "1px solid #ff9a00";
            object.style.borderLeftColor = "#27a7d8";
            object.style.borderTopColor = "#27a7d8";
        }
        else if(last_cell.value === ""){
            last_cell.value = object.id;
            object.style.border = "1px solid #ff9a00";
        }
        else if(first_cell.value === last_cell.value && first_cell.value !== object.id){
            document.getElementById(last_cell.value).style.border = "1px solid #27a7d8";
            last_cell.value = object.id;
            object.style.border = "1px solid #ff9a00";
        }
        else if(first_cell.value === object.id){
            object.style.border = null;
            first_cell.value = "";
        }
        else{
            document.getElementById(last_cell.value).style.border = null;
            last_cell.value = object.id;
            object.style.border = "1px solid #ff9a00";
        }

        if(first_cell.value !== "" && last_cell.value !== ""){
            document.getElementById('submit_group_btns').style.display = "inline-block";
        }
        else{
            document.getElementById('submit_group_btns').style.display = "none";
        }

        document.activeElement.blur();
    }
    else{
        onFocusInCellFocus(object)

        document.getElementById('grade_block').classList.remove('disabled');
        document.getElementById('visibility_block').classList.remove('disabled');
        document.getElementById('check_grade').classList.remove('m-tables-show');
    }
}

/**
 * On focus cell when attach button deactive.
 *
 * @param {object} object html object (textarea cell).
 */
function onFocusInCellFocus(object){
    let data = {
        update_type: "focusin",
        table_id: document.getElementById('main_table').getAttribute('data-moduleinstance'),
        sheet_id: document.getElementById('main_table').getAttribute('data-sheet'),
        cell_id: object.id,
        cell_content: object.value
    };

    let prev_cell = document.getElementById("prev_cell").value;

    if (prev_cell !== object.id && prev_cell !== "") {
        onFocusOutCell(prev_cell);
    }

    document.getElementById(object.id).style.border = "1px solid black";
    document.getElementById("focused_cell").value = object.id;
    document.getElementById("prev_cell").value = object.id;
    document.getElementById("focused_cell_content").value = object.value;
    document.getElementById("font-family-selector").value = object.style.fontFamily;
    document.getElementById("font-size-selector").value = object.style.fontSize.replace("pt", "");

    if(document.getElementById('select_cell_visibility')){
        document.getElementById("select_cell_visibility").value = object.getAttribute('data-visibility');
    }
    else{
        document.getElementById("select_cell_visibility").value = localStorage[object.id];
    }

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
    if(localStorage.socket !== "false") {
        socket.emit('send', {
            room: document.getElementById('main_table').getAttribute('data-moduleinstance'),
            message: data
        });
    }

    // Send information to update_cell_focus.php for updating database
    $.ajax({
        method: "POST",
        url: "update_cell_focus.php",
        data: data
    });
}

/**
 * Update cell focus information for page and database.
 *
 * @param {object} object html object (textarea cell).
 */
function onFocusInCell(object) {
    if(document.getElementById('attach_cell_to_users')){
        onFocusInCellAttach(object)
    }
    else{
        onFocusInCellFocus(object)
    }
}

/**
 * Send on WS unfocused cell id
 *
 * @param {string} cell_id cell name.
 */
function onFocusOutCell(cell_id) {
    let data = {
        update_type: "focusout",
        cell_id: cell_id
    };

    document.getElementById(cell_id).style.border = "";
    document.getElementById("text-left-button").style.border = "";
    document.getElementById("text-center-button").style.border = "";
    document.getElementById("text-right-button").style.border = "";
    document.getElementById("font-bold-button").style.border = "";
    document.getElementById("font-italic-button").style.border = "";
    document.getElementById("font-underline-button").style.border = "";
    document.getElementById("focused_cell").value = "";
    document.getElementById("focused_cell_content").value = "";
    if(document.getElementById('main_table').getAttribute('data-user-role') === 'teacher'){
        document.getElementById('grade_block').classList.add('disabled');
        document.getElementById('visibility_block').classList.add('disabled');
    }

    // Send information to other users
    if(localStorage.socket !== "false") {
        socket.emit('send', {
            room: document.getElementById('main_table').getAttribute('data-moduleinstance'),
            message: data
        });
    }
}

/**
 * Update cell focus information for page and database.
 *
 * @param {Object} object html object.
 */
function onChangeInputCell(object) {
    try {
        if (object.value === "") {
            let prev_cell = document.getElementById("prev_cell").value;

            document.getElementById(prev_cell).style.border = "";
            document.getElementById("focused_cell").value = "";
            document.getElementById("prev_cell").value = "";
            document.getElementById("focused_cell_content").value = "";

            onFocusOutCell(prev_cell);
        } else {
            let cell = document.getElementById(object.value);

            onFocusInCellFocus(cell);
        }
    } catch (e) {
        alert("Incorrect cell name");
    }

}

/**
 * Update cell information for page and database.
 *
 * @param {Object} object html object.
 */
function onChangeInputContent(object) {
    let cell_id = document.getElementById("focused_cell").value;
    let cell = document.getElementById(cell_id);
    cell.value = object.value;

    updateTablesCell(cell);
}

/**
 * Update cells height for page and database.
 *
 * @param {object} object html object.
 */
function updateHeight(object) {
    let row_id = "row_".concat(object.value);

    let data = {
        update_type: "resize_h",
        table_id: document.getElementById('main_table').getAttribute('data-moduleinstance'),
        sheet_id: document.getElementById('main_table').getAttribute('data-sheet'),
        name: row_id,
        height: document.getElementById(row_id).offsetHeight
    };

    // Send information to other users
    if(localStorage.socket !== "false") {
        socket.emit('send', {
            room: document.getElementById('main_table').getAttribute('data-moduleinstance'),
            message: data
        });
    }

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
 */
function updateWidth(object) {
    let col_id = "col_".concat(object.value);

    let data = {
        update_type: "resize_w",
        table_id: document.getElementById('main_table').getAttribute('data-moduleinstance'),
        sheet_id: document.getElementById('main_table').getAttribute('data-sheet'),
        name: col_id,
        width: document.getElementById(col_id).offsetWidth
    };

    // Send information to other users
    if(localStorage.socket !== "false") {
        socket.emit('send', {
            room: document.getElementById('main_table').getAttribute('data-moduleinstance'),
            message: data
        });
    }

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
 */
function updateFont(object) {
    let data = {
        update_type: "font",
        button_type: object.id,
        table_id: document.getElementById('main_table').getAttribute('data-moduleinstance'),
        sheet_id: document.getElementById('main_table').getAttribute('data-sheet'),
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
        if(localStorage.socket !== "false") {
            socket.emit('send', {
                room: document.getElementById('main_table').getAttribute('data-moduleinstance'),
                message: data
            });
        }

        // Send information to update_font.php for updating database
        $.ajax({
            method: "POST",
            url: "update_font.php",
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
        table_id: document.getElementById('main_table').getAttribute('data-moduleinstance'),
        sheet_id: document.getElementById('main_table').getAttribute('data-sheet'),
        cell_id: object.id,
        cell_content: object.value
    };

    // Send information to save_history.php for updating database
    $.ajax({
        method: "POST",
        url: "save_history.php",
        data: data
    });
}

/**
 * Create new sheet for the table.
 *
 * @param {object} object html object.
 */
function createSheet(object){
    let data = {
        update_type: "add_sheet",
        table_id: object.id.replace('add_sheet_for_module_', '')
    };

    let new_sheet = document.createElement("BUTTON");
    new_sheet.setAttribute("class", "m-tables-sheet-select");
    new_sheet.innerHTML = "<img src=\"pix/refreshdouble.png\" alt=\"refresh\">";
    document.querySelector("#sheet_bar").appendChild(new_sheet);

    //Send information to create_sheet.php for updating database
    $.ajax({
        method: "POST",
        url: "create_sheet.php",
        data: data
    });

    setTimeout(function(){ location.reload(); }, 500);
}

/**
 * Delete sheet from the table.
 *
 */
function deleteSheet(){
    let data = {
        update_type: "delete_sheet",
        table_id: document.getElementById('main_table').getAttribute('data-moduleinstance'),
        sheet_id: document.getElementById('main_table').getAttribute('data-sheet')
    };

    //Send information to create_sheet.php for updating database
    $.ajax({
        method: "POST",
        url: "create_sheet.php",
        data: data
    });
}

/**
 * Grade attached cells
 *
 */
function gradeCell(){
    let main = document.getElementById('main_table');

    let data = {
        sheet_id: main.getAttribute('data-sheet'),
        table_id: main.getAttribute('data-moduleinstance'),
        cell_name: document.getElementById('focused_cell').value,
        user_id: document.getElementById('select_user_grade').value,
        grade: document.getElementById('input_grade').value
    };

    if(document.getElementById("feedback_block").style.display !== "none"){
        data["feedback"] = document.getElementById("feedback_textarea").value;
    }

    $.ajax({
        method: "POST",
        url: "grade_cell.php",
        data: data
    });

    document.getElementById('check_grade').classList.add('m-tables-show');
}

/**
 * Hide element
 *
 */
function onchangeInputGrade(){
    document.getElementById('check_grade').classList.remove('m-tables-show');
}

/**
 * Set max and min range
 *
 */
function oninputGrade(object){
    if(object.value > 100){
        object.value = 100;
    }
    else if(object.value < 0){
        object.value = 0;
    }
}

/**
 * Show feedback textarea
 *
 * @param {object} object html object.
 */
function showFeedback(object){
    let feedback_block = document.getElementById("feedback_block");

    if(feedback_block.style.display === "none"){
        feedback_block.style.display = "inline-block";
        object.classList.remove("fa-plus");
        object.classList.add("fa-minus");
    }
    else{
        feedback_block.style.display = "none";
        object.classList.remove("fa-minus");
        object.classList.add("fa-plus");
    }
}

/**
 * Change cell visibility.
 *
 */
function onChangeSelectVisibility(){
    let data = {
        update_type: "visibility",
        table_id: document.getElementById('main_table').getAttribute('data-moduleinstance'),
        sheet_id: document.getElementById('main_table').getAttribute('data-sheet'),
        cell_id: document.getElementById('focused_cell').value,
        cell_visibility: document.getElementById('select_cell_visibility').value
    };

    document.getElementById(data['cell_id']).setAttribute('data-visibility', data['cell_visibility'])

    // Send information to other users
    if(localStorage.socket !== "false") {
        socket.emit('send', {
            room: document.getElementById('main_table').getAttribute('data-moduleinstance'),
            message: data
        });
    }

    //Send information to create_sheet.php for updating database
    $.ajax({
        method: "POST",
        url: "update_visibility.php",
        data: data
    });
}

/**
 * Update attached cells database.
 *
 * @param {object} object html object.
 * @param {string[]} messages array of messages.
 */
function attachCells(object, messages){

    let data = {
        update_type: object.getAttribute('data-update-type'),
        user_id: object.getAttribute('data-user'),
        table_id: object.getAttribute('data-table'),
        sheet_id: object.getAttribute('data-sheet')
    };

    let first_cell = document.getElementById(object.getAttribute("data-first-cell"));
    let last_cell = document.getElementById(object.getAttribute("data-last-cell"));
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

        data['update_type'] = "attach_cells";
        if(localStorage.socket !== "false") {
            socket.emit('send', {room: data['table_id'], message: data});
        }
    }
    else{
        alert(messages[1]);
    }
}

// module.exports = {onclickSubmitAttach,
//     onclickCanselAttach,
//     onclickAttach,
//     updateTablesCell,
//     onclickCheckboxAttach,
//     onFocusInCellAttach,
//     onFocusInCellFocus,
//     onChangeInputCell,
//     onChangeInputContent,
//     updateFont,
//     gradeCell,
//     onchangeInputGrade,
//     oninputGrade,
//     showFeedback,
//     onChangeSelectVisibility
// };