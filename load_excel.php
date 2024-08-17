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

$PAGE->set_url('/mod/tables/attach_cells.php');
$PAGE->requires->jquery();

if (file_exists('G:\test.xlsx') && is_file('G:\test.xlsx') && is_readable('G:\test.xlsx')) {
    $z=new ZipArchive();
    if ($z->open('G:\test.xlsx')) {

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
                'tableid' => optional_param('table_id', 0, PARAM_INT));

            if($DB->record_exists('tables_sheets', $data)){
                $sheetid = $DB->get_record('tables_sheets', $data)->id;
                $DB->delete_records('tables_sheets_cells', array('sheetid' => $sheetid));
                
                for($j = array_keys($sheets_to_load[$i])[0]; $j <= count($sheets_to_load[$i]); $j++){
                    $keys = array_keys($sheets_to_load[$i][$j]);
                    foreach($keys as $key){
                        $cell_data = array (
                            'sheetid' => $i,
                            'name' => $key.$j);

                        if($DB->record_exists('tables_sheets_cells', $cell_data)){
                            $cell_data['content'] = $sheets_to_load[$i][$j][$key];
                            $cell_data['timemodified'] = time();
                            $DB->update_record('tables_sheets_cells', (object)$cell_data);
                        }
                        else{
                            $cell_data['content'] = $sheets_to_load[$i][$j][$key];
                            $cell_data['timecreated'] = time();
                            $DB->insert_record('tables_sheets_cells', $cell_data);
                        }
                    }
                }
            }
            else{
                $DB->insert_record('tables_sheets', $data);
                for($j = array_keys($sheets_to_load[$i])[0]; $j <= count($sheets_to_load[$i]); $j++){
                    $keys = array_keys($sheets_to_load[$i][$j]);
                    foreach($keys as $key){
                        $cell_data = array (
                            'sheetid' => $i,
                            'name' => $key.$j);

                        $cell_data['content'] = $sheets_to_load[$i][$j][$key];
                        $cell_data['timecreated'] = time();
                        $DB->insert_record('tables_sheets_cells', $cell_data);
                    }
                }
            }
        }
    }
    else {
        // что-то не так с доступностью файла
    }
}
else {
    // что-то не так с наличием файла
}

