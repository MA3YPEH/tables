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
 * Update cell size content for mod_tables.
 *
 * @package     mod_tables
 * @copyright   2023 Mazur Egor <mazur.eh@edu.spbstu.ru>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require(__DIR__.'/../../config.php');
require_once(__DIR__.'/lib.php');

global $CFG, $PAGE, $DB, $USER;

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

$PAGE->set_url('/mod/tables/load_excel.php?id='.$id);
$PAGE->requires->jquery();

require_login($course, false, $cm);

$context = context_module::instance($cm->id);

require_capability('moodle/course:manageactivities', $context);

$file = 'upload/upload.xlsx';

if (file_exists($file) && is_file($file) && is_readable($file)) {
    $z=new ZipArchive();
    if ($z->open($file)) {

        $str_values=array();

        if ($fp=$z->getStream('xl/workbook.xml')){
            $data='';
            while (!feof($fp)) {
                $data.=fread($fp, 1024);
            }
            fclose($fp);

            $xml=simplexml_load_string($data);

            if(isset($xml->sheets)){
                $sheets = $xml->sheets;
                $sheets_count = count($sheets->sheet);
            }
        }

        // Прочитать строковые значения
        if ($fp=$z->getStream('xl/sharedStrings.xml')) {
            $data='';
            while (!feof($fp)) {
                $data.=fread($fp, 1024);
            }
            fclose($fp);

            $xml=simplexml_load_string($data);

            if (isset($xml->si) && count($xml->si)) {
                foreach ($xml->si as $data) {
                    $data=(array)$data;
                    $str_values[]=$data['t'];
                }
            }
        }

        $sheets_to_load = array();

        for($i = 1; $i<=$sheets_count; $i++){
            $xls_values=array();

            // Прочитать значения каждого листа
            if ($fp=$z->getStream('xl/worksheets/sheet'.$i.'.xml')) {
                $data='';
                while (!feof($fp)) {
                    $data.=fread($fp, 1024);
                }
                fclose($fp);

                $xml=simplexml_load_string($data);

                if (isset($xml->sheetData)) {
                    $sheetData=(array)($xml->sheetData);
                    if (isset($sheetData['row']) && count($sheetData['row'])>0) {

                        if (!is_array($sheetData['row'])) {
                            $sheetData['row'] = array(0 => $sheetData['row']);
                        }

                        foreach($sheetData['row'] as $row) {
                            $row=(array)$row;

                            // Особый случай для одноколоночной страницы
                            if (!is_array($row['c'])) {
                                $row['c']=array($row['c']);
                            }

                            foreach ($row['c'] as $col) {
                                $col=(array)$col;

                                // Столбец и колонка
                                preg_match('/([A-Z]+)(\d+)/',$col['@attributes']['r'],$matches);

                                // Строка из списка
                                if (isset($col['@attributes']['t'])
                                    && $col['@attributes']['t']=='s'
                                    && isset($str_values[$col['v']]))
                                {
                                    $xls_values[$matches[2]][$matches[1]]=$str_values[$col['v']];

                                }
                                // Непосредственное значение
                                elseif (isset($col['v'])) {
                                    $xls_values[$matches[2]][$matches[1]]=$col['v'];
                                }

                            }
                        }
                        $sheets_to_load[$i] = $xls_values;
                    }
                }
            }
        }
        $z->close();

        for($i = 1; $i <= count($sheets_to_load); $i++){
            $data = array (
                'name' => $i,
                'tableid' => $moduleinstance->id);


            if($DB->record_exists('tables_sheets', $data)){
                $sheetid = $DB->get_record('tables_sheets', $data)->id;
                $DB->delete_records('tables_sheets_cells', array('sheetid' => $sheetid));
                for($j = 1, $k = 0; $j <= count($sheets_to_load[$i]); $j++, $k++){
                    $sheet_key = array_keys($sheets_to_load[$i])[$k];
                    $keys = array_keys($sheets_to_load[$i][$sheet_key]);
                    foreach($keys as $key){
                        $cell_data = array (
                            'sheetid' => $sheetid,
                            'name' => $key.$j);

                        if($DB->record_exists('tables_sheets_cells', $cell_data)){
                            $cell_data = $DB->get_record('tables_sheets_cells', $cell_data, '*', MUST_EXIST);
                            $cell_data->content = $sheets_to_load[$i][$sheet_key][$key];
                            $cell_data->timmodified = time();
                            $DB->update_record('tables_sheets_cells', (object)$cell_data);
                        }
                        else{
                            $cell_data['content'] = $sheets_to_load[$i][$sheet_key][$key];
                            $cell_data['timecreated'] = time();
                            $DB->insert_record('tables_sheets_cells', $cell_data);
                        }
                    }
                }
            }
            else{
                $DB->insert_record('tables_sheets', $data);
                $sheetid = $DB->get_record('tables_sheets', $data)->id;
                for($j = 1, $k = 0; $j <= count($sheets_to_load[$i]); $j++, $k++){
                    $sheet_key = array_keys($sheets_to_load[$i])[$k];
                    $keys = array_keys($sheets_to_load[$i][$sheet_key]);
                    foreach($keys as $key){
                        $cell_data = array (
                            'sheetid' => $sheetid,
                            'name' => $key.$j);

                        $cell_data['content'] = $sheets_to_load[$i][$sheet_key][$key];
                        $cell_data['timecreated'] = time();
                        $DB->insert_record('tables_sheets_cells', $cell_data);
                    }
                }
            }
        }
        redirect('view.php?id='.$id);
    }
    else {
        // что-то не так с доступностью файла
    }
}
else {
    // что-то не так с наличием файла
}
