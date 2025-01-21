const messages = ["Message 1", "Message 2"]
document.body.innerHTML =
    '<div class="m-tables-toolbar"> ' +
        '<div class="m-tables-toolbar-block">' +
            '<form class="m-tables-toolbar-load-up" method="post" action="upload_from_xlsx.php?id=1">'+
                '<button class="m-tables-toolbar-button" type="submit">'+
                    '<img class="m-tables-toolbar-img" src="pix/upload.png" alt="bold">'+
                '</button>'+
            '</form>'+
            '<form class="m-tables-toolbar-load-down" method="post" action="upload_from_activity.php?id=1">'+
                '<button class="m-tables-toolbar-button" type="submit">'+
                    '<img class="m-tables-toolbar-img" src="pix/upload.png" alt="bold">'+
                '</button>'+
            '</form>'+
        '</div>'+
        '<div id="toolbar_font" class="m-tables-toolbar-block">'+
        '<div class="m-tables-toolbar-font-up">'+
            '<input class="m-tables-font-family-selector"'+
                ' id="font-family-selector"'+
                ' title=""'+
                ' name="font-family-selector"'+
                ' type="text"'+
                ' value="Calibri"'+
                ' autocomplete="off"'+
                ' onchange="updateFont(this)"'+
                ' list="fonts"/>'+
            '<datalist id="fonts">' +
                '<option value="Calibri">Calibri</option>'+
            '</datalist>\n' +
            '<input class="m-tables-font-size-selector" \n' +
                'id="font-size-selector" \n' +
                'title="\' . get_string(\'font_size_title\', \'mod_tables\') . \'" \n' +
                'onchange="updateFont(this)" \n' +
                'type="number" min="1" max="409" value="11" xmlns="http://www.w3.org/1999/html"/>\n' +
        '</div>\n' +
            '<div class="m-tables-toolbar-font-down">\n' +
                '<button class="m-tables-toolbar-button" id="font-bold-button" onclick="updateFont(this)" \n' +
                '   title="\'.get_string(\'font_bold_title\', \'mod_tables\').\'">\n' +
                    '<img class="m-tables-toolbar-img" src="pix/bold.png" alt="bold">\n' +
                '</button>\n' +
                '<button class="m-tables-toolbar-button" id="font-italic-button" onclick="updateFont(this)" \n' +
                    'title="\'.get_string(\'font_italic_title\', \'mod_tables\').\'">\n' +
                    '<img class="m-tables-toolbar-img" src="pix/italic.png" alt="italic">\n' +
                '</button>\n' +
                '<button class="m-tables-toolbar-button" id="font-underline-button" onclick="updateFont(this)" \n' +
                    'title="\'.get_string(\'font_underline_title\', \'mod_tables\').\'">\n' +
                    '<img class="m-tables-toolbar-img" src="pix/underline.png" alt="underline">\n' +
                '</button>\n' +
            '</div>\n' +
        '</div>\n' +
        '<div id="toolbar_align" class="m-tables-toolbar-block">\n' +
            '<div class="m-tables-toolbar-align">\n' +
                '<button class="m-tables-toolbar-button" id="text-left-button" onclick="updateFont(this)" \n' +
                    'title="\'.get_string(\'text_align_left_title\', \'mod_tables\').\'" >\n' +
                    '<img class="m-tables-toolbar-img" src="pix/textalignleft.png" alt="left">\n' +
                '</button>\n' +
                '<button class="m-tables-toolbar-button" id="text-center-button" onclick="updateFont(this)" \n' +
                    'title="\'.get_string(\'text_align_center_title\', \'mod_tables\').\'" >\n' +
                    '<img class="m-tables-toolbar-img" src="pix/textaligncenter.png" alt="center">\n' +
                '</button>\n' +
                '<button class="m-tables-toolbar-button" id="text-right-button" onclick="updateFont(this)" \n' +
                    'title="\'.get_string(\'text_align_right_title\', \'mod_tables\').\'" >\n' +
                    '<img class="m-tables-toolbar-img" src="pix/textalignright.png" alt="right">\n' +
                '</button>\n' +
            '</div>\n' +
            '<div class="m-tables-toolbar-align">\n' +
                '\n' +
            '</div>\n' +
        '</div>\n' +
        '<div class="m-tables-toolbar-block-attach">\n' +
            '<div class="m-tables-toolbar-attach">\n' +
                '<button class="m-tables-toolbar-button" id="attach_cell_to_users" onclick="onclickAttach(this)"\n' +
                    ' title="" value="off" data-attach-to="user">\n' +
                    '<img class="m-tables-toolbar-img" src="pix/user.png" alt="user">\n' +
                '</button>\n' +
                '<div class="m-dropdown" id="dropdown_attach_students" style="display:none;" >\n' +
                    '<div class="m-dropdown-display">\n' +
                        '<input class="m-dropdown-checked" type="text" id="display_selected_students" value="user 2">\n' +
                        '<input class="m-dropdown-search" autocomplete="off"  type="text" oninput="onInputSearch(this)" id="search_students" data-attach-to="user">\n' +
                    '</div>\n' +
                    '<div class="m-dropdown-content" id="dropdown-content-users">\n' +
                        '<p>\n' +
                            '<input id="chechbox_attach_users" class="m-user-check" data-attach-to="user" data-attach-name="user 1" value="user 1" type="checkbox" onclick="onclickCheckboxAttach(this)">\n' +
                            '<label class="m-tables-user-label">user 1</label>\n' +
                        '</p>\n' +
                    '</div>\n' +
                '</div>\n' +
                '<input class="m-dropdown-students-cell" id="first_cell-students" type="text" value="A1" readonly>\n' +
                '<input class="m-dropdown-students-cell" id="last_cell-students" type="text" value="B2" readonly>\n' +
                '<div id="submit_user_btns" style="display: none">\n' +
                    '<span class="m-tables-green-btn">\n' +
                        '<i id="submit_attach_users" class="fa fa-check" data-attach-to="user" onclick="onclickSubmitAttach(this, messages)" ></i>\n' +
                    '</span>\n' +
                    '<span class="m-tables-red-btn">\n' +
                        '<i id="cansel_attach_users" class="fa fa-times" data-attach-to="user" onclick="onclickCanselAttach(this)" ></i>\n' +
                    '</span>\n' +
                '</div>\n' +
            '</div>\n' +
            '<div class="m-tables-toolbar-attach">\n' +
                '<button class="m-tables-toolbar-button" id="attach_cell_to_groups" onclick="onclickAttach(this)"\n' +
                    ' title="" value="off" data-attach-to="group">\n' +
                    '<img class="m-tables-toolbar-img" src="pix/group.png" alt="group">\n' +
                '</button>\n' +
                '<div class="m-dropdown" id="dropdown_attach_groups" style="display:none;" >\n' +
                    '<div class="m-dropdown-display">\n' +
                        '<input class="m-dropdown-checked" type="text" id="display_selected_groups" value="group 2">\n' +
                        '<input class="m-dropdown-search" autocomplete="off"  type="text" oninput="onInputSearch(this)" id="search_groups" data-attach-to="group">\n' +
                    '</div>\n' +
                    '<div class="m-dropdown-content" id="dropdown-content-groups">\n' +
                        '<p>\n' +
                            '<input id="chechbox_attach_groups" class="m-group-check" data-attach-to="group" data-attach-name="group 1" value="groupid" type="checkbox" onclick="onclickCheckboxAttach(this)">\n' +
                            '<label class="m-tables-user-label">Group 1</label>\n' +
                        '</p>\n' +
                    '</div>\n' +
                '</div>\n' +
                '<input class="m-dropdown-groups-cell" id="first_cell-groups" type="text" value="A1" readonly>\n' +
                '<input class="m-dropdown-groups-cell" id="last_cell-groups" type="text" value="B2" readonly>\n' +
                '<div id="submit_group_btns" style="display: none">\n' +
                    '<span class="m-tables-green-btn">\n' +
                        '<i id="submit_attach_groups" class="fa fa-check" data-attach-to="group" onclick="onclickSubmitAttach(this, messages)" ></i>\n' +
                    '</span>\n' +
                    '<span class="m-tables-red-btn">\n' +
                        '<i id="cansel_attach_groups" class="fa fa-times" data-attach-to="group" onclick="onclickCanselAttach(this)" ></i>\n' +
                    '</span>\n' +
                '</div>\n' +
            '</div>\n' +
        '</div>\n' +
        '<div id="grade_block" class="m-tables-toolbar-block disabled">\n' +
            '<div class="m-tables-toolbar-grade-up">\n' +
                '<select id="select_user_grade">\n' +
                    '<option value="userid">\n' +
                        'user 1\n' +
                    '</option>'+
                '</select>\n' +
            '</div>\n' +
            '<div class="m-tables-toolbar-grade-down">\n' +
                '<input id="input_grade" type="number" min="0" max="100" oninput="oninputGrade(this)" onchange="onchangeInputGrade()">\n' +
                '<i class="fa fa-check m-tables-check-grade" id="check_grade"></i>\n' +
                '<button class="btn btn-primary m-tables-toolbar-grade-button" onclick="gradeCell()">Оценить</button>\n' +
                '<span class="m-tables-blue-btn">\n' +
                    '<i class="fa fa-plus" id="show_feedback_btn" onclick="showFeedback(this)"></i>\n' +
                '</span>\n' +
            '</div>\n' +
        '</div>\n' +
        '<div id="feedback_block" class="m-tables-toolbar-block m-tables-toolbar-grade-textarea" style="display: none">\n' +
            '<textarea id="feedback_textarea"></textarea>\n' +
        '</div>\n' +
        '<div class="m-tables-toolbar-block">\n' +
            '<div id="visibility_block" class="m-tables-toolbar-visible-up disabled">\n' +
                '<select id="select_cell_visibility" onchange="onChangeSelectVisibility()">\n' +
                    '<option value="teacher">teacher</option>\n' +
                    '<option value="all">all</option>\n' +
                    '<option value="user">user</option>\n' +
                    '<option value="group">group</option>\n' +
                '</select>\n' +
            '</div> \n' +
        '</div> \n' +
    '</div> \n' +
    '<div class="m-tables-input-bar" id="input_bar">\n' +
        '<input style="display: none"\n' +
            'type="text" id="prev_cell">\n' +
        '<input class="m-tables-focused-cell" \n' +
            'type="text" id="focused_cell" \n' +
            'onchange="onChangeInputCell(this)" />\n' +
        '<input class="m-tables-focused-cell-content" \n' +
            'type="text" \n' +
            'onchange = "onChangeInputContent(this)" \n' +
            'id="focused_cell_content" />\n' +
    '</div>'+
    '<div class="m-tables-settings">\n' +
        '<table id="main_table" data-id="1" data-moduleinstance="1" data-sheet="1" data-user-role="teacher" data-user="1">\n' +
            '<thead>\n' +
                '<tr>\n' +
                    '<td></td>\n' +
                    '<td>\n' +
                        '<input class="resizable-column" \n' +
                            'type="text" \n' +
                            'id="col_A" \n' +
                            'style="width: 300px;" \n' +
                            'value="A" readonly />\n' +
                    '</td>\n' +
                '</tr>\n' +
            '</thead>\n' +
            '<tbody>\n' +
                '<tr>\n' +
                    '<td>\n' +
                        '<input class="resizable-row" \n' +
                            'type="text" \n' +
                            'id="row_1" \n' +
                            'style="height:50px;" \n' +
                            'value="1" readonly />\n' +
                    '</td>\n' +
                    '<td>\n' +
                        '<textarea name="cell_textarea" \n' +
                            'disabled \n' +
                            'data-visibility = "all" \n' +
                            'data-attached="false" \n' +
                            'style="\n' +
                                'font-family: Arial; \n' +
                                'font-size: 14pt; \n' +
                                'font-weight: bold; \n' +
                                'font-style: italic; \n' +
                                'text-decoration: underline; \n' +
                                'text-align: center; "\n' +
                            'onfocus="onFocusInCell(this)" \n' +
                            'onchange="saveCellHistory(this)" \n' +
                            'oninput="updateTablesCell(this)" \n' +
                            'id="A1">' +
                        'Hello' +
                        '</textarea>\n' +
                    '</td>\n' +
                    '<td>\n' +
                        '<textarea name="cell_textarea" \n' +
                            'disabled \n' +
                            'data-visibility = "all" \n' +
                            'data-attached="false" \n' +
                            'style="\n' +
                                'font-family: Calibri; \n' +
                                'font-size: 10pt; \n' +
                                'font-weight: normal; \n' +
                                'font-style: normal; \n' +
                                'text-decoration: none; \n' +
                                'text-align: left; "\n' +
                            'onfocus="onFocusInCell(this)" \n' +
                            'onchange="saveCellHistory(this)" \n' +
                            'oninput="updateTablesCell(this)" \n' +
                            'id="B2">\n' +
                        'World\n' +
                        '</textarea>\n' +
                    '</td>\n' +
                '</tr>\n' +
            '</tbody>\n' +
        '</table>\n' +
        '<ul class="m-sheet-custom-menu" id="custom_menu_1">\n' +
            '<li id="delete_sheet" data-action = "delete_sheet" >Delete</li>\n' +
        '</ul>\n' +
        '<form method="post">\n' +
            '<div class="m-tables-sheet-bar" id="sheet_bar">\n' +
                '<button class="m-tables-sheet-select" type="submit" name="sheet" value="1" id="sheet_1" >\n' +
                    'Sheet 1 \n' +
                '</button>\n' +
            '</div>\n' +
            '<span class="m-tables-sheet-add">\n' +
                '<i class="fa fa-plus" id="add_sheet_for_module_1" onclick="createSheet(this)"></i>\n' +
            '</span>\n' +
        '</form>\n' +
        '<button class="btn btn-primary" style="border-radius: 5px" onclick="deleteSheet()">Delete sheet</button>\n' +
    '</div>\n' +

    ' <script> \n' +
    '    let messages = ["Message 1", "Message 2"] \n' +
    '</script>\n';

