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
 * Prints an instance of mod_tables.
 *
 * @package     mod_tables
 * @copyright   2023 Mazur Egor <mazur.eh@edu.spbstu.ru>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require(__DIR__.'/../../config.php');
require_once(__DIR__.'/lib.php');

global $DB, $USER, $CFG, $OUTPUT, $PAGE;

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

require_login($course, true, $cm);

$modulecontext = context_module::instance($cm->id);
$coursecontext = context_course::instance($course->id);

$event = \mod_tables\event\course_module_viewed::create(array(
    'objectid' => $moduleinstance->id,
    'context' => $modulecontext
));
$event->add_record_snapshot('course', $course);
$event->add_record_snapshot('tables', $moduleinstance);
$event->trigger();

$PAGE->set_url('/mod/tables/view.php', array('id' => $cm->id));
$PAGE->set_title(format_string($moduleinstance->name));
$PAGE->set_heading(format_string($course->fullname));
$PAGE->set_context($modulecontext);

$PAGE->requires->jquery();
$PAGE->requires->js(new moodle_url($CFG->wwwroot . '/mod/tables/amd/src/connect_to_websocket.js?v=4.4'));
$PAGE->requires->js(new moodle_url($CFG->wwwroot . '/mod/tables/amd/src/update_data.js?v=7.6'));
$PAGE->requires->js(new moodle_url($CFG->wwwroot . '/mod/tables/amd/src/interact_resize.js?v=2.1'));
$PAGE->requires->js(new moodle_url($CFG->wwwroot . '/mod/tables/amd/src/jquery_tables_functions.js?v=1.2'));

$roles = get_default_enrol_roles($modulecontext);
$user_roles = get_user_roles_in_course($USER->id, $course->id);

echo'<script>
        localStorage.clear();
    </script>';

if((strpos($user_roles, $roles[1]) !== false) || (strpos($user_roles, $roles[3]) !== false)){
    $user_activity_role = "teacher";
    echo'<script>
        localStorage.activity_role = "'.$user_activity_role.'"
    </script>';
}
elseif(strpos($user_roles, $roles[4]) !== false){
    $user_activity_role = "assistant";
    echo'<script>
        localStorage.activity_role = "'.$user_activity_role.'"
    </script>';
}
else{
    $user_activity_role = "student";
    echo'<script>
        localStorage.activity_role = "'.$user_activity_role.'"
    </script>';
}

if($DB->record_exists('tables_users_focus', array('tableid' => $moduleinstance->id, 'userid' => $USER->id))){
    $user_focus_data = $DB->get_record('tables_users_focus', array('tableid' => $moduleinstance->id, 'userid' => $USER->id));
    $prev_cell = $user_focus_data->focused_cell;
    echo'<input hidden id="prev_element" type="text" value="'.$prev_cell.'" />';
    $user_focus_data->focused_cell = null;
    if($_POST["sheet"]){
        $active_sheet = $DB->get_record('tables_sheets', array('id' => $_POST["sheet"]));
        $user_focus_data->active_sheet = $active_sheet;
    }
    else{
        $active_sheet =  $DB->get_record('tables_sheets', array('id' => $user_focus_data->active_sheet));
    }
    $DB->update_record('tables_users_focus', $user_focus_data);
}
else{
    $active_sheet = $DB->get_record('tables_sheets', array('tableid' => $moduleinstance->id));
    $DB->insert_record('tables_users_focus', array('tableid' => $moduleinstance->id, 'userid' => $USER->id, "active_sheet" => $active_sheet->id,
        'timecreated' => time()));
}

if($active_sheet->activityloadid != "" && $active_sheet->activityloadtype != "" && $active_sheet->updateonreloadpage != "false"){
    load_from_activity($modulecontext, $course, $active_sheet->id, $active_sheet->activityloadtype, $active_sheet->activityloadid);
}

