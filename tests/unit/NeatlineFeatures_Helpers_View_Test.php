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

/**
 * This tests the utility class for views.
 **/
class NeatlineFeatures_Utils_View_Test extends NeatlineFeatures_Test
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
     * Tear everything back down.
     *
     * @return void
     * @author Eric Rochester <erochest@virginia.edu>
     **/
    public function tearDown()
    {
        parent::tearDown();
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
        $this->assertEquals('1', $this->_cutil->getHtmlValue(0));

        $_POST['Elements'][(string)$this->_cutil->getElementId()] = array(
            '0' => array('html' => '3')
        );
        $this->assertEquals('3', $this->_cutil->getHtmlValue(0));
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
        $tutil = new NeatlineFeatures_Utils_View();
        $tutil->setEditOptions($this->_item, $this->_title);

        $etext = $tutil->getElementText();
        $this->assertNotNull($etext);
        $this->assertCount(1, $etext);
        $this->assertEquals('<b>A Title</b>', $etext[0]->text);
        $this->assertTrue((bool)$etext[0]->html);

        $sutil = new NeatlineFeatures_Utils_View();
        $sutil->setEditOptions($this->_item, $this->_subject);
        $etext = $sutil->getElementText();
        $this->assertNotNull($etext);
        $this->assertCount(1, $etext);
        $this->assertEquals('Subject', $etext[0]->text);
        $this->assertFalse((bool)$etext[0]->html);
    }

    /**
     * This tests the isMap predicate in a POST request, when it is true.
     *
     * @return void
     * @author Eric Rochester <erochest@virginia.edu>
     **/
    public function testIsMapInPostTrue()
    {
        $_POST['Elements'][(string)$this->_cutil->getElementId()] = array(
            '0' => array('mapon' => '1')
        );
        $this->assertTrue($this->_cutil->isMap(0));
    }

    /**
     * This tests the isMap predicate in a POST request, when it is false.
     *
     * @return void
     * @author Eric Rochester <erochest@virginia.edu>
     **/
    public function testIsMapInPostFalse()
    {
        $_POST['Elements'][(string)$this->_cutil->getElementId()] = array(
            '0' => array()
        );
        $this->assertFalse((bool)$this->_cutil->isMap(0));
    }

    /**
      * This tests the isMap predicate outside of a POST request, when it is
      * true.
     *
     * @return void
     * @author Eric Rochester <erochest@virginia.edu>
     **/
    public function testIsMapNoPostTrue()
    {
        $tutil = new NeatlineFeatures_Utils_View();
        $tutil->setEditOptions($this->_item, $this->_title);

        $text = "WKT: something\n\nhi";
        $this->addElementText($this->_item, $this->_coverage, $text);
        $eid = (string)$this->_cutil->getElementId();
        $_POST['Elements'][$eid] = array(
            '0' => array(
                'mapon' => '1',
                'text'  => $text
            )
        );
        $features = get_db()
            ->getTable('NeatlineFeature')
            ->updateFeatures($this->_item, $_POST['Elements'][$eid]);
        $feature = $features[0];
        $feature->is_map = 1;
        $feature->save();

        $this->assertTrue($this->_cutil->isMap(0));
    }

    /**
     * This tests the isMap predicate outside of a POST request, when it is
     * false.
     *
     * @return void
     * @author Eric Rochester <erochest@virginia.edu>
     **/
    public function testIsMapNoPostFalse()
    {
        $sutil = new NeatlineFeatures_Utils_View();
        $sutil->setEditOptions($this->_item, $this->_subject);
        $this->assertFalse((bool)$sutil->isMap());
    }

    /**
     * This gets the first element child of a node.
     *
     * @param DOMNode $node This is the parent node.
     *
     * @return DOMNode
     * @author Eric Rochester <erochest@virginia.edu>
     **/
    protected function getFirstElementChild($node)
    {
        $child = $node->firstChild;

        while ($child !== NULL && $child->nodeType !== XML_ELEMENT_NODE) {
            $child = $child->nextSibling;
        }

        return $child;
    }

    /**
     * This tests that the setCoverageElement method retrieves the correct 
     * element.
     *
     * @return void
     * @author Eric Rochester <erochest@virginia.edu>
     **/
    public function testSetCoverageElement()
    {
        $utils = new NeatlineFeatures_Utils_View();
        $utils->setCoverageElement();

        $el = $utils->getElement();
        $this->assertEquals('Dublin Core', $el->getElementSet()->name);
        $this->assertEquals('Coverage', $el->name);
    }

    /**
     * This tests the POST data returned by the getPost function.
     *
     * @return void
     * @author Eric Rochester <erochest@virginia.edu>
     **/
    public function testGetPost()
    {
        $utils = new NeatlineFeatures_Utils_View();
        $utils->setCoverageElement();

        // No Elements in _POST.
        if (array_key_exists('Elements', $_POST)) {
            unset($_POST['Elements']);
        }
        $this->assertNull($utils->getPost());

        // No element ID in Elements.
        $_POST['Elements'] = array();
        $this->assertNull($utils->getPost());

        // I CAN HAZ VALUE!
        $cid = (string)$this->_coverage->id;
        $_POST['Elements'][$cid] = 'hi';
        $this->assertEquals('hi', $utils->getPost());
    }

}

