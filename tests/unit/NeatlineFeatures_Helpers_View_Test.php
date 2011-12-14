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
 * @author      Eric Rochester <erochest@virginia.edu>
 * @copyright   2011 The Board and Visitors of the University of Virginia
 * @license     http://www.apache.org/licenses/LICENSE-2.0.html Apache 2 License
 */
?><?php

require_once 'NeatlineFeatures_Test.php';
require_once 'application/helpers/FormFunctions.php';
require_once 'lib/NeatlineFeatures/Utils/View.php';

/**
 * This tests the utility class for views.
 **/
class NeatlineFeatures_Utils_View_Test extends NeatlineFeatures_Test
{

    /**
     * The title element.
     *
     * @var Element
     **/
    var $_title;

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

        $rows = $this
            ->db
            ->getTable('Element')
            ->findBy(array('name' => 'Coverage'));

        foreach ($rows as $row) {
            if ($row->name == 'Coverage') {
                $this->_coverage = $row;
            } else if ($row->name == 'Title') {
                $this->_title = $row;
            }
        }
    }

    /**
     * This tests pulling the element ID from $inputNameStem
     *
     * @return void
     * @author Eric Rochester <erochest@virginia.edu>
     **/
    public function testElementId()
    {
        $util = new NeatlineFeatures_Utils_View("Elements[38][0]", null,
                                                array(), null, $this->_coverage);
        $this->assertEquals(38, $util->getElementId());
        $util = new NeatlineFeatures_Utils_View("Elements[50][0]", null,
                                                array(), null, $this->_title);
        $this->assertEquals(50, $util->getElementId());
    }

    /**
     * This tests pulling the index from $inputNameStem.
     *
     * @return void
     * @author Eric Rochester <erochest@virginia.edu>
     **/
    public function testGetIndex()
    {
        $util = new NeatlineFeatures_Utils_View("Elements[38][1]", null,
                                                array(), null, null);
        $this->assertEquals(1, $util->getIndex());
        $util = new NeatlineFeatures_Utils_View("Elements[38][3]", null,
                                                array(), null, null);
        $this->assertEquals(3, $util->getIndex());
    }

    /**
     * This tests the TEXTAREA returned by getRawField.
     *
     * @return void
     * @author Eric Rochester <erochest@virginia.edu>
     **/
    public function testGetRawField()
    {
        $expected = new DOMDocument;
        $expected->loadXML(
            '<textarea id="Elements-38-1-text" name="Elements[38][0][text]" ' .
            'class="textinput" rows="5" cols="50"></textarea>'
        );

        $util = new NeatlineFeatures_Utils_View("Elements[38][1]", null,
                                                array(), null, $this->_coverage);
        $actual = new DOMDocument;
        $actual->loadXML($util->getRawField());

        $this->assertEqualXMLStructure(
            $expected->firstChild, $actual->firstChild, TRUE
        );
    }
}

