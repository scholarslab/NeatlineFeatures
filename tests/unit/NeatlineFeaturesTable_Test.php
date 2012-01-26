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
     * This sets up for this set of tests.
     *
     * @return void
     * @author Eric Rochester <erochest@virginia.edu>
     **/
    public function setUp()
    {
        parent::setUp();
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

        $results = $this
            ->db
            ->getTable('NeatlineFeature')
            ->findBy(array( 'item_id' => $item->id ));
        $this->assertEquals(0, count($results));

        $features = $this
            ->db
            ->getTable('NeatlineFeature')
            ->createOrGetRecord($item, $text);

        $results = $this
            ->db
            ->getTable('NeatlineFeature')
            ->findBy(array( 'item_id' => $item->id ));
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

        $raw  = "WKT: POINT(123, 456)\n\nSomthing";
        $text = $this->addElementText($item, $this->_coverage,
            $raw, FALSE);
        $this->toDelete($text);

        $_POST['Elements'][(string)$this->_cutil->getElementId()] = array(
            '0' => array(
                'mapon' => '1',
                'text'  => $raw
            )
        );
        $item->save();

        $features = $this
            ->db
            ->getTable('NeatlineFeature')
            ->createOrGetRecord($item, $text);

        $results = $this
            ->db
            ->getTable('NeatlineFeature')
            ->findBy(array( 'item_id' => $item->id ));
        $this->assertInternalType('array', $results);
        $this->assertGreaterThan(0, count($results));

        $this->assertTrue((bool)$features->is_map);
    }

}