//
//if (file_exists('G:\test.xlsx') && is_file('G:\test.xlsx') && is_readable('G:\test.xlsx')) {
//    $z=new ZipArchive();
//    if ($z->open('G:\test.xlsx')) {
//
//        $str_values=array();
//
//        if ($fp=$z->getStream('xl/workbook.xml')){
//            $data='';
//            while (!feof($fp)) {
//                $data.=fread($fp, 1024);
//            }
//            fclose($fp);
//
//            $xml=simplexml_load_string($data);
//
//            if(isset($xml->sheets)){
//                $sheets = $xml->sheets;
//                $sheets_count = count($sheets->sheet);
//            }
//        }
//
//        // Прочитать строковые значения
//        if ($fp=$z->getStream('xl/sharedStrings.xml')) {
//            $data='';
//            while (!feof($fp)) {
//                $data.=fread($fp, 1024);
//            }
//            fclose($fp);
//
//            $xml=simplexml_load_string($data);
//
//            if (isset($xml->si) && count($xml->si)) {
//                foreach ($xml->si as $data) {
//                    $data=(array)$data;
//                    $str_values[]=$data['t'];
//                }
//            }
//        }
//
//        print_r($str_values);
//        echo '</br> ////////////////////';
//
//        $sheets_to_load = array();
//
//        for($i = 1; $i<=$sheets_count; $i++){
//            $xls_values=array();
//
//            // Прочитать значения каждого листа
//            if ($fp=$z->getStream('xl/worksheets/sheet'.$i.'.xml')) {
//                $data='';
//                while (!feof($fp)) {
//                    $data.=fread($fp, 1024);
//                }
//                fclose($fp);
//
//                $xml=simplexml_load_string($data);
//
//                if (isset($xml->sheetData)) {
//                    $sheetData=(array)($xml->sheetData);
//                    if (isset($sheetData['row']) && count($sheetData['row'])>0) {
//
//                        if (!is_array($sheetData['row'])) {
//                            $sheetData['row'] = array(0 => $sheetData['row']);
//                        }
//
//                        foreach($sheetData['row'] as $row) {
//                            $row=(array)$row;
//
//                            // Особый случай для одноколоночной страницы
//                            if (!is_array($row['c'])) {
//                                $row['c']=array($row['c']);
//                            }
//
//                            foreach ($row['c'] as $col) {
//                                $col=(array)$col;
//
////
////                                print_r($col);
//
//                                // Столбец и колонка
//                                preg_match('/([A-Z]+)(\d+)/',$col['@attributes']['r'],$matches);
//                                // Строка из списка
//                                if (isset($col['@attributes']['t'])
//                                    && $col['@attributes']['t']=='s'
//                                    && isset($str_values[$col['v']]))
//                                {
//                                    $xls_values[$matches[2]][$matches[1]]=$str_values[$col['v']];
//
//                                }
//                                // Непосредственное значение
//                                elseif (isset($col['v'])) {
//                                    $xls_values[$matches[2]][$matches[1]]=$col['v'];
//                                }
//                                echo '</br> List'.$i;
//                                print_r($xls_values);
//
//                            }
//                        }
//                        $sheets_to_load[$i] = $xls_values;
//                    }
//                }
//            }
//        }
//        $z->close();
//
//        echo '</br> ///////////';
//
//        print_r($sheets_to_load);
//
//        echo '</br> ///////////';
//
//        for($i = 1; $i <= count($sheets_to_load); $i++){
//            $data = array (
//                'name' => $i,
//                'tableid' => $moduleinstance->id);
//
//            //array_keys($sheets_to_load[$i]);
//
//            if($DB->record_exists('tables_sheets', $data)){
//                for($j = 1, $k = array_keys($sheets_to_load[$i])[$j-1]; $j <= count($sheets_to_load[$i]); ++$j){
//                    echo 'KEY = '.$k.' j = '.$j;
//                    $keys = array_keys($sheets_to_load[$i][$k]);
//                    foreach($keys as $key){
//                        $cell_data = array (
//                            'sheetid' => $i,
//                            'name' => $key.$j);
//
//                        if($DB->record_exists('tables_sheets_cells', $cell_data)){
//                            $cell_data['content'] = $sheets_to_load[$i][$j][$key];
//                            $cell_data['timemodified'] = time();
//                            echo '</br> List '.$i.' string '.$j;
//                            print_r($cell_data);
//                            //$DB->update_record('tables_sheets_cells', (object)$cell_data);
//                        }
//                        else{
//                            $cell_data['content'] = $sheets_to_load[$i][$j][$key];
//                            $cell_data['timecreated'] = time();
//                            echo '</br> List '.$i.' string '.$j;
//                            print_r($cell_data);
//                            //$DB->insert_record('tables_sheets_cells', $cell_data);
//                        }
//                    }
//                }
//            }
//            else{
//                //$DB->insert_record('tables_sheets', $data);
//                for($j = 1; $j <= count($sheets_to_load[$i]); $j++){
//                    $keys = array_keys($sheets_to_load[$i][$j]);
//                    foreach($keys as $key){
//                        $cell_data = array (
//                            'sheetid' => $i,
//                            'name' => $key.$j);
//
//                        $cell_data['content'] = $sheets_to_load[$i][$j][$key];
//                        $cell_data['timecreated'] = time();
//                        echo '</br> List '.$i.' string '.$j;
//                        print_r($cell_data);
//                        //$DB->insert_record('tables_sheets_cells', $cell_data);
//                    }
//                }
//            }
//        }
//    }
//    else {
//        // что-то не так с доступностью файла
//    }
//}
//else {
//    // что-то не так с наличием файла
//}