//Toolbar
$fonts = array('Arial',
    'Arial Black',
    'Bahnschrift',
    'Calibri',
    'Cambria',
    'Cambria Math',
    'Candara',
    'Comic Sans MS',
    'Consolas',
    'Constantia',
    'Corbel',
    'Courier New',
    'Ebrima',
    'Franklin Gothic Medium',
    'Gabriola',
    'Gadugi',
    'Georgia',
    'HoloLens MDL2 Assets',
    'Impact',
    'Ink Free',
    'Javanese Text',
    'Leelawadee UI',
    'Lucida Console',
    'Lucida Sans Unicode',
    'Malgun Gothic',
    'Marlett',
    'Microsoft Himalaya',
    'Microsoft JhengHei',
    'Microsoft New Tai Lue',
    'Microsoft PhagsPa',
    'Microsoft Sans Serif',
    'Microsoft Tai Le',
    'Microsoft YaHei',
    'Microsoft Yi Baiti',
    'MingLiU-ExtB',
    'Mongolian Baiti',
    'MS Gothic',
    'MV Boli',
    'Myanmar Text',
    'Nirmala UI',
    'Palatino Linotype',
    'Segoe MDL2 Assets',
    'Segoe Print',
    'Segoe Script',
    'Segoe UI',
    'Segoe UI Historic',
    'Segoe UI Emoji',
    'Segoe UI Symbol',
    'SimSun',
    'Sitka',
    'Sylfaen',
    'Symbol',
    'Tahoma',
    'Times New Roman',
    'Trebuchet MS',
    'Verdana',
    'Webdings',
    'Wingdings',
    'Yu Gothic');

echo $OUTPUT->header();

echo '
<div class="m-tables-toolbar">';
if($user_activity_role == "teacher") {
    echo'
    <div class="m-tables-toolbar-block">
        <form class="m-tables-toolbar-load-up" method="post" action="upload_from_xlsx.php?id='.$id.'">
            <button class="m-tables-toolbar-button" type="submit">
                <img class="m-tables-toolbar-img" src="pix/upload.png" alt="bold">   
            </button>
        </form>
        <form class="m-tables-toolbar-load-down" method="post" action="upload_from_activity.php?id='.$id.'">
            <button class="m-tables-toolbar-button" type="submit">
                <img class="m-tables-toolbar-img" src="pix/upload.png" alt="bold">   
            </button>
        </form>
    </div>';
}
    echo'
    <div id="toolbar_font" class="m-tables-toolbar-block">
        <div class="m-tables-toolbar-font-up">
            <input class="m-tables-font-family-selector" 
                id="font-family-selector" 
                title="'.get_string('font_family_title', 'mod_tables').'" 
                name="font-family-selector" 
                type="text" 
                value="Calibri" 
                autocomplete="off" 
                onchange="updateFont(this)" 
                list="fonts"/>
            <datalist id="fonts">';
                foreach ($fonts as &$font){
echo                '<option value="'.$font.'">'.$font.'</option>';
                }
