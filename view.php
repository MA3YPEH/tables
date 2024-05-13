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
$PAGE->requires->js(new moodle_url($CFG->wwwroot . '/mod/tables/amd/src/connect_to_websocket.js?v=2.6'));
$PAGE->requires->js(new moodle_url($CFG->wwwroot . '/mod/tables/amd/src/update_data.js?v=3.3'));
$PAGE->requires->js(new moodle_url($CFG->wwwroot . '/mod/tables/amd/src/interact_resize.js?v=2.0'));
$PAGE->requires->js(new moodle_url($CFG->wwwroot . '/mod/tables/amd/src/attach_cells.js?v=1.8'));

if($DB->record_exists('tables_users_cells', array('tableid' => $moduleinstance->id, 'userid' => $USER->id))){
    $user_data = $DB->get_record('tables_users_cells', array('tableid' => $moduleinstance->id, 'userid' => $USER->id));
    $prev_cell = $user_data->focused_cell;
    echo'<input hidden id="prev_element" type="text" value="'.$prev_cell.'" />';
    $user_data->focused_cell = null;
    $DB->update_record('tables_users_cells', $user_data);
}
else{
    $DB->insert_record('tables_users_cells', array('tableid' => $moduleinstance->id, 'userid' => $USER->id,
        'timecreated' => time()));
}

echo $OUTPUT->header();

if ($groupmode = groups_get_activity_groupmode($cm)) {   // Groups are being used.
    $currentgroup = groups_get_activity_group($cm);
} else {
    $currentgroup = 0;
}
$groupingid = $cm->groupingid;

