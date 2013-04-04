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
 * This tests the view for the coverage field.
 **/
class NeatlineFeatures_View_Test extends NeatlineFeatures_Test
{
    /**
     * The coverage element.
     *
     * @var Element
     **/
    var $_coverage;

    /**
     * This performs a little set up for this set of tests.
     *
     * @return void
     * @author Eric Rochester <erochest@virginia.edu>
     **/
    public function setUp()
    {
        parent::setUp();

        $this->_coverage = $this
            ->db
            ->getTable('Element')
            ->findBy(array('name' => 'Coverage'));
    }

    /**
     * This tests that there is no "Use HTML" check for the coverage field.
     *
     * @return void
     * @author Eric Rochester <erochest@virginia.edu>
     **/
    public function testUseHTML()
    {
        // This is no longer the case.
        // $this->dispatch('/items/add');
        // $this->assertNotQuery("#element-38//label.use-html", "'Use HTML' found.");
    }
}

