<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4; */

/**
 * PHP version 5
 *
 * Licensed under the Apache License, Version 2.0 (the "License"); you may not
 * use this file except in compliance with the License. You may obtain a copy of
 * the License at http://www.apache.org/licenses/LICENSE-2.0 Unless required by
 * applicable law or agreed to in writing, software distributed under the
 * License is distributed on an "AS IS" BASIS, WITHOUT WARRANTIES OR CONDITIONS
 * OF ANY KIND, either express or implied. See the License for the specific
 * language governing permissions and limitations under the License.
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

require_once 'NeatlineFeaturesPlugin.php';

/**
 * This tests the plugin manager class.
 **/
class NeatlineFeaturesPlugin_Test extends Omeka_Test_AppTestCase
{

    // Variables {{{
    /**
     * The NeatlineFeaturesPlugin object.
     *
     * @var NeatlineFeaturesPlugin
     **/
    private $_nf_plugin;
    // }}}

    // Test Infrastructure {{{
    /**
     * Set ups up for each test.
     *
     * @return void
     * @author Eric Rochester <erochest@virginia.edu>
     **/
    public function setUp()
    {
        parent::setUp();

        $this->_nf_plugin = new NeatlineFeaturesPlugin();
        $this->_nf_plugin->install();
    }

    /**
     * Tears down after each test.
     *
     * @return void
     * @author Eric Rochester <erochest@virginia.edu>
     **/
    public function tearDown()
    {
        parent::tearDown();

        $this->_nf_plugin->uninstall();
    }
    // }}}

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