if (has_capability('mod/survey:readresponses', $modulecontext) or ($groupmode == VISIBLEGROUPS)) {
    $currentgroup = 0;
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

echo '<div class="m-tables-toolbar">
    <div class="m-tables-toolbar-font">
        <div class="m-tables-toolbar-font-up">
            <input class="m-tables-font-family-selector" 
                id="font-family-selector" 
                title="'.get_string('font_family_title', 'mod_tables').'" 
                name="cell_module_'.$moduleinstance->id.'" 
                type="text" 
                value="Calibri" 
                onchange="updateFont(this, conn)" 
                list="fonts"/>
            <datalist id="fonts">';
                foreach ($fonts as &$font){
echo                '<option value="'.$font.'">'.$font.'</option>';
                }
echo       '</datalist>
            <input class="m-tables-font-size-selector" 
                id="font-size-selector" 
                title="' . get_string('font_size_title', 'mod_tables') . '" 
                name="cell_module_' . $moduleinstance->id . '" 
                onchange="updateFont(this, conn)" 
                type="number" min="1" max="409" value="11" xmlns="http://www.w3.org/1999/html"/>
        </div>
        <div class="m-tables-toolbar-font-down">
            <button id="font-bold-button" name="cell_module_' .$moduleinstance->id.'" onclick="updateFont(this, conn)" 
                title="'.get_string('font_bold_title', 'mod_tables').'">
                <img src="pix/bold.png" alt="bold">
            </button>
            <button id="font-italic-button" name="cell_module_'.$moduleinstance->id.'" onclick="updateFont(this, conn)" 
                title="'.get_string('font_italic_title', 'mod_tables').'">
                <img src="pix/italic.png" alt="italic">
            </button>
            <button id="font-underline-button" name="cell_module_'.$moduleinstance->id.'" onclick="updateFont(this, conn)" 
                title="'.get_string('font_underline_title', 'mod_tables').'">
                <img src="pix/underline.png" alt="underline">
            </button>
        </div>
    </div>
    <div class="m-tables-toolbar-align">
        <div class="m-tables-toolbar-align-up">
            <button id="text-left-button" name="cell_module_' .$moduleinstance->id.'" onclick="updateFont(this, conn)" 
                title="'.get_string('text_align_left_title', 'mod_tables').'" >
                <img src="pix/textalignleft.png" alt="left">
            </button>
            <button id="text-center-button" name="cell_module_' .$moduleinstance->id.'" onclick="updateFont(this, conn)" 
                title="'.get_string('text_align_center_title', 'mod_tables').'" >
                <img src="pix/textaligncenter.png" alt="center">
            </button>
            <button id="text-right-button" name="cell_module_' .$moduleinstance->id.'" onclick="updateFont(this, conn)" 
                title="'.get_string('text_align_right_title', 'mod_tables').'" >
                <img src="pix/textalignright.png" alt="right">
            </button>
        </div>
        <div class="m-tables-toolbar-align-down">
            
        </div>
    </div>
</div>';

//Input bar
echo '<div class="m-tables-input-bar">
    <input class="m-tables-focused-cell" 
        type="text" id="focused_cell" 
        onfocusin="onFocusInInputCell(this, conn)" 
        onfocusout="onFocusOutInputCell(this, conn)" 
        name="cell_module_'.$moduleinstance->id.'" />
    <input class="m-tables-focused-cell-content" 
        type="text" 
        onchange = "onChangeInputContent(this, conn)" 
        id="focused_cell_content" 
        name="cell_module_'.$moduleinstance->id.'"/>
</div>';

//Table
$rows = $moduleinstance->rowcount;
$columns = $moduleinstance->columncount;
echo '<div class="m-tables-settings">
    <table>
        <thead>
            <tr>
                <td></td>';
                    for ($column = 0; $column < $columns; $column++) {
                        $columnname = generate_column_name($column);
                        $columnwidth = get_column_width("col_".$columnname, $moduleinstance->id);
                        echo'<td>
                                <input class="resizable-column" 
                                    type="text" 
                                    id="col_'.$columnname.'" 
                                    style="width: '.$columnwidth.'px;" 
                                    name="cell_module_'.$moduleinstance->id.'" 
                                    value="'.$columnname.'" readonly 
                                    />
                            </td>';
                    }
            echo '</tr>
        </thead>
        <tbody>';
            for ($row = 1; $row <= $rows; $row++) {
                $rowheight = get_row_height("row_".$row, $moduleinstance->id);
                echo '<tr>
                    <td>
                        <input class="resizable-row" 
                            type="text" 
                            id="row_'.$row.'" 
                            style="height:'.$rowheight.'px;" 
                            name="cell_module_'.$moduleinstance->id.'" 
                            value="'.$row.'" readonly />
                    </td>';
                    for ($column = 0; $column < $columns; $column++) {
                        $cell = array('name' => generate_column_name($column).$row, 'tableid' => $moduleinstance->id);
                        $useronfocus = null;
                        $attached_cells = null;

                        if($DB->record_exists('tables_users_cells', array('focused_cell' => $cell['name'], 'tableid' => $cell['tableid']))){
                            $useronfocus = $DB->get_record('tables_users_cells',
                                array('focused_cell' => $cell['name'], 'tableid' => $cell['tableid']), '*', MUST_EXIST);
                        }
                        if($DB->record_exists('tables_users_cells', array('tableid' => $moduleinstance->id, 'userid' => $USER->id))){
                            $attached_cells = $DB->get_record('tables_users_cells',
                                array('userid' => $USER->id, 'tableid' => $moduleinstance->id), '*', MUST_EXIST)->attached_cells;
                            $attached_cells = explode(', ', $attached_cells);
                        }

                        $viewableroles = get_viewable_roles($modulecontext, $USER->id);
                        $roles = get_user_roles_in_course($USER->id, $course->id);

                        if(str_contains($roles, $viewableroles[4]) || str_contains($roles, $viewableroles[5]) || str_contains($roles, $viewableroles[6]) || str_contains($roles, $viewableroles[7]) || str_contains($roles, $viewableroles[8])){
                            $disablecell = 'disabled';
                        }
                        else{
                            $disablecell = '';
                        }

                        if($useronfocus->userid != null && $useronfocus->userid != $USER->id){
                            $disablecell = 'disabled';
                        }
                        else if(!isAttach($attached_cells, $cell['name'])){
                            $disablecell = '';
                        }

                        if($DB->record_exists('tables_cells', $cell)){
                            $cell['content'] = $DB->get_record('tables_cells', $cell, '*', MUST_EXIST)->content;

                            echo '<td>
                                    <textarea name="cell_module_'.$cell['tableid'].'" 
                                    '.$disablecell.' 
                                    style="
                                        font-family: '.get_cell_font_family($cell['name'], $moduleinstance->id).'; 
                                        font-size: '.get_cell_font_size($cell['name'], $moduleinstance->id).'pt; 
                                        font-weight: '.get_cell_bold($cell['name'], $moduleinstance->id).'; 
                                        font-style: '.get_cell_italic($cell['name'], $moduleinstance->id).'; 
                                        text-decoration: '.get_cell_underline($cell['name'], $moduleinstance->id).'; 
                                        text-align: '.get_cell_align($cell['name'], $moduleinstance->id).'; "
                                    onfocus="onFocusInCell(this, conn)" 
                                    oninput="updateTablesCell(this, conn)" 
                                    id='.$cell['name'].'>'.$cell['content'].'</textarea>
                            </td>';
                        }
                        else{
                            $cell['content'] = null;
                            echo '<td>
                                    <textarea name="cell_module_'.$cell['tableid'].'" 
                                    '.$disablecell.' 
                                    style="
                                        font-family: '.get_cell_font_family($cell['name'], $moduleinstance->id).'; 
                                        font-size: '.get_cell_font_size($cell['name'], $moduleinstance->id).'pt; 
                                        font-weight: '.get_cell_bold($cell['name'], $moduleinstance->id).'; 
                                        font-style: '.get_cell_italic($cell['name'], $moduleinstance->id).'; 
                                        text-decoration: '.get_cell_underline($cell['name'], $moduleinstance->id).'; 
                                        text-align: '.get_cell_align($cell['name'], $moduleinstance->id).'; "
                                    onfocus="onFocusInCell(this, conn)" 
                                    oninput="updateTablesCell(this, conn)" 
                                    id='.$cell['name'].'>'.$cell['content'].'</textarea>
                            </td>';
                        }
                    }
                echo '</tr>';
            }
        echo '</tbody>
    </table>
    <input readonly hidden="hidden" id="attached_cells" value="'.implode(', ', $attached_cells).'">
</div>
<script src="https://cdn.jsdelivr.net/npm/interactjs/dist/interact.min.js"></script>';

echo $OUTPUT->footer();

