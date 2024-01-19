<?php

namespace mod_tables;
/**
 * PHPUnit data generator testcase
 *
 * @package     mod_tables
 * @category   phpunit
 * @copyright  2012 Matt Petro
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers \mod_quiz_generator
 */
class generator_test extends \advanced_testcase {
    public function test_table_generator() {
        global $DB, $SITE;

        $this->resetAfterTest(true);

        $this->assertEquals(0, $DB->count_records('tables'));

        /** @var \mod_tables_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('mod_tables');
        $this->assertInstanceOf('mod_tables_generator', $generator);
        $this->assertEquals('tables', $generator->get_modulename());

        $generator->create_instance(array('course'=>$SITE->id, 'name' => 'test name'));
        $generator->create_instance(array('course'=>$SITE->id, 'name' => 'test name'));
        $createtime = time();
        $tables = $generator->create_instance(array('course' => $SITE->id, 'timecreated' => 0, 'name' => 'test name'));
        $this->assertEquals(3, $DB->count_records('tables'));

        $cm = get_coursemodule_from_instance('tables', $tables->id);
        $this->assertEquals($tables->id, $cm->instance);
        $this->assertEquals('tables', $cm->modname);
        $this->assertEquals($SITE->id, $cm->course);

        $context = \context_module::instance($cm->id);
        $this->assertEquals($tables->cmid, $context->instanceid);

        $this->assertEqualsWithDelta($createtime,
            $DB->get_field('tables', 'timecreated', ['id' => $cm->instance]), 2);
    }
    public function test_cell_generator(){
        global $DB, $SITE;

        $this->resetAfterTest(true);

        /** @var \mod_tables_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('mod_tables');
        $tables = $generator->create_instance(array('course' => $SITE->id, 'timecreated' => 0, 'name' => 'test name'));

        $data = array(
            'tableid' => $tables->id,
            'name' => 'A1',
            'content' => "hello",
            'timecreated' => time());

        $DB->insert_record('tables_cells', (object) $data);

        $this->assertEquals($data['name'], $DB->get_record('tables_cells', array('name' => $data['name'],
            'tableid' => $tables->id), '*', MUST_EXIST)->content);
    }
}