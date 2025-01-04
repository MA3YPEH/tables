<?php

namespace mod_tables;

class mod_tables_generator_testcase extends \advanced_testcase {
    public function test_table_generator() {
        global $DB, $SITE;

        $this->resetAfterTest(true);

        $this->assertEquals(0, $DB->count_records('tables'));

        /** @var \mod_tables_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('mod_tables');
        $this->assertInstanceOf('mod_tables_generator', $generator);
        $this->assertEquals('tables', $generator->get_modulename());

        $generator->create_instance(array('course'=>$SITE->id, 'name' => 'test name'));
        $createtime = time();
        $tables = $generator->create_instance(array('course' => $SITE->id, 'timecreated' => 0, 'name' => 'test name'));
        $this->assertEquals(2, $DB->count_records('tables'));

        $cm = get_coursemodule_from_instance('tables', $tables->id);
        $this->assertEquals($tables->id, $cm->instance);
        $this->assertEquals('tables', $cm->modname);
        $this->assertEquals($SITE->id, $cm->course);

        $context = \context_module::instance($cm->id);
        $this->assertEquals($tables->cmid, $context->instanceid);

        $this->assertEqualsWithDelta($createtime,
            $DB->get_field('tables', 'timecreated', ['id' => $cm->instance]), 2);
    }
}