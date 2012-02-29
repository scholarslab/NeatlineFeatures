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
 * This tests the NeatlineFeature model class.
 **/
class NeatlineFeature_Test extends NeatlineFeatures_Test
{
    /**
     * This performs a little set up for these tests.
     *
     * @return void
     * @author Eric Rochester <erochest@virginia.edu>
     **/
    public function setUp()
    {
        parent::setUp();

        $text = "WKT: POINT(1, 2)\n\nnothing";
        $_POST['Elements'][(string)$this->_cutil->getElementId()] = array(
            '0' => array(
                'mapon' => '1',
                'text'  => $text
            )
        );
        $this->_coverage_text = $this->addElementText(
            $this->_item, $this->_coverage, $text, 0
        );
        $this->toDelete($this->_coverage_text);

        $this->_item->save();
    }

    /**
     * This tests whether isMap is true.
     *
     * @return void
     * @author Eric Rochester <erochest@virginia.edu>
     **/
    public function testIsMapTrue()
    {
        $features = $this
            ->db
            ->getTable('NeatlineFeature')
            ->createOrGetRecord($this->_item, $this->_coverage_text);

        $this->assertTrue((bool)$features->is_map);
    }

    /**
     * This tests whether isMap is false.
     *
     * @return void
     * @author Eric Rochester <erochest@virginia.edu>
     **/
    public function testIsMapFalse()
    {
        $text = "Nothing here.";
        $_POST['Elements'][(string)$this->_cutil->getElementId()] = array(
            '0' => array(
                'mapon' => '0',
                'text'  => $text
            )
        );
        $this->_item->save();

        $features = $this
            ->db
            ->getTable('NeatlineFeature')
            ->createOrGetRecord($this->_item, $this->_coverage_text);

        $this->assertFalse((bool)$features->is_map);
    }

}

