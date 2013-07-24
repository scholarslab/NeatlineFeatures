<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4; */

/**
 * PHP version 5
 *
 * @package     omeka
 * @subpackage  nlfeatures
 * @author      Scholars' Lab <>
 * @author      Bethany Nowviskie <bethany@virginia.edu>
 * @author      Adam Soroka <ajs6f@virginia.edu>
 * @author      David McClure <david.mcclure@virginia.edu>
 * @author      Eric Rochester <erochest@virginia.edu>
 * @copyright   2011 The Board and Visitors of the University of Virginia
 * @license     http://www.apache.org/licenses/LICENSE-2.0.html Apache 2 License
 */
?><?php

require_once NEATLINE_FEATURES_PLUGIN_DIR . '/tests/NeatlineFeatures_Test.php';

/**
 * This tests the plugin manager class.
 **/
class NeatlineFeaturesPlugin_Test extends NeatlineFeatures_Test
{

    // Tests {{{
    /**
     * This tests NeatlineFeaturesPlugin->install().
     *
     * This method doesn't actually do anything right now, so there isn't much 
     * to test.
     *
     * @return void
     * @author Eric Rochester <erochest@virginia.edu>
     **/
    public function testInstall()
    {
        $db     = $this->db;
        $table  = "{$db->prefix}neatline_features";
        $tables = $db->listTables();
        $this->assertContains($table, $tables);

        $columns = array();
        foreach ($db->describeTable($table) as $col) {
            array_push($columns, $col['COLUMN_NAME']);
        }

        $this->assertContains('geo',        $columns);
        $this->assertContains('zoom',       $columns);
        $this->assertContains('center_lat', $columns);
        $this->assertContains('center_lon', $columns);
        $this->assertContains('base_layer', $columns);
    }

    /**
     * This tests NeatlineFeaturesPlugin->uninstall().
     *
     * This method doesn't actually do anything right now, so there isn't much 
     * to test.
     *
     * @return void
     * @author Eric Rochester <erochest@virginia.edu>
     **/
    public function testUninstall()
    {
        $this
            ->phelper
            ->pluginBroker
            ->callHook('uninstall', array(), 'NeatlineFeatures');

        $db     = $this->db;
        $tables = $db->listTables();
        $this->assertNotContains(
            "{$db->prefix}neatline_features",
            $tables
        );
    }

    /**
     * This tests the after_save_item hook.
     *
     * @return void
     * @author Eric Rochester <erochest@virginia.edu>
     **/
    public function testAfterSaveItem()
    {
        $text = "WKT: data\n\ntext";
        $cov  = $this->setupCoverageData($this->_item, $text);
        $this->_item->save();
        $features = $this
            ->db
            ->getTable('NeatlineFeature')
            ->getItemFeatures($this->_item);
        $this->assertEquals(1, count($features));
    }

    /**
     * This tests the before_delete_item hook.
     *
     * @return void
     * @author Eric Rochester <erochest@virginia.edu>
     **/
    public function testBeforeDeleteItem()
    {
        $text = "WKT: data\n\ntext";
        $cov  = $this->setupCoverageData($this->_item, $text);
        $this->_item->save();

        $db    = $this->db;
        $table = $db->getTable('NeatlineFeature');

        $features = $table->fetchAll(
            $db->select()->from($table->getTableName())
        );
        $this->assertCount(1, $features);

        $this->_item->delete();
        $features = $table->fetchAll(
            $db->select()->from($table->getTableName())
        );
        $this->assertEmpty($features);
    }

    // }}}
}

