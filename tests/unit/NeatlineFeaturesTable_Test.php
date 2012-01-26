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

require_once 'NeatlineFeatures_Test.php';

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
            $item, $this->_coverage, "WKT: POINT(1, 2)\n\nnothing", FALSE
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
        $text = $this->setupCoverageData($item, $raw, FALSE, TRUE);
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

}

