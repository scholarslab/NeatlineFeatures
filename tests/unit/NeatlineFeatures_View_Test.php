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

require_once 'NeatlineFeatures_Test.php';
require_once 'application/helpers/FormFunctions.php';

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
        $this->dispatch('/items/add');
        $this->assertNotQuery("#element-38//label.use-html", "'Use HTML' found.");
    }
}

