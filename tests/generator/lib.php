<?php
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

defined('MOODLE_INTERNAL') || die();

/**
 * tables module test data generator class
 *
 * @package mod_tables
 * @copyright 2012 The Open University
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class mod_tables_generator extends testing_module_generator {

    public function create_instance($record = null, array $options = null) {
        global $CFG;

        require_once($CFG->dirroot.'/mod/tables/lib.php');
        $record = (object)(array)$record;

        $defaulttablesettings = array(
            'name'               => 'Tables',
            'course'              => 0,
            'columncount'           => 10,
            'rowcount'              => 10,
            'timecreated'            => time(),
            'timemodified'           => time()
        );

        foreach ($defaulttablesettings as $name => $value) {
            if (!isset($record->{$name})) {
                $record->{$name} = $value;
            }
        }

        if (isset($record->gradepass)) {
            $record->gradepass = unformat_float($record->gradepass);
        }

        return parent::create_instance($record, (array)$options);
    }

    /**
     * Create a tables override (either user or group).
     *
     * @param array $data must specify tablesid, and one of userid or groupid.
     */
    public function create_override(array $data): void {
        global $DB;

        // Validate.
        if (!isset($data['id'])) {
            throw new coding_exception('Must specify tables (id) when creating a tables override.');
        }

        if (!isset($data['course'])) {
            throw new coding_exception('Must specify one of userid or groupid when creating a tables override.');
        }

        // Create the override.
        $DB->insert_record('tables', (object) $data);

        }
}
