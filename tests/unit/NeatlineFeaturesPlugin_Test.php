<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4; */

/**
 * PHP version 5
 *
 * @package     omeka
 * @subpackage  neatline
 * @author      Scholars' Lab <>
 * @author      Bethany Nowviskie <bethany@virginia.edu>
 * @author      Adam Soroka <ajs6f@virginia.edu>
 * @author      David McClure <david.mcclure@virginia.edu>
 * @author      Eric Rochester <erochest@virginia.edu>
 * @copyright   2011 The Board and Visitors of the University of Virginia
 * @license     http://www.apache.org/licenses/LICENSE-2.0.html Apache 2 License
 */
?><?php

require_once 'NeatlineFeatures_Test.php';

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
    }
    // }}}
}