echo       '</datalist>
            <input class="m-tables-font-size-selector" 
                id="font-size-selector" 
                title="' . get_string('font_size_title', 'mod_tables') . '" 
                onchange="updateFont(this)" 
                type="number" min="1" max="409" value="11" xmlns="http://www.w3.org/1999/html"/>
        </div>
        <div class="m-tables-toolbar-font-down">
            <button class="m-tables-toolbar-button" id="font-bold-button" onclick="updateFont(this)" 
                title="'.get_string('font_bold_title', 'mod_tables').'">
                <img class="m-tables-toolbar-img" src="pix/bold.png" alt="bold">
            </button>
            <button class="m-tables-toolbar-button" id="font-italic-button" onclick="updateFont(this)" 
                title="'.get_string('font_italic_title', 'mod_tables').'">
                <img class="m-tables-toolbar-img" src="pix/italic.png" alt="italic">
            </button>
            <button class="m-tables-toolbar-button" id="font-underline-button" onclick="updateFont(this)" 
                title="'.get_string('font_underline_title', 'mod_tables').'">
                <img class="m-tables-toolbar-img" src="pix/underline.png" alt="underline">
            </button>
        </div>
    </div>
    <div id="toolbar_align" class="m-tables-toolbar-block">
        <div class="m-tables-toolbar-align">
            <button class="m-tables-toolbar-button" id="text-left-button" onclick="updateFont(this)" 
                title="'.get_string('text_align_left_title', 'mod_tables').'" >
                <img class="m-tables-toolbar-img" src="pix/textalignleft.png" alt="left">
            </button>
            <button class="m-tables-toolbar-button" id="text-center-button" onclick="updateFont(this)" 
                title="'.get_string('text_align_center_title', 'mod_tables').'" >
                <img class="m-tables-toolbar-img" src="pix/textaligncenter.png" alt="center">
            </button>
            <button class="m-tables-toolbar-button" id="text-right-button" onclick="updateFont(this)" 
                title="'.get_string('text_align_right_title', 'mod_tables').'" >
                <img class="m-tables-toolbar-img" src="pix/textalignright.png" alt="right">
            </button>
        </div>
        <div class="m-tables-toolbar-align-down">
            
        </div>
    </div>';
    
    if($user_activity_role == "teacher") {
        echo
        '<div class="m-tables-toolbar-block-attach">
            <div class="m-tables-toolbar-attach">
                <button class="m-tables-toolbar-button" id="attach_cell_to_users" onclick="onclickAttach(this)" 
                    title="' . get_string('attachcellstostudents', 'mod_tables') . '" value="off" data-attach-to="user">
                    <img class="m-tables-toolbar-img" src="pix/user.png" alt="user">
                </button>
                <div class="m-dropdown" id="dropdown_attach_students" style="display:none;" >
                    <div class="m-dropdown-display">
                        <input class="m-dropdown-checked" type="text" id="display_selected_students">
                        <input class="m-dropdown-search" autocomplete="off"  type="text" oninput="onInputSearch(this)" id="search_students" data-attach-to="user">
                    </div>
                    <div class="m-dropdown-content" id="dropdown-content-users">';

        $role_users = get_role_users(5, $coursecontext);

        foreach ($role_users as $user) {
            echo '<p>
                    <input class="m-user-check" data-attach-to="user" data-attach-name="' . $user->firstname . " " . $user->lastname . '" value="' . $user->id . '" type="checkbox" onclick="onclickCheckboxAttach(this)">
                    <label class="m-tables-user-label">' . $user->firstname . " " . $user->lastname . '</label>
            </p>';
        }
                    echo '</div>
                </div>
                <input class="m-dropdown-students-cell" id="first_cell-students" type="text" readonly> 
                <input class="m-dropdown-students-cell" id="last_cell-students" type="text" readonly>
                <div id="submit_user_btns" style="display: none">
                    <span class="m-tables-green-btn">
                        <i class="fa fa-check" data-attach-to="user" onclick="onclickSubmitAttach(this, messages)" ></i>
                    </span>
                    <span class="m-tables-red-btn">
                        <i class="fa fa-times" data-attach-to="user" onclick="onclickCanselAttach(this)" ></i>
                    </span>
                </div>
            </div>
            <div class="m-tables-toolbar-attach">
                <button class="m-tables-toolbar-button" id="attach_cell_to_groups" onclick="onclickAttach(this)" 
                    title="" value="off" data-attach-to="group">
                    <img class="m-tables-toolbar-img" src="pix/group.png" alt="group">
                </button>
                <div class="m-dropdown" id="dropdown_attach_groups" style="display:none;" >
                    <div class="m-dropdown-display">
                        <input class="m-dropdown-checked" type="text" id="display_selected_groups">
                        <input class="m-dropdown-search" autocomplete="off"  type="text" oninput="onInputSearch(this)" id="search_groups" data-attach-to="group">
                    </div>
                    <div class="m-dropdown-content" id="dropdown-content-groups">';

        $groups = groups_get_all_groups($course->id);

        foreach ($groups as $group) {
            echo '<p>
                    <input class="m-group-check" data-attach-to="group" data-attach-name="' . $group->name . '" value="' . $group->id . '" type="checkbox" onclick="onclickCheckboxAttach(this)">
                    <label class="m-tables-user-label">' . $group->name . '</label>
            </p>';
        }
        echo '</div>
                </div>
                <input class="m-dropdown-groups-cell" id="first_cell-groups" type="text" readonly> 
                <input class="m-dropdown-groups-cell" id="last_cell-groups" type="text" readonly>
                <div id="submit_group_btns" style="display: none">
                    <span class="m-tables-green-btn">
                        <i class="fa fa-check" data-attach-to="group" onclick="onclickSubmitAttach(this, messages)" ></i>
                    </span>
                    <span class="m-tables-red-btn">
                        <i class="fa fa-times" data-attach-to="group" onclick="onclickCanselAttach(this)" ></i>
                    </span>
                </div>
            </div>
        </div>
        <div id="grade_block" class="m-tables-toolbar-block disabled">
            <div class="m-tables-toolbar-grade-up">
                <select id="select_user_grade">';
                $sheet_users = get_role_users(5, $modulecontext, true, 'u.id, u.firstname, u.lastname');
                foreach ($sheet_users as $user) {
                    echo '
                    <option value="' . $user->id . '">
                        ' . $user->lastname . ' ' . $user->firstname . '
                    </option>';
        }
                echo '</select>
            </div>
            <div class="m-tables-toolbar-grade-down">
                <input id="input_grade" type="number" min="0" max="100" oninput="oninputGrade(this)" onchange="onchangeInputGrade()">
                <i class="fa fa-check m-tables-check-grade" id="check_grade"></i>
                <button class="btn btn-primary m-tables-toolbar-grade-button" onclick="gradeCell()">Оценить</button>
                <span class="m-tables-blue-btn">
                    <i class="fa fa-plus" id="show_feedback_btn" onclick="showFeedback(this)"></i>
                </span>
            </div>
        </div>
        <div id="feedback_block" class="m-tables-toolbar-block m-tables-toolbar-grade-textarea" style="display: none">
            <textarea id="feedback_textarea"></textarea>
        </div>
        <div class="m-tables-toolbar-block">
            <div id="visibility_block" class="m-tables-toolbar-visible-up disabled">
                <select id="select_cell_visibility" onchange="onChangeSelectVisibility()">
                    <option value="teacher">'.get_string("visibleteacher", 'mod_tables').'</option>
                    <option value="all">'.get_string("visibleall", 'mod_tables').'</option>
                    <option value="user">'.get_string("visibleuser", 'mod_tables').'</option>
                    <option value="group">'.get_string("visiblegroup", 'mod_tables').'</option>
                </select>
            </div> 
        </div>';
    }