describe('tables_tests', function() {
    const func = require('./update_data.js');

    test('Attach users test', () => {
        func.onclickAttach(document.getElementById('attach_cell_to_users'))
        expect(document.getElementById('dropdown_attach_students').style.display).toBe("inline-block");
        expect(document.getElementById('attach_cell_to_users').style.border).toBe("1px solid black");
        expect(document.getElementById('input_bar').classList).toContain("disabled");

        Array.prototype.forEach.call(document.getElementsByClassName('m-tables-toolbar-block'), function (element, idx){
            expect(element.classList).toContain("disabled")
        })

        func.onclickAttach(document.getElementById('attach_cell_to_users'))
        expect(document.getElementById('attach_cell_to_users').style.border).toBe("");

        Array.prototype.forEach.call(document.getElementsByClassName('m-tables-toolbar-block'), function (element, idx){
            expect(element.classList).not.toContain("disabled")
        })

    });

    test('Attach groups test', () => {

        func.onclickAttach(document.getElementById('attach_cell_to_groups'))
        expect(document.getElementById('dropdown_attach_groups').style.display).toBe("inline-block");
        expect(document.getElementById('attach_cell_to_groups').style.border).toBe("1px solid black");
        expect(document.getElementById('input_bar').classList).toContain("disabled");

        Array.prototype.forEach.call(document.getElementsByClassName('m-tables-toolbar-block'), function (element, idx){
            expect(element.classList).toContain("disabled")
        })


        func.onclickAttach(document.getElementById('attach_cell_to_groups'))
        expect(document.getElementById('attach_cell_to_groups').style.border).toBe("");

        Array.prototype.forEach.call(document.getElementsByClassName('m-tables-toolbar-block'), function (element, idx){
            expect(element.classList).not.toContain("disabled")
        })
    });

    test('Submit attach users test', () => {

        func.onclickAttach(document.getElementById('attach_cell_to_users'))
        expect(document.getElementById('dropdown_attach_students').style.display).toBe("inline-block");

        func.onclickSubmitAttach(document.getElementById('submit_attach_users'), messages)
        expect(document.getElementById('submit_group_btns').style.display).toBe('none');

        expect(document.getElementById('first_cell-students').value).toBe('');
        expect(document.getElementById('last_cell-students').value).toBe('');
        expect(document.getElementById('A1').style.border).toBe('');
        expect(document.getElementById('B2').style.border).toBe('');

        Array.prototype.forEach.call(document.getElementsByClassName('m-tables-toolbar-block'), function (element, idx){
            expect(element.classList).not.toContain("disabled")
        })
    });

    test('Submit attach groups test', () => {

        func.onclickAttach(document.getElementById('attach_cell_to_groups'))
        expect(document.getElementById('dropdown_attach_groups').style.display).toBe("inline-block");

        func.onclickSubmitAttach(document.getElementById('submit_attach_groups'), messages)
        expect(document.getElementById('submit_group_btns').style.display).toBe('none');

        expect(document.getElementById('first_cell-groups').value).toBe('');
        expect(document.getElementById('last_cell-groups').value).toBe('');
        expect(document.getElementById('A1').style.border).toBe('');
        expect(document.getElementById('B2').style.border).toBe('');

        Array.prototype.forEach.call(document.getElementsByClassName('m-tables-toolbar-block'), function (element, idx){
            expect(element.classList).not.toContain("disabled")
        })
    });

    test('Cansel attach test', () => {
        func.onclickAttach(document.getElementById('attach_cell_to_users'))
        document.getElementById('first_cell-students').value = 'A1'
        document.getElementById('last_cell-students').value = 'B2'

        func.onclickCanselAttach(document.getElementById('cansel_attach_users'))
        expect(document.getElementById('submit_user_btns').style.display).toBe('none');
        expect(document.getElementById('first_cell-students').value).toBe('');
        expect(document.getElementById('last_cell-students').value).toBe('');
        expect(document.getElementById('A1').style.border).toBe('');
        expect(document.getElementById('B2').style.border).toBe('');

        func.onclickAttach(document.getElementById('attach_cell_to_groups'))
        document.getElementById('first_cell-groups').value = 'A1'
        document.getElementById('last_cell-groups').value = 'B2'

        func.onclickCanselAttach(document.getElementById('cansel_attach_groups'))
        expect(document.getElementById('submit_user_btns').style.display).toBe('none');
        expect(document.getElementById('first_cell-groups').value).toBe('');
        expect(document.getElementById('last_cell-groups').value).toBe('');
        expect(document.getElementById('A1').style.border).toBe('');
        expect(document.getElementById('B2').style.border).toBe('');
    });

    test('Update Cell test', () => {

        func.updateTablesCell(document.getElementById('A1'))
        expect(document.getElementById("focused_cell_content").value).toBe(document.getElementById('A1').innerHTML);
    });

    test('Click ceckbox attach test', () => {
        document.getElementById('chechbox_attach_users').checked = true;
        func.onclickCheckboxAttach(document.getElementById('chechbox_attach_users'))
        expect(document.getElementById('display_selected_students').value).toBe("user 1");

        document.getElementById('chechbox_attach_groups').checked = true;
        func.onclickCheckboxAttach(document.getElementById('chechbox_attach_groups'))
        expect(document.getElementById('display_selected_groups').value).toBe("group 1");
    });

    test('Attach focus cell test', () => {
        document.getElementById('attach_cell_to_groups').value = "off";
        document.getElementById('attach_cell_to_users').value = "on";
        document.getElementById('first_cell-students').value = "";
        document.getElementById('A1').style.border = "1px solid black";

        func.onFocusInCellAttach(document.getElementById('A1'))
        expect(document.getElementById('first_cell-students').value).toBe("A1");
        expect(document.getElementById('A1').style.border).toBe("1px solid #27a7d8");

        func.onFocusInCellAttach(document.getElementById('A1'))
        expect(document.getElementById('first_cell-students').value).toBe("A1");
        expect(document.getElementById('A1').style.border).toBe("1px solid #ff9a00");

        func.onFocusInCellAttach(document.getElementById('A1'))
        expect(document.getElementById('first_cell-students').value).toBe("");
        expect(document.getElementById('A1').style.border).toBe("1px solid black");

        func.onFocusInCellAttach(document.getElementById('A1'))
        expect(document.getElementById('first_cell-students').value).toBe("A1");
        expect(document.getElementById('A1').style.border).toBe("1px solid #27a7d8");

        func.onFocusInCellAttach(document.getElementById('B2'))
        expect(document.getElementById('last_cell-students').value).toBe("B2");
        expect(document.getElementById('B2').style.border).toBe("1px solid #ff9a00");

        func.onFocusInCellAttach(document.getElementById('A1'))
        expect(document.getElementById('first_cell-students').value).toBe("");
        expect(document.getElementById('A1').style.border).toBe("1px solid black");
    });

    test('Focus cell test', () => {

        func.onFocusInCellFocus(document.getElementById('A1'))
        expect(document.getElementById("focused_cell").value).toBe("A1");
        expect(document.getElementById("prev_cell").value).toBe("A1");
        expect(document.getElementById("focused_cell_content").value).toBe("Hello");
        expect(document.getElementById("font-family-selector").value).toBe("Arial");
        expect(document.getElementById("font-size-selector").value).toBe("14");
        expect(document.getElementById("font-bold-button").style.border).toBe("1px solid black");
        expect(document.getElementById("font-italic-button").style.border).toBe("1px solid black");
        expect(document.getElementById("font-underline-button").style.border).toBe("1px solid black");
        expect(document.getElementById("text-center-button").style.border).toBe("1px solid black");

        func.onFocusInCellFocus(document.getElementById('B2'))
        expect(document.getElementById("focused_cell").value).toBe("B2");
        expect(document.getElementById("prev_cell").value).toBe("B2");
        expect(document.getElementById("focused_cell_content").value).toBe("World");
        expect(document.getElementById("font-family-selector").value).toBe("Calibri");
        expect(document.getElementById("font-size-selector").value).toBe("10");
        expect(document.getElementById("font-bold-button").style.border).toBe("");
        expect(document.getElementById("font-italic-button").style.border).toBe("");
        expect(document.getElementById("font-underline-button").style.border).toBe("");
        expect(document.getElementById("text-center-button").style.border).toBe("");
    });

    test('Change input cell test', () => {

        document.getElementById('focused_cell').value = ""

        func.onChangeInputCell(document.getElementById('focused_cell'))
        expect(document.getElementById("focused_cell").value).toBe("");
        expect(document.getElementById("prev_cell").value).toBe("");
        expect(document.getElementById("focused_cell_content").value).toBe("");
        expect(document.getElementById("font-family-selector").value).toBe("Calibri");
        expect(document.getElementById("font-size-selector").value).toBe("10");
        expect(document.getElementById("font-bold-button").style.border).toBe("");
        expect(document.getElementById("font-italic-button").style.border).toBe("");
        expect(document.getElementById("font-underline-button").style.border).toBe("");
        expect(document.getElementById("text-center-button").style.border).toBe("");

        document.getElementById('focused_cell').value = "A1"

        func.onChangeInputCell(document.getElementById('focused_cell'))
        expect(document.getElementById("focused_cell").value).toBe("A1");
        expect(document.getElementById("prev_cell").value).toBe("A1");
        expect(document.getElementById("focused_cell_content").value).toBe("Hello");
        expect(document.getElementById("font-family-selector").value).toBe("Arial");
        expect(document.getElementById("font-size-selector").value).toBe("14");
        expect(document.getElementById("font-bold-button").style.border).toBe("1px solid black");
        expect(document.getElementById("font-italic-button").style.border).toBe("1px solid black");
        expect(document.getElementById("font-underline-button").style.border).toBe("1px solid black");
        expect(document.getElementById("text-center-button").style.border).toBe("1px solid black");

        document.getElementById('focused_cell').value = "B2"

        func.onChangeInputCell(document.getElementById('focused_cell'))
        expect(document.getElementById("focused_cell").value).toBe("B2");
        expect(document.getElementById("prev_cell").value).toBe("B2");
        expect(document.getElementById("focused_cell_content").value).toBe("World");
        expect(document.getElementById("font-family-selector").value).toBe("Calibri");
        expect(document.getElementById("font-size-selector").value).toBe("10");
        expect(document.getElementById("font-bold-button").style.border).toBe("");
        expect(document.getElementById("font-italic-button").style.border).toBe("");
        expect(document.getElementById("font-underline-button").style.border).toBe("");
        expect(document.getElementById("text-center-button").style.border).toBe("");
    });

    test('Change input content test', () => {

        document.getElementById("focused_cell_content").value = "Test text"
        document.getElementById('focused_cell').value = "A1"

        func.onChangeInputContent(document.getElementById("focused_cell_content"))
        expect(document.getElementById("A1").value).toBe("Test text");
    });

    test('Update font test', () => {
        document.getElementById('focused_cell').value = "A1"
        func.onChangeInputCell(document.getElementById('focused_cell'))
        document.getElementById("font-family-selector").value = "Times new roman"

        func.updateFont(document.getElementById("font-family-selector"))
        expect(document.getElementById("A1").style.fontFamily).toBe("Times new roman");

        document.getElementById("font-size-selector").value = "22"

        func.updateFont(document.getElementById("font-size-selector"))
        expect(document.getElementById("A1").style.fontSize).toBe("22pt");

        document.getElementById('focused_cell').value = "B2"
        func.onChangeInputCell(document.getElementById('focused_cell'))

        func.updateFont(document.getElementById("font-bold-button"))
        expect(document.getElementById("B2").style.fontWeight).toBe("bold");
        func.updateFont(document.getElementById("font-bold-button"))
        expect(document.getElementById("B2").style.fontWeight).toBe("normal");

        func.updateFont(document.getElementById("font-italic-button"))
        expect(document.getElementById("B2").style.fontStyle).toBe("italic");
        func.updateFont(document.getElementById("font-italic-button"))
        expect(document.getElementById("B2").style.fontStyle).toBe("normal");

        func.updateFont(document.getElementById("font-underline-button"))
        expect(document.getElementById("B2").style.textDecoration).toBe("underline");
        func.updateFont(document.getElementById("font-underline-button"))
        expect(document.getElementById("B2").style.textDecoration).toBe("none");

        func.updateFont(document.getElementById("text-right-button"))
        expect(document.getElementById("B2").style.textAlign).toBe("right");

        func.updateFont(document.getElementById("text-center-button"))
        expect(document.getElementById("B2").style.textAlign).toBe("center");

        func.updateFont(document.getElementById("text-left-button"))
        expect(document.getElementById("B2").style.textAlign).toBe("left");
    });

    test('Create sheet test', () => {

        func.createSheet(document.getElementById("add_sheet_for_module_1"))
        expect(document.getElementById("new_sheet_id").value).toBe("test_value");
    });

    test('Grade cell test', () => {
        document.getElementById("feedback_block").style.display = "inline-block";
        document.getElementById("feedback_textarea").value = "Test feedback";

        func.gradeCell()
        expect(document.getElementById('check_grade').classList).toContain("m-tables-show");

        document.getElementById("feedback_block").style.display = "none";
    });

    test('Change grade test', () => {

        func.onchangeInputGrade()
        expect(document.getElementById('check_grade').classList).not.toContain("m-tables-show");
    });

    test('Wrong input grade test', () => {
        document.getElementById('input_grade').value = 101;

        func.oninputGrade(document.getElementById('input_grade'))
        expect(document.getElementById('input_grade').value).toBe("100");

        document.getElementById('input_grade').value = -1;

        func.oninputGrade(document.getElementById('input_grade'))
        expect(document.getElementById('input_grade').value).toBe("0");

        document.getElementById('input_grade').value = 75;

        func.oninputGrade(document.getElementById('input_grade'))
        expect(document.getElementById('input_grade').value).toBe("75");
    });

    test('Show feedback test', () => {
        document.getElementById('input_grade').value = 101;

        func.showFeedback(document.getElementById('show_feedback_btn'))
        expect(document.getElementById("feedback_block").style.display).toBe("inline-block");
        expect(document.getElementById('show_feedback_btn').classList).toContain("fa-minus");

        func.showFeedback(document.getElementById('show_feedback_btn'))
        expect(document.getElementById("feedback_block").style.display).toBe("none");
        expect(document.getElementById('show_feedback_btn').classList).toContain("fa-plus");
    });

    test('Change visibility test', () => {
        document.getElementById('focused_cell').value = "A1"
        document.getElementById('select_cell_visibility').value = "user"

        func.onChangeSelectVisibility()
        expect(document.getElementById('A1').getAttribute("data-visibility")).toBe("user");
    });
});