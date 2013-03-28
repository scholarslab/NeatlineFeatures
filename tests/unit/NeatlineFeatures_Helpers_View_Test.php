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
        $tutil = new NeatlineFeatures_Utils_View();
        $tutil->setEditOptions($this->_item, $this->_title, "", "Elements[38][0]", 0);

        $etext = $tutil->getElementText();
        $this->assertNotNull($etext);
        $this->assertEquals('<b>A Title</b>', $etext->text);
        $this->assertTrue((bool)$etext->html);

        $sutil = new NeatlineFeatures_Utils_View();
        $sutil->setEditOptions($this->_item, $this->_subject, "", "Elements[38][0]", 0);
        $etext = $sutil->getElementText();
        $this->assertNotNull($etext);
        $this->assertEquals('Subject', $etext->text);
        $this->assertFalse((bool)$etext->html);
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
        $tutil->setEditOptions($this->_item, $this->_title, "", "Elements[38][0]", 0);

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
        $sutil->setEditOptions($this->_item, $this->_subject, "", "Elements[38][0]", 0);
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

    private function _addElementTextDelete($item, $field, $value)
    {
        $element = $this->addElementText($item, $field, $value);
        $this->toDelete($element);
        $item->save();
        return $element;
    }

    private function _addFeature($utils, $nlfTable, $item, $data, $append)
    {
        $this->setupCoverageData($item, $data, 0, 1, $append);
        $feature = $nlfTable->createFeatures($item, $utils->getPost());
        $item->save();
        return $feature;
    }

    /**
     * This tests that we can display non-feature coverages.
     *
     * This is a work-around for this spec no longer working, due to O2's 
     * popping up a new window when you click "View Public Page." Technically, 
     * this test should have been a unittest all along, but having it as a 
     * feature keep the planning cleaner.
     *
     * Feature: Display Multiple Coverages
     *  As a theme developer
     *  I want to be able to display coverage data from a mixed collection of fields
     *  So that visitors can see all coverage data.
     *
     *  @javascript
     *  @file_fixture
     *  Scenario: All Non-Feature Coverages
     *    Given I am logged into the admin console
     *    And I replace "themes/default/items/show.php" with "plugins/NeatlineFeatures/features/data/show-display-coverage-delim.php"
     *    And I click "Add a new item"
     *    And I enter "Cucumber: Display All Non-Feature Coverages" for the "Elements-50-0-text"      # Title
     *    And I enter "Display All Non-Feature Coverages" for the "Elements-49-0-text"      # Subject
     *    And I enter "Charlottesville, VA" into "Elements-38-0-free"
     *    And I see "#Elements-38-0-free" contains "Charlottesville, VA"
     *    And I click "add_element_38"
     *    And I enter "UVa" into "Elements-38-1-free"
     *    And I see "#Elements-38-1-free" contains "UVa"
     *    And I click on "Add Item"
     *    And I click "Display All Non-Feature Coverages"
     *    When I click "View Public Page"
     *    Then I should see text "Charlottesville, VA; UVa" in "#dublin-core-coverage"
     *    But I should not see text "null" in "#dublin-core-coverage"
     *
     * @return void
     * @author Eric Rochester <erochest@virginia.edu>
     **/
    public function testDisplayAllNonCoverage()
    {
        $item = new Item;
        $item->save();
        $this->toDelete($item);
        $this->_addElementTextDelete(
            $item,
            $this->_title,
            "Unittest: Display All Non-Feature Coverages",
            1
        );
        $this->_addElementTextDelete(
            $item,
            $this->_subject,
            "Display all non-feature coverages.",
            1
        );
        $this->_addElementTextDelete(
            $item,
            $this->_coverage,
            "Charlottesville, VA",
            1
        );
        $this->_addElementTextDelete(
            $item,
            $this->_coverage,
            "UVa",
            1
        );

        set_current_record('item', $item);
        $text = metadata('item', array('Dublin Core', 'Coverage'), array('delimiter' => '; '));
        $this->assertEquals("Charlottesville, VA; UVa", $text);
    }

    /**
     * This tests that we can display feature-only coverages.
     *
     * This is a work-around for this spec no longer working, due to O2's 
     * popping up a new window when you click "View Public Page." Technically, 
     * this test should have been a unittest all along, but having it as a 
     * feature keep the planning cleaner.
     *
     *  @kml
     *  @file_fixture @javascript
     *  Scenario: All Feature Coverages
     *    Given I am logged into the admin console
     *    And I replace "themes/default/items/show.php" with "plugins/NeatlineFeatures/features/data/show-display-coverage-delim.php"
     *    And I click "Add a new item"
     *    And I enter "Cucumber: Display All Feature Coverages" for the "Elements-50-0-text"      # Title
     *    And I enter "Display All Feature Coverages" for the "Elements-49-0-text"      # Subject
     *    And I click "Use Map" checkbox in "#Elements-38-0-widget"
     *    And I draw a point on "div#Elements-38-0-map.olMap"
     *    And I click on "add_element_38"
     *    And I click "Use Map" checkbox in "#Elements-38-1-widget"
     *    And I draw a line on "div#Elements-38-1-map.olMap"
     *    And I click on "Add Item"
     *    And I click "Display All Feature Coverages"
     *    When I click "View Public Page"
     *    Then the map at "(//div[@id='dublin-core-coverage']//div[@class='nlfeatures'])[1]" should have a point feature
     *    And the map at "(//div[@id='dublin-core-coverage']//div[@class='nlfeatures'])[2]" should have a line feature
     *    But I should not see text "null" in "#dublin-core-coverage .nlfeatures"
     *
     * @return void
     * @author Eric Rochester
     **/
    public function testDisplayAllFeatureCoverage()
    {
        $table = $this->db->getTable('NeatlineFeature');
        $utils = new NeatlineFeatures_Utils_View();
        $utils->setCoverageElement();

        $item = new Item;
        $item->save();
        $this->toDelete($item);

        $this->_addElementTextDelete(
            $item,
            $this->_title,
            "Unittest: Display All Feature Coverages",
            1
        );
        $this->_addFeature($utils, $table, $item, "<kml xmlns=\"http://earth.google.com/kml/2.0\"><Folder><name>OpenLayers export</name><description>Exported on Thu Mar 07 2013 11:18:43 GMT-0500 (EST)</description><Placemark><name>OpenLayers.Feature.Vector_158</name><description>No description available</description><Polygon><outerBoundaryIs><LinearRing><coordinates>-10194865.083145,4313188.6314755 -9803507.4983789,3295658.9110849 -9744803.8606641,5017632.2840536 -10194865.083145,4313188.6314755</coordinates></LinearRing></outerBoundaryIs></Polygon></Placemark><Placemark><name>OpenLayers.Feature.Vector_182</name><description>No description available</description><LineString><coordinates>-8003262.6084573,5487261.3857724 -10097025.686953,6269976.5553037 -10958012.373438,4724114.0954794</coordinates></LineString></Placemark><Placemark><name>OpenLayers.Feature.Vector_194</name><description>No description available</description><LineString><coordinates>-10958012.373438,4724114.0954794 -10488383.271719,3119547.9979404</coordinates></LineString></Placemark></Folder></kml>|3|-9490421.4305667|3972279.4853711|osm\ncoverage a", 0);
        $this->_addFeature($utils, $table, $item, "<kml xmlns=\"http://earth.google.com/kml/2.0\"><Folder><name>OpenLayers export</name><description>Exported on Thu Mar 07 2013 11:53:02 GMT-0500 (EST)</description><Placemark><name>OpenLayers.Feature.Vector_451</name><description>No description available</description><Point><coordinates>-7474929.8690234,6739785.6570224</coordinates></Point></Placemark></Folder></kml>|3|-8739209.3930606|4584602.3035698|osm\ncoverage b", 1);

        $this->dispatch('/');
        set_current_record('item', $item);

        $text = metadata('item', array('Dublin Core', 'Coverage'), array('delimiter' => '; '));
        $this->assertRegExp('/(?sm)<LinearRing>.*;/', $text);
        $this->assertRegExp('/(?sm);.*<Point>/',      $text);
    }

    /**
     * This tests that we can display mixed coverages.
     *
     * This is a work-around for this spec no longer working, due to O2's 
     * popping up a new window when you click "View Public Page." Technically, 
     * this test should have been a unittest all along, but having it as a 
     * feature keep the planning cleaner.
     *
     *  @kml
     *  @file_fixture @javascript
     *  Scenario: Mixed Feature Coverages
     *    Given I am logged into the admin console
     *    And I replace "themes/default/items/show.php" with "plugins/NeatlineFeatures/features/data/show-display-coverage-d
     *    And I click "Add a new item"
     *    And I enter "Cucumber: Display Mixed Feature Coverages" for the "Elements-50-0-text"      # Title
     *    And I enter "Display Mixed Feature Coverages" for the "Elements-49-0-text"      # Subject
     *    And I click "Use Map" checkbox in "#element-38"
     *    And I draw a point on "div#Elements-38-0-map.olMap"
     *    And I click on "add_element_38"
     *    And I enter "UVa" into "Elements-38-1-free"
     *    And I click on "Add Item"
     *    And I click "Display Mixed Feature Coverages"
     *    When I click "View Public Page"
     *    Then the map at "//div[@id='dublin-core-coverage']//div[@class='nlfeatures']" should have a point feature
     *    And I should see text "UVa" in "#dublin-core-coverage"
     *    But I should not see text "null" in "#dublin-core-coverage .nlfeatures"
     *
     * @return void
     * @author Eric Rochester
     **/
    public function testDisplayMixedFeatureCoverage()
    {
        $table = $this->db->getTable('NeatlineFeature');
        $utils = new NeatlineFeatures_Utils_View();
        $utils->setCoverageElement();

        $item = new Item;
        $item->save();
        $this->toDelete($item);

        $this->_addElementTextDelete(
            $item,
            $this->_title,
            "Unittest: Display All Feature Coverages",
            1
        );
        $this->_addFeature($utils, $table, $item, "<kml xmlns=\"http://earth.google.com/kml/2.0\"><Folder><name>OpenLayers export</name><description>Exported on Thu Mar 07 2013 11:18:43 GMT-0500 (EST)</description><Placemark><name>OpenLayers.Feature.Vector_158</name><description>No description available</description><Polygon><outerBoundaryIs><LinearRing><coordinates>-10194865.083145,4313188.6314755 -9803507.4983789,3295658.9110849 -9744803.8606641,5017632.2840536 -10194865.083145,4313188.6314755</coordinates></LinearRing></outerBoundaryIs></Polygon></Placemark><Placemark><name>OpenLayers.Feature.Vector_182</name><description>No description available</description><LineString><coordinates>-8003262.6084573,5487261.3857724 -10097025.686953,6269976.5553037 -10958012.373438,4724114.0954794</coordinates></LineString></Placemark><Placemark><name>OpenLayers.Feature.Vector_194</name><description>No description available</description><LineString><coordinates>-10958012.373438,4724114.0954794 -10488383.271719,3119547.9979404</coordinates></LineString></Placemark></Folder></kml>|3|-9490421.4305667|3972279.4853711|osm\ncoverage a", 0);
        $this->_addElementTextDelete(
            $item,
            $this->_coverage,
            "Charlottesville, VA",
            1
        );

        $this->dispatch('/');
        set_current_record('item', $item);

        $text = metadata('item', array('Dublin Core', 'Coverage'), array('delimiter' => '; '));
        $this->assertRegExp('/(?sm)<LinearRing>.*;/', $text);
        $this->assertStringEndsWith('; Charlottesville, VA', $text);
    }

    /**
     * This tests that we can iterate over non-feature coverages.
     *
     * This is a work-around for this spec no longer working, due to O2's 
     * popping up a new window when you click "View Public Page." Technically, 
     * this test should have been a unittest all along, but having it as a 
     * feature keep the planning cleaner.
     *
     *  @kml
     *  @file_fixture
     *  Scenario: All Non-Feature Coverages
     *    Given I am logged into the admin console
     *    And I replace "themes/default/items/show.php" with "plugins/NeatlineFeatures/features/data/show-display-coverage-indiv.php"
     *    And I click "Add a new item"
     *    And I enter "Cucumber: Iterate All Non-Feature Coverages" for the "Elements-50-0-text"      # Title
     *    And I enter "Iterate All Non-Feature Coverages" for the "Elements-49-0-text"      # Subject
     *    And I enter "Charlottesville, VA" into "Elements-38-0-free"
     *    And I click on "add_element_38"
     *    And I enter "UVa" into "Elements-38-1-free"
     *    And I click on "Add Item"
     *    And I click "Iterate All Non-Feature Coverages"
     *    When I click "View Public Page"
     *    Then I should see the following output in unordered list "#item-coverage":
     *      | Charlottesville, VA |
     *      | UVa                 |
     *    But I should not see text "kml" in "#dublin-core-coverage"
     *
     * @return void
     * @author Eric Rochester
     **/
    public function testIterateAllNonCoverage()
    {
        $item = new Item;
        $item->save();
        $this->toDelete($item);
        $this->_addElementTextDelete(
            $item,
            $this->_title,
            "Unittest: Display All Non-Feature Coverages",
            1
        );
        $this->_addElementTextDelete(
            $item,
            $this->_subject,
            "Display all non-feature coverages.",
            1
        );
        $c0 = $this->_addElementTextDelete(
            $item,
            $this->_coverage,
            "Charlottesville, VA",
            1
        );
        $c1 = $this->_addElementTextDelete(
            $item,
            $this->_coverage,
            "UVa",
            1
        );

        set_current_record('item', $item);
        $covs = metadata(
            'item', array('Dublin Core', 'Coverage'), array('all' => true)
        );
        $this->assertCount(2, $covs);
        $this->assertEquals('Charlottesville, VA', $covs[0]);
        $this->assertEquals('UVa', $covs[1]);
    }

    /**
     * This tests that we can iterate over feature-only coverages.
     *
     * This is a work-around for this spec no longer working, due to O2's 
     * popping up a new window when you click "View Public Page." Technically, 
     * this test should have been a unittest all along, but having it as a 
     * feature keep the planning cleaner.
     *
     *  @kml
     *  @file_fixture @javascript
     *  Scenario: All Feature Coverages
     *    Given I am logged into the admin console
     *    And I replace "themes/default/items/show.php" with "plugins/NeatlineFeatures/features/data/show-display-coverage-indiv.php"
     *    And I click "Add a new item"
     *    And I enter "Cucumber: Iterate All Feature Coverages" for the "Elements-50-0-text"      # Title
     *    And I enter "Iterate All Feature Coverages" for the "Elements-49-0-text"      # Subject
     *    And I click "Use Map" checkbox in "#Elements-38-0-widget"
     *    And I draw a point on "div#Elements-38-0-map.olMap"
     *    And I click on "add_element_38"
     *    And I click "Use Map" checkbox in "#Elements-38-1-widget"
     *    And I draw a line on "div#Elements-38-1-map.olMap"
     *    And I click on "Add Item"
     *    And I click "Iterate All Feature Coverages"
     *    When I click "View Public Page"
     *    Then the map at "(//div[@id='dublin-core-coverage']//div[@class='nlfeatures'])[1]" should have a point feature
     *    And the map at "(//div[@id='dublin-core-coverage']//div[@class='nlfeatures'])[2]" should have a line feature
     *    But I should not see text "kml" in "#dublin-core-coverage .nlfeatures"
     *
     * @return void
     * @author Eric Rochester
     **/
    public function testIterateAllFeatureCoverage()
    {
        $table = $this->db->getTable('NeatlineFeature');
        $utils = new NeatlineFeatures_Utils_View();
        $utils->setCoverageElement();

        $item = new Item;
        $item->save();
        $this->toDelete($item);

        $this->_addElementTextDelete(
            $item,
            $this->_title,
            "Unittest: Display All Feature Coverages",
            1
        );
        $this->_addFeature($utils, $table, $item, "<kml xmlns=\"http://earth.google.com/kml/2.0\"><Folder><name>OpenLayers export</name><description>Exported on Thu Mar 07 2013 11:18:43 GMT-0500 (EST)</description><Placemark><name>OpenLayers.Feature.Vector_158</name><description>No description available</description><Polygon><outerBoundaryIs><LinearRing><coordinates>-10194865.083145,4313188.6314755 -9803507.4983789,3295658.9110849 -9744803.8606641,5017632.2840536 -10194865.083145,4313188.6314755</coordinates></LinearRing></outerBoundaryIs></Polygon></Placemark><Placemark><name>OpenLayers.Feature.Vector_182</name><description>No description available</description><LineString><coordinates>-8003262.6084573,5487261.3857724 -10097025.686953,6269976.5553037 -10958012.373438,4724114.0954794</coordinates></LineString></Placemark><Placemark><name>OpenLayers.Feature.Vector_194</name><description>No description available</description><LineString><coordinates>-10958012.373438,4724114.0954794 -10488383.271719,3119547.9979404</coordinates></LineString></Placemark></Folder></kml>|3|-9490421.4305667|3972279.4853711|osm\ncoverage a", 0);
        $this->_addFeature($utils, $table, $item, "<kml xmlns=\"http://earth.google.com/kml/2.0\"><Folder><name>OpenLayers export</name><description>Exported on Thu Mar 07 2013 11:53:02 GMT-0500 (EST)</description><Placemark><name>OpenLayers.Feature.Vector_451</name><description>No description available</description><Point><coordinates>-7474929.8690234,6739785.6570224</coordinates></Point></Placemark></Folder></kml>|3|-8739209.3930606|4584602.3035698|osm\ncoverage b", 1);

        $this->dispatch('/');
        set_current_record('item', $item);

        $covs = metadata('item', array('Dublin Core', 'Coverage'), array('all' => true, 'no_filter' => true, 'no_escape' => true));
        $this->assertCount(2, $covs);
        $this->assertStringStartsWith('<kml ', $covs[0]);
        $this->assertStringEndsWith('coverage a', $covs[0]);
        $this->assertStringStartsWith('<kml ', $covs[1]);
        $this->assertStringEndsWith('coverage b', $covs[1]);
    }

    /**
     * This tests that we can iterate over mixed coverages.
     *
     * This is a work-around for this spec no longer working, due to O2's 
     * popping up a new window when you click "View Public Page." Technically, 
     * this test should have been a unittest all along, but having it as a 
     * feature keep the planning cleaner.
     *
     *  @kml
     *  @file_fixture @javascript
     *  Scenario: Mixed Feature Coverages
     *    Given I am logged into the admin console
     *    And I replace "themes/default/items/show.php" with "plugins/NeatlineFeatures/features/data/show-display-coverage-indiv.php"
     *    And I click "Add a new item"
     *    And I enter "Cucumber: Iterate Mixed Feature Coverages" for the "Elements-50-0-text"       # Title
     *    And I enter "Iterate Mixed Feature Coverages" for the "Elements-49-0-text"       # Subject
     *    And I click "Use Map" checkbox in "#Elements-38-0-widget"
     *    And I draw a point on "div#Elements-38-0-map.olMap"
     *    And I click on "add_element_38"
     *    And I wait 1 seconds
     *    And I enter "UVa" into "Elements-38-1-free"
     *    And I click on "Add Item"
     *    And I click "Iterate Mixed Feature Coverages"
     *    When I click "View Public Page"
     *    Then the map at "(//div[@id='dublin-core-coverage']//div[@class='nlfeatures'])[1]" should have a point feature
     *    And I should see text "UVa" in "#dublin-core-coverage"
     *    But I should not see text "kml" in "#dublin-core-coverage .nlfeatures"
     *
     * @return void
     * @author Eric Rochester
     **/
    public function testIterateMixedFeatureCoverage()
    {
        $table = $this->db->getTable('NeatlineFeature');
        $utils = new NeatlineFeatures_Utils_View();
        $utils->setCoverageElement();

        $item = new Item;
        $item->save();
        $this->toDelete($item);

        $this->_addElementTextDelete(
            $item,
            $this->_title,
            "Unittest: Display All Feature Coverages",
            1
        );
        $this->_addFeature($utils, $table, $item, "<kml xmlns=\"http://earth.google.com/kml/2.0\"><Folder><name>OpenLayers export</name><description>Exported on Thu Mar 07 2013 11:18:43 GMT-0500 (EST)</description><Placemark><name>OpenLayers.Feature.Vector_158</name><description>No description available</description><Polygon><outerBoundaryIs><LinearRing><coordinates>-10194865.083145,4313188.6314755 -9803507.4983789,3295658.9110849 -9744803.8606641,5017632.2840536 -10194865.083145,4313188.6314755</coordinates></LinearRing></outerBoundaryIs></Polygon></Placemark><Placemark><name>OpenLayers.Feature.Vector_182</name><description>No description available</description><LineString><coordinates>-8003262.6084573,5487261.3857724 -10097025.686953,6269976.5553037 -10958012.373438,4724114.0954794</coordinates></LineString></Placemark><Placemark><name>OpenLayers.Feature.Vector_194</name><description>No description available</description><LineString><coordinates>-10958012.373438,4724114.0954794 -10488383.271719,3119547.9979404</coordinates></LineString></Placemark></Folder></kml>|3|-9490421.4305667|3972279.4853711|osm\ncoverage a", 0);
        $this->_addElementTextDelete(
            $item,
            $this->_coverage,
            "Charlottesville, VA",
            1
        );

        $this->dispatch('/');
        set_current_record('item', $item);

        $covs = metadata('item', array('Dublin Core', 'Coverage'), array('all' => true, 'no_filter' => true, 'no_escape' => true));
        $this->assertCount(2, $covs);
        $this->assertStringStartsWith('<kml ', $covs[0]);
        $this->assertStringEndsWith('coverage a', $covs[0]);
        $this->assertEquals('Charlottesville, VA', $covs[1]);
    }

    /**
     * This tests that we can test for feature data on non-feature coverages.
     *
     * This is a work-around for this spec no longer working, due to O2's 
     * popping up a new window when you click "View Public Page." Technically, 
     * this test should have been a unittest all along, but having it as a 
     * feature keep the planning cleaner.
     *
     *  @kml
     *  @file_fixture
     *  Scenario: Test All Non-Feature Coverages
     *    Given I am logged into the admin console
     *    And I replace "themes/default/items/show.php" with "plugins/NeatlineFeatures/features/data/show-display-coverage-test.php"
     *    And I click "Add a new item"
     *    And I enter "Cucumber: Test Iterate All Non-Feature Coverages" for the "Elements-50-0-text"      # Title
     *    And I enter "Iterate All Non-Feature Coverages" for the "Elements-49-0-text"      # Subject
     *    And I enter "Charlottesville, VA" into "Elements-38-0-free"
     *    And I click "add_element_38"
     *    And I enter "UVa" into "Elements-38-1-free"
     *    And I click on "Add Item"
     *    And I click "Test Iterate All Non-Feature Coverages"
     *    When I click "View Public Page"
     *    Then I should see the following output in unordered list "#item-coverage":
     *      | false |
     *      | false |
     *
     * @return void
     * @author Eric Rochester
     **/
    public function testTestAllNonCoverage()
    {
        $item = new Item;
        $item->save();
        $this->toDelete($item);
        $this->_addElementTextDelete(
            $item,
            $this->_title,
            "Unittest: Display All Non-Feature Coverages",
            1
        );
        $this->_addElementTextDelete(
            $item,
            $this->_subject,
            "Display all non-feature coverages.",
            1
        );
        $c0 = $this->_addElementTextDelete(
            $item,
            $this->_coverage,
            "Charlottesville, VA",
            1
        );
        $c1 = $this->_addElementTextDelete(
            $item,
            $this->_coverage,
            "UVa",
            1
        );

        set_current_record('item', $item);
        $covs = metadata(
            'item', array('Dublin Core', 'Coverage'), array('all' => true)
        );
        $this->assertCount(2, $covs);
        $this->assertFalse(NeatlineFeatures_Functions::isKmlCoverage($covs[0]));
        $this->assertFalse(NeatlineFeatures_Functions::isKmlCoverage($covs[1]));
    }

    /**
     * This tests that we can iterate over feature-only coverages.
     *
     * This is a work-around for this spec no longer working, due to O2's 
     * popping up a new window when you click "View Public Page." Technically, 
     * this test should have been a unittest all along, but having it as a 
     * feature keep the planning cleaner.
     *
     *  @kml
     *  @file_fixture @javascript
     *  Scenario: All Feature Coverages
     *    Given I am logged into the admin console
     *    And I replace "themes/default/items/show.php" with "plugins/NeatlineFeatures/features/data/show-display-coverage-test.php"
     *    And I click "Add a new item"
     *    And I enter "Cucumber: Test Iterate All Feature Coverages" for the "Elements-50-0-text"      # Title
     *    And I enter "Iterate All Feature Coverages" for the "Elements-49-0-text"      # Subject
     *    And I click "Use Map" checkbox in "#Elements-38-0-widget"
     *    And I draw a line on "div#Elements-38-0-map.olMap"
     *    And I click "add_element_38"
     *    And I click "Use Map" checkbox in "#Elements-38-1-widget"
     *    And I draw a point on "div#Elements-38-1-map.olMap"
     *    And I click on "Add Item"
     *    And I click "Cucumber: Test Iterate All Feature Coverages"
     *    When I click "View Public Page"
     *    Then I should see the following output in unordered list "#item-coverage":
     *      | true  |
     *      | true  |
     *
     * @return void
     * @author Eric Rochester
     **/
    public function testTestAllFeatureCoverage()
    {
        $table = $this->db->getTable('NeatlineFeature');
        $utils = new NeatlineFeatures_Utils_View();
        $utils->setCoverageElement();

        $item = new Item;
        $item->save();
        $this->toDelete($item);

        $this->_addElementTextDelete(
            $item,
            $this->_title,
            "Unittest: Display All Feature Coverages",
            1
        );
        $this->_addFeature($utils, $table, $item, "<kml xmlns=\"http://earth.google.com/kml/2.0\"><Folder><name>OpenLayers export</name><description>Exported on Thu Mar 07 2013 11:18:43 GMT-0500 (EST)</description><Placemark><name>OpenLayers.Feature.Vector_158</name><description>No description available</description><Polygon><outerBoundaryIs><LinearRing><coordinates>-10194865.083145,4313188.6314755 -9803507.4983789,3295658.9110849 -9744803.8606641,5017632.2840536 -10194865.083145,4313188.6314755</coordinates></LinearRing></outerBoundaryIs></Polygon></Placemark><Placemark><name>OpenLayers.Feature.Vector_182</name><description>No description available</description><LineString><coordinates>-8003262.6084573,5487261.3857724 -10097025.686953,6269976.5553037 -10958012.373438,4724114.0954794</coordinates></LineString></Placemark><Placemark><name>OpenLayers.Feature.Vector_194</name><description>No description available</description><LineString><coordinates>-10958012.373438,4724114.0954794 -10488383.271719,3119547.9979404</coordinates></LineString></Placemark></Folder></kml>|3|-9490421.4305667|3972279.4853711|osm\ncoverage a", 0);
        $this->_addFeature($utils, $table, $item, "<kml xmlns=\"http://earth.google.com/kml/2.0\"><Folder><name>OpenLayers export</name><description>Exported on Thu Mar 07 2013 11:53:02 GMT-0500 (EST)</description><Placemark><name>OpenLayers.Feature.Vector_451</name><description>No description available</description><Point><coordinates>-7474929.8690234,6739785.6570224</coordinates></Point></Placemark></Folder></kml>|3|-8739209.3930606|4584602.3035698|osm\ncoverage b", 1);

        $this->dispatch('/');
        set_current_record('item', $item);

        $covs = metadata('item', array('Dublin Core', 'Coverage'), array('all' => true, 'no_filter' => true, 'no_escape' => true));
        $this->assertCount(2, $covs);
        $this->assertTrue(NeatlineFeatures_Functions::isKmlCoverage($covs[0]));
        $this->assertTrue(NeatlineFeatures_Functions::isKmlCoverage($covs[1]));
    }

    /**
     * This tests that we can iterate over mixed coverages.
     *
     * This is a work-around for this spec no longer working, due to O2's 
     * popping up a new window when you click "View Public Page." Technically, 
     * this test should have been a unittest all along, but having it as a 
     * feature keep the planning cleaner.
     *
     *  @kml
     *  @file_fixture @javascript
     *  Scenario: Mixed Feature Coverages
     *    Given I am logged into the admin console
     *    And I replace "themes/default/items/show.php" with "plugins/NeatlineFeatures/features/data/show-display-coverage-test.php"
     *    And I click "Add a new item"
     *    And I enter "Cucumber: Test Iterate Mixed Feature Coverages" for the "Elements-50-0-text"      # Title
     *    And I enter "Iterate Mixed Feature Coverages" for the "Elements-49-0-text"      # Subject
     *    And I click "Use Map" checkbox in "#Elements-38-0-widget"
     *    And I draw a line on "div#Elements-38-0-map.olMap"
     *    And I click "add_element_38"
     *    And I wait 15 seconds
     *    And I enter "UVa" into "Elements-38-1-free"
     *    And I click on "Add Item"
     *    And I click "Test Iterate Mixed Feature Coverages"
     *    When I click "View Public Page"
     *    Then I should see the following output in unordered list "#item-coverage":
     *      | true  |
     *      | false |
     *
     * @return void
     * @author Eric Rochester
     **/
    public function testTestMixedFeatureCoverage()
    {
        $table = $this->db->getTable('NeatlineFeature');
        $utils = new NeatlineFeatures_Utils_View();
        $utils->setCoverageElement();

        $item = new Item;
        $item->save();
        $this->toDelete($item);

        $this->_addElementTextDelete(
            $item,
            $this->_title,
            "Unittest: Display All Feature Coverages",
            1
        );
        $this->_addFeature($utils, $table, $item, "<kml xmlns=\"http://earth.google.com/kml/2.0\"><Folder><name>OpenLayers export</name><description>Exported on Thu Mar 07 2013 11:18:43 GMT-0500 (EST)</description><Placemark><name>OpenLayers.Feature.Vector_158</name><description>No description available</description><Polygon><outerBoundaryIs><LinearRing><coordinates>-10194865.083145,4313188.6314755 -9803507.4983789,3295658.9110849 -9744803.8606641,5017632.2840536 -10194865.083145,4313188.6314755</coordinates></LinearRing></outerBoundaryIs></Polygon></Placemark><Placemark><name>OpenLayers.Feature.Vector_182</name><description>No description available</description><LineString><coordinates>-8003262.6084573,5487261.3857724 -10097025.686953,6269976.5553037 -10958012.373438,4724114.0954794</coordinates></LineString></Placemark><Placemark><name>OpenLayers.Feature.Vector_194</name><description>No description available</description><LineString><coordinates>-10958012.373438,4724114.0954794 -10488383.271719,3119547.9979404</coordinates></LineString></Placemark></Folder></kml>|3|-9490421.4305667|3972279.4853711|osm\ncoverage a", 0);
        $this->_addElementTextDelete(
            $item,
            $this->_coverage,
            "Charlottesville, VA",
            1
        );

        $this->dispatch('/');
        set_current_record('item', $item);

        $covs = metadata('item', array('Dublin Core', 'Coverage'), array('all' => true, 'no_filter' => true, 'no_escape' => true));
        $this->assertCount(2, $covs);
        $this->assertTrue(NeatlineFeatures_Functions::isKmlCoverage($covs[0]));
        $this->assertFalse(NeatlineFeatures_Functions::isKmlCoverage($covs[1]));
    }

}