echo'</div>';

//Input bar
echo '<div class="m-tables-input-bar" id="input_bar">
    <input style="display: none"
        type="text" id="prev_cell">
    <input class="m-tables-focused-cell" 
        type="text" id="focused_cell" 
        onchange="onChangeInputCell(this)" />
    <input class="m-tables-focused-cell-content" 
        type="text" 
        onchange = "onChangeInputContent(this)" 
        id="focused_cell_content" />
</div>';

//Table
$rows = $moduleinstance->rowcount;
$columns = $moduleinstance->columncount;

echo '<div class="m-tables-settings">
    <table id="main_table" data-id="'.$id.'" data-moduleinstance="'.$moduleinstance->id.'" data-sheet="'.$active_sheet->id.'" data-user-role="'.$user_activity_role.'" data-user="'.$USER->id.'">
        <thead>
            <tr>
                <td></td>';
                    for ($column = 0; $column < $columns; $column++) {
                        $columnname = generate_column_name($column);
                        $columnwidth = get_column_width("col_".$columnname, $active_sheet->id);
                        echo'<td>
                                <input class="resizable-column" 
                                    type="text" 
                                    id="col_'.$columnname.'" 
                                    style="width: '.$columnwidth.'px;" 
                                    value="'.$columnname.'" readonly 
                                    />
                            </td>';
                    }
            echo '</tr>
        </thead>
        <tbody>';
            for ($row = 1; $row <= $rows; $row++) {
                $rowheight = get_row_height("row_".$row, $active_sheet->id);
                echo '<tr>
                    <td>
                        <input class="resizable-row" 
                            type="text" 
                            id="row_'.$row.'" 
                            style="height:'.$rowheight.'px;" 
                            value="'.$row.'" readonly />
                    </td>';
                    for ($column = 0; $column < $columns; $column++) {
                        $cell = array('name' => generate_column_name($column).$row, 'sheetid' => $active_sheet->id);
                        $useronfocus = null;

                        if($DB->record_exists('tables_users_focus', array('focused_cell' => $cell['name'], 'active_sheet' => $cell['sheetid']))){
                            $useronfocus = $DB->get_record('tables_users_focus',
                                array('focused_cell' => $cell['name'], 'active_sheet' => $cell['sheetid']), '*', MUST_EXIST);
                        }

                        if($user_activity_role == 'teacher'){
                            $disablecell = '';
                        }
                        else{
                            $disablecell = 'disabled-cell';
                            $group_visibility = 'false';
                            $user_visibility = 'false';
                        }

                        $user_groups = groups_get_user_groups($course->id, $USER->id);

                        foreach($user_groups as $user_group){
                            foreach($user_group as $group_id){
                                if($DB->record_exists('tables_groups_cells', array('sheetid' => $active_sheet->id, 'groupid' => $group_id, 'cellname' => $cell['name']))){
                                    $disablecell = '';
                                    $group_visibility = 'group';
                                }
                            }
                        }

                        if($DB->record_exists('tables_users_cells', array('sheetid' => $active_sheet->id, 'userid' => $USER->id, 'cellname' => $cell['name']))){
                            $disablecell = '';
                            $user_visibility = 'user';
                        }

                        if($useronfocus->userid != null && $useronfocus->userid != $USER->id){
                            $disablecell = 'disabled-cell';
                        }

                        if($DB->record_exists('tables_sheets_cells', $cell)){
                            if($user_activity_role != 'teacher'){
                                $cell_visibility = $DB->get_record('tables_sheets_cells', $cell, '*', MUST_EXIST)->visibility;

                                echo'
                                    <script>
                                        localStorage.'.$cell["name"].' = "all";
                                    </script>
                                ';

                                switch ($cell_visibility){
                                    case 'all':{
                                        $cell['content'] = $DB->get_record('tables_sheets_cells', $cell, '*', MUST_EXIST)->content;
                                        echo'
                                            <script>
                                                 localStorage.'.$cell["name"].' = "all";
                                            </script>
                                        ';
                                        break;
                                    }
                                    case 'group':{
                                        if($group_visibility == $cell_visibility){
                                            $cell['content'] = $DB->get_record('tables_sheets_cells', $cell, '*', MUST_EXIST)->content;
                                            echo'
                                                <script>
                                                    localStorage.'.$cell["name"].' = "group";
                                                </script>
                                            ';
                                        }
                                        break;
                                    }
                                    case 'user':{
                                        if($user_visibility == $cell_visibility){
                                            $cell['content'] = $DB->get_record('tables_sheets_cells', $cell, '*', MUST_EXIST)->content;
                                            echo'
                                                <script>
                                                     localStorage.'.$cell["name"].' = "user";
                                                </script>
                                            ';
                                        }
                                        break;
                                    }
                                }
                            }
                            else{
                                $cell_visibility = $DB->get_record('tables_sheets_cells', $cell, '*', MUST_EXIST)->visibility;
                                $cell['content'] = $DB->get_record('tables_sheets_cells', $cell, '*', MUST_EXIST)->content;
                                echo'
                                    <script>
                                         localStorage.'.$cell["name"].' = "teacher";
                                    </script>
                                ';
                            }

                            echo '<td class="table-cell">
                                    <textarea name="cell_textarea" 
                                    class ="'.$disablecell.'" 
                                    data-visibility = "'.$cell_visibility.'" 
                                    data-attached="'.$DB->record_exists('tables_users_cells', array('sheetid' => $active_sheet->id, 'userid' => $USER->id, 'cellname' => $cell['name'])).'" 
                                    style="
                                        font-family: '.get_cell_font_family($cell['name'], $moduleinstance->id).'; 
                                        font-size: '.get_cell_font_size($cell['name'], $moduleinstance->id).'pt; 
                                        font-weight: '.get_cell_bold($cell['name'], $moduleinstance->id).'; 
                                        font-style: '.get_cell_italic($cell['name'], $moduleinstance->id).'; 
                                        text-decoration: '.get_cell_underline($cell['name'], $moduleinstance->id).'; 
                                        text-align: '.get_cell_align($cell['name'], $moduleinstance->id).'; "
                                    onfocus="onFocusInCell(this)" 
                                    onchange="saveCellHistory(this)" 
                                    oninput="updateTablesCell(this)" 
                                    id='.$cell['name'].'>'.$cell['content'].'</textarea>
                            </td>';
                        }
                        else{
                            $cell_visibility = 'all';
                            $cell['content'] = null;
                            echo '<td class="table-cell">
                                    <textarea name="cell_textarea" 
                                    class ="'.$disablecell.'" 
                                    data-visibility = "'.$cell_visibility.'" 
                                    data-attached="'.$DB->record_exists('tables_users_cells', array('sheetid' => $active_sheet->id, 'userid' => $USER->id, 'cellname' => $cell['name'])).'" 
                                    style="
                                        font-family: '.get_cell_font_family($cell['name'], $moduleinstance->id).'; 
                                        font-size: '.get_cell_font_size($cell['name'], $moduleinstance->id).'pt; 
                                        font-weight: '.get_cell_bold($cell['name'], $moduleinstance->id).'; 
                                        font-style: '.get_cell_italic($cell['name'], $moduleinstance->id).'; 
                                        text-decoration: '.get_cell_underline($cell['name'], $moduleinstance->id).'; 
                                        text-align: '.get_cell_align($cell['name'], $moduleinstance->id).'; "
                                    onfocus="onFocusInCell(this)" 
                                    onchange="saveCellHistory(this)" 
                                    oninput="updateTablesCell(this)" 
                                    id='.$cell['name'].'>'.$cell['content'].'</textarea>
                            </td>';
                        }
                    }
                echo '</tr>';
            }
        echo '</tbody>
    </table>
    <ul class="m-sheet-custom-menu" id="custom_menu_'.$moduleinstance->id.'">
        <li id="delete_sheet" data-action = "delete_sheet" '; if($user_activity_role!="teacher"){echo'hidden="hidden"';} echo'>Delete</li>
    </ul>
    <form method="post">
        <div class="m-tables-sheet-bar" id="sheet_bar">';
            $sheets = $DB->get_records('tables_sheets', array('tableid'=>$moduleinstance->id));
                foreach($sheets as $sheet){
                    echo'<button class="m-tables-sheet-select" type="submit" name="sheet" value="'.$sheet->id.'" id="sheet_'.$sheet->id.'" '; if($active_sheet->id == $sheet->id){echo'disabled';} echo'>
                        '.get_string("sheet", "mod_tables")." ".$sheet->name.'
                    </button>';
                }
        echo'</div>';
            if($user_activity_role =="teacher"){
                echo'
                <span class="m-tables-sheet-add">
                    <i class="fa fa-plus" id="add_sheet_for_module_'.$moduleinstance->id.'" onclick="createSheet(this)"></i>
                </span>';
            }
    echo'
    </form>';
    if($user_activity_role =="teacher") {
        echo '
        <button class="btn btn-primary" style="border-radius: 5px" onclick="deleteSheet()">Delete sheet</button>';
    }
    echo'
</div>';

echo '<script src="https://cdn.jsdelivr.net/npm/interactjs/dist/interact.min.js"></script>
<script src="//cdn.socket.io/socket.io-1.2.0.js"></script>
<script> 
    localStorage.KEY = "'.$moduleinstance->wskey.'";
    if("'.$moduleinstance->wsserver.'" == ""){
        localStorage.socket = "false";
    }
    else{
        localStorage.socket = "true";
    }
    let socket = io("'.$moduleinstance->wsserver.'")
    let messages = ["'.get_string("alertselectstudents", "mod_tables").'", "'.get_string("alertselectcellss", "mod_tables").'"] 
</script>';

echo $OUTPUT->footer();

