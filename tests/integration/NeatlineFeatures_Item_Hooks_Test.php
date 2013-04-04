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
require_once NEATLINE_FEATURES_PLUGIN_DIR . '/lib/NeatlineFeatures/Utils/View.php';
require_once NEATLINE_FEATURES_PLUGIN_DIR . '/models/Table/Table_NeatlineFeature.php';

/**
 * This tests that item update and delete hooks are working properly.
 **/
class NeatlineFeatures_Item_Hooks_Test extends NeatlineFeatures_Test
{

    /**
     * This performs a little set up for this set of tests.
     *
     * @return void
     * @author Eric Rochester <erochest@virginia.edu>
     **/
    public function setUp()
    {
        parent::setUp();
    }

    /**
     * This tears down after the tests.
     *
     * @return void
     * @author Eric Rochester <erochest@virginia.edu>
     **/
    public function tearDown()
    {
        parent::tearDown();
    }

    /**
     * This tests that the item INSERT/UPDATE hook works properly.
     *
     * @return void
     * @author Eric Rochester <erochest@virginia.edu>
     **/
    public function testInsertHook()
    {
        $item = new Item;
        $item->save();
        $this->toDelete($item);

        $text = $this->addElementText($item, $this->_title,
            '<b>testInsertHook</b>', 1);
        $this->toDelete($text);
        $text = $this->addElementText($item, $this->_coverage,
            "WKT: POINT(123, 456)\n\nSomthing", 0);
        $this->toDelete($text);

        $_POST['Elements'][(string)$this->_cutil->getElementId()] = array(
            '0' => array(
                'mapon' => '1',
                'text'  => "WKT: POINT(123, 456)\n\nSomthing"
            )
        );
        $item->save();

        $features = $this
            ->db
            ->getTable('NeatlineFeature')
            ->findBy(array(
                'item_id'         => $item -> id,
                'element_text_id' => $text -> id
            ));

        $this->assertNotNull($features);
        $this->assertGreaterThan(0, count($features));

        $feature = $features[0];
        $this->assertTrue((bool)$feature->is_map);
    }

    /**
     * This tests that the DELETE hook works properly.
     *
     * @return void
     * @author Eric Rochester <erochest@virginia.edu>
     **/
    public function testDeleteHook()
    {
        $item = new Item;
        $item->save();

        $text = $this->addElementText($item, $this->_title,
            '<b>testDeleteHook</b>', 1);
        $this->toDelete($text);
        $text = $this->addElementText($item, $this->_coverage,
            "WKT: POINT(123, 456)\n\nSomthing", 0);
        $this->toDelete($text);

        $_POST['Elements'][(string)$this->_cutil->getElementId()] = array(
            '0' => array(
                'mapon' => '1',
                'text'  => ''
            )
        );
        $item->save();

        $item_id = $item->id;
        $item->delete();

        $results = $this
            ->db
            ->getTable('NeatlineFeature')
            ->findBy(array( 'item_id' => $item_id ));

        $this->assertEmpty($results);
    }

}

