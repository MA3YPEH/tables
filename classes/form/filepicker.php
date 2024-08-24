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

require_once("$CFG->libdir/formslib.php");

class pick_excel_file extends moodleform {
    public function definition() {
        $mform = $this->_form;

        $maxbytes = USER_CAN_IGNORE_FILE_SIZE_LIMITS;

        $mform->addElement('filepicker', 'xlsxfile',
            get_string('filepicker', 'mod_tables'),
            null,
            [
                'maxbytes' => $maxbytes,
                'accepted_types' => '.xlsx',
            ]
        );

        $this->add_action_buttons(true, 'Загрузить');
    }

    function validation($data, $files) {
        return [];
    }
}