<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4; */

/**
 * PHP version 5
 *
 * @package     omeka
 * @subpackage  nlfeatures
 * @author      Scholars' Lab <>
 * @author      Eric Rochester <erochest@virginia.edu>
 * @copyright   2011 The Board and Visitors of the University of Virginia
 * @license     http://www.apache.org/licenses/LICENSE-2.0.html Apache 2 License
 */
?><?php

require_once NEATLINE_FEATURES_PLUGIN_DIR . '/tests/NeatlineFeatures_Test.php';

/**
 * This tests the NeatlineFeaturesDataRecordTable model class.
 **/
class NeatlineFeaturesTable_Test extends NeatlineFeatures_Test
{

    /**
     * The NeatlineFeaturesTable being tested.
     *
     * @var NeatlineFeaturesTable
     **/
    var $table;

    /**
     * This sets up for this set of tests.
     *
     * @return void
     * @author Eric Rochester <erochest@virginia.edu>
     **/
    public function setUp()
    {
        parent::setUp();
        $this->table = $this->db->getTable('NeatlineFeature');
    }

    /**
     * This tears down from this set of tests.
     *
     * @return void
     * @author Eric Rochester <erochest@virginia.edu>
     **/
    public function tearDown()
    {
        parent::tearDown();
    }

    /**
     * This tests the create branch of the createOrGetRecord method.
     *
     * @return void
     * @author Eric Rochester <erochest@virginia.edu>
     **/
    public function testCreateFromCreateOrGetRecord()
    {
        $item = new Item();
        $item->save();
        $text = $this->addElementText(
            $item, $this->_coverage, "WKT: POINT(1, 2)\n\nnothing", 0
        );
        $this->toDelete($item);

        $results = $this->table->findBy(array( 'item_id' => $item->id ));
        $this->assertEquals(0, count($results));

        $features = $this->table->createOrGetRecord($item, $text);

        $results = $this->table->findBy(array( 'item_id' => $item->id ));
        $this->assertInternalType('array', $results);
        // Not saved yet, so it's not actually in the DB yet.
        $this->assertEquals(0, count($results));

    }

    /**
     * This tests the get branch of the createOrGetRecord method.
     *
     * @return void
     * @author Eric Rochester <erochest@virginia.edu>
     **/
    public function testGetFromCreateOrGetRecord()
    {
        $item = new Item();
        $item->save();
        $this->toDelete($item);

        $raw  = "WKT: POINT(123, 456)\n\nSomething";
        $text = $this->setupCoverageData($item, $raw, 0, 1);
        $item->save();

        $features = $this->table->createOrGetRecord($item, $text);

        $results = $this->table->findBy(array( 'item_id' => $item->id ));
        $this->assertInternalType('array', $results);
        $this->assertGreaterThan(0, count($results));

        $this->assertTrue((bool)$features->is_map);
    }

    /**
     * This tests getItemFeatures.
     *
     * @return void
     * @author Eric Rochester <erochest@virginia.edu>
     **/
    public function testGetItemFeatures()
    {
        $item = new Item();
        $item->save();
        $this->toDelete($item);

        $this->assertEmpty($this->table->getItemFeatures($item));

        $this->setupCoverageData($item, "WKT: Data\n\nText");
        $item->save();

        $this->assertCount(1, $this->table->getItemFeatures($item));
    }

    /**
     * This tests removeItemFeatures.
     *
     * @return void
     * @author Eric Rochester <erochest@virginia.edu>
     **/
    public function testRemoveItemFeatures()
    {
        $item = new Item();
        $item->save();
        $this->toDelete($item);
        $this->setupCoverageData($item, "WKT: Data\n\nText");
        $item->save();
        $this->assertCount(1, $this->table->getItemFeatures($item));

        $this->table->removeItemFeatures($item);
        $this->assertEmpty($this->table->getItemFeatures($item));
    }

    /**
     * This tests createFeatures if there isn't a map feature.
     *
     * @return void
     * @author Eric Rochester <erochest@virginia.edu>
     **/
    public function testCreateFeaturesNoMap()
    {
        $utils = new NeatlineFeatures_Utils_View();
        $utils->setCoverageElement();
        $et_table = $this->db->getTable('ElementText');

        $item = new Item();
        $item->save();
        $this->toDelete($item);

        $this->setupCoverageData($item, "Just Text.", 0, 0);
        $features = $this->table->createFeatures($item, $utils->getPost());
        $this->assertCount(1, $features);
        $this->assertFalse((bool)$features[0]->is_map);
        $this->assertEquals(
            "Just Text.",
            $et_table->find($features[0]->element_text_id)->getText()
        );
    }

    /**
     * This tests createFeatures for map features.
     *
     * @return void
     * @author Eric Rochester <erochest@virginia.edu>
     **/
    public function testCreateFeaturesUsesMap()
    {
        $utils = new NeatlineFeatures_Utils_View();
        $utils->setCoverageElement();
        $et_table = $this->db->getTable('ElementText');

        $item = new Item();
        $item->save();
        $this->toDelete($item);

        $this->setupCoverageData($item, "WKT: POINT\n\nJust Text.", 0, 1);
        $features = $this->table->createFeatures($item, $utils->getPost());
        $this->assertCount(1, $features);
        $this->assertTrue((bool)$features[0]->is_map);
        $this->assertEquals(
            "WKT: POINT\n\nJust Text.",
            $et_table->find($features[0]->element_text_id)->getText()
        );
    }

    /**
     * This tests updateFeatures.
     *
     * @return void
     * @author Eric Rochester <erochest@virginia.edu>
     **/
    public function testUpdateFeatures()
    {
        $utils = new NeatlineFeatures_Utils_View();
        $utils->setCoverageElement();
        $et_table = $this->db->getTable('ElementText');

        $item = new Item();
        $item->save();
        $this->toDelete($item);

        // First test with no features.
        $features = $this->table->updateFeatures($item, array());
        $this->assertEmpty($features);

        // Now test with two features.
        $this->setupCoverageData($item, "Just Text.", 0, 0);
        $this->setupCoverageData($item, "WKT: POINT\n\nAnd Text.", 0, 1, 1);
        $features = $this->table->updateFeatures($item, $utils->getPost());
        $this->assertCount(2, $features);
        $this->assertFalse((bool)$features[0]->is_map);
        $this->assertEquals(
            "Just Text.",
            $et_table->find($features[0]->element_text_id)->getText()
        );
        $this->assertTrue ((bool)$features[1]->is_map);
        $this->assertEquals(
            "WKT: POINT\n\nAnd Text.",
            $et_table->find($features[1]->element_text_id)->getText()
        );

        // Finally, wipe those out and test with just one feature.
        $etexts = $et_table->fetchObjects(
            $this
                ->db
                ->select()
                ->from($et_table->getTableName())
                ->where('element_id=?', $this->_coverage->id)
                ->where('record_id=?', $item->id)
            );
        foreach ($etexts as $et) {
            $et->delete();
        }

        $this->setupCoverageData($item, "Other Text.", 0, 0);
        $this->table->updateFeatures($item, $utils->getPost());
        $features = $this->table->getItemFeatures($item);
        $this->assertCount(1, $features);
        $this->assertFalse ((bool)$features[0]->is_map);
        $this->assertEquals(
            "Other Text.",
            $et_table->find($features[0]->element_text_id)->getText()
        );
    }

}

