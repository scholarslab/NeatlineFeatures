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
     * The subject element.
     *
     * @var Element
     **/
    var $_subject;

    /**
     * The coverage element.
     *
     * @var Element
     **/
    var $_coverage;

    /**
     * The NeatlineFeatures_Utils_View for the coverage element.
     *
     * @var NeatlineFeatures_Utils_View
     **/
    var $_cutil;

    /**
     * This is an item to play with.
     *
     * @var Item
     **/
    var $_item;

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
            switch ($row->name) {
            case 'Coverage':
                $this->_coverage = $row;
                $this->_cutil = new NeatlineFeatures_Utils_View(
                    'Elements[38][0]', null, array(), null, $row
                );
                break;
            case 'Title':
                $this->_title = $row;
                break;
            case 'Subject':
                $this->_subject = $row;
                break;
            }
        }

        $this->_item = new Item;
        $this->_item->save();

        $this->addElementText($this->_item, $this->_title, '<b>A Title</b>',
            TRUE);
        $this->addElementText($this->_item, $this->_subject, 'Subject');

        $this->_item->save();
    }

    /**
     * Tear everything back down.
     *
     * @return void
     * @author Eric Rochester <erochest@virginia.edu>
     **/
    public function tearDown()
    {
        parent::tearDown();
        if (isset($this->_item['title'])) {
            $this->_item['title']->delete();
        }
        if (isset($this->_item['subject'])) {
            $this->_item['subject']->delete();
        }
        $this->_item->delete();
        $this->_item = null;
    }

    /**
     * This tests pulling the element ID from $inputNameStem
     *
     * @return void
     * @author Eric Rochester <erochest@virginia.edu>
     **/
    public function testElementId()
    {
        $this->assertEquals(38, $this->_cutil->getElementId());
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
            '<textarea id="Elements-38-0-text" name="Elements[38][0][text]" ' .
            'class="textinput" rows="5" cols="50"></textarea>'
        );

        $actual = new DOMDocument;
        $actual->loadXML($this->_cutil->getRawField());

        $this->assertEqualXMLStructure(
            $expected->firstChild, $actual->firstChild, TRUE
        );
    }

    /**
     * This tests the predicate for whether this is submitted using POST or 
     * not.
     *
     * @return void
     * @author Eric Rochester <erochest@virginia.edu>
     **/
    public function testIsPosted()
    {
        $this->assertFalse($this->_cutil->isPosted());
        $_POST['Elements'][(string)$this->_cutil->getElementId()] = array(
            'o' => 'oops'
        );
        $this->assertTrue($this->_cutil->isPosted());
    }

    /**
     * This tests getHtmlValue, which returns the Elements[id][n][html] field 
     * from the POST request.
     *
     * @return void
     * @author Eric Rochester <erochest@virginia.edu>
     **/
    public function testGetHtmlValue()
    {
        $_POST['Elements'][(string)$this->_cutil->getElementId()] = array(
            '0' => array('html' => '1')
        );
        $this->assertEquals('1', $this->_cutil->getHtmlValue());

        $_POST['Elements'][(string)$this->_cutil->getElementId()] = array(
            '0' => array('html' => '3')
        );
        $this->assertEquals('3', $this->_cutil->getHtmlValue());
    }

    /**
     * This tests the getElementText function, which is a wrapper around the 
     * same function from the view helper.
     *
     * @return void
     * @author Eric Rochester <erochest@virginia.edu>
     **/
    public function testGetElementText()
    {
    }
}

