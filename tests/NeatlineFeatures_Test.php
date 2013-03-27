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

if (!defined('NEATLINE_FEATURES_PLUGIN_DIR')) {
    define('NEATLINE_FEATURES_PLUGIN_DIR', dirname(__FILE__) . '/..');
}
// For some reason, this isn't getting picked up when running tests.
// require_once APP_DIR . '/models/Plugin.php';
// require_once NEATLINE_FEATURES_PLUGIN_DIR . '/NeatlineFeaturesPlugin.php';

/**
 * This is a base class for all NeatlineFeatures unit tests.
 **/
class NeatlineFeatures_Test extends Omeka_Test_AppTestCase
{

    // Variables {{{
    /**
     * The Omeka_Test_Helper_Plugin object.
     *
     * @var Omeka_Test_Helper_Plugin
     **/
    public $phelper;

    /**
     * The user we're logged in as.
     *
     * @var User
     **/
    public $user;

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
     * This is a list of objects created during a test, which will need to be 
     * deleted.
     *
     * @var string
     **/
    var $_todel;

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

        $this->_todel = array();

        $this->user = $this->db->getTable('user')->find(1);
        $this->_authenticateUser($this->user);

        $this->phelper = new Omeka_Test_Helper_Plugin;
        $this->phelper->setUp('NeatlineFeatures');

        $this->_dbHelper = Omeka_Test_Helper_Db::factory($this->application);

        // Retrieve the element for some DC fields.
        $el_table = get_db()->getTable('Element');
        $this->_title = $el_table
            ->findByElementSetNameAndElementName('Dublin Core', 'Title');

        $this->_subject = $el_table
            ->findByElementSetNameAndElementName('Dublin Core', 'Subject');

        $this->_coverage = $el_table
            ->findByElementSetNameAndElementName('Dublin Core', 'Coverage');
        $this->_cutil = new NeatlineFeatures_Utils_View();
        $this->_cutil->setEditOptions(null, $this->_coverage, "", "Elements[38][0]", 0);

        $this->_item = new Item;
        $this->_item->save();
        $this->toDelete($this->_item);

        $t1 = $this->addElementText($this->_item, $this->_title, '<b>A Title</b>',
            1);
        $t2 = $this->addElementText($this->_item, $this->_subject, 'Subject');
        $this->toDelete($t1);
        $this->toDelete($t2);

        $this->_item->save();
    }

    /**
     * This creates the plugin and sets the current plugin directory.
     *
     * @param PluginBroker $plugin_broker The current plugin broker.
     * @param string       $plugin_name   The name of the plugin to load.
     *
     * @return Instance of the plugin class.
     * @author Eric Rochester <erochest@virginia.edu>
     **/
    public function _addHooksAndFilters($plugin_broker, $plugin_name)
    {
        $class_name = $plugin_name . 'Plugin';
        $plugin_broker->setCurrentPluginDirName($plugin_name);
        return (new $class_name);
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

        foreach ($this->_todel as $todel) {
            try {
                $todel->delete();
            } catch (Exception $e) {
            }
        }
        $this->_todel = array();

        $this->_item = null;
    }
    // }}}

    // Null text {{{
    /**
     * This is a null test to make PHPUnit shut up.
     *
     * @return void
     * @author Eric Rochester <erochest@virginia.edu>
     **/
    public function test()
    {
        $this->assertTrue((bool)1);
    }
    // }}}

    // Utility Methods {{{
    /**
     * This cereates and element text and adds it to an item.
     *
     * @param Item    $item    The item to add the data to.
     * @param Element $element The element to add the text to.
     * @param string  $text    The text data.
     * @param bool    $html    Is the text really HTML? (Default is FALSE.)
     *
     * @return ElementText
     * @author Eric Rochester <erochest@virginia.edu>
     **/
    protected function addElementText($item, $element, $text, $html=0)
    {
        $etext = new ElementText;

        $etext->setText($text);
        $etext->html = $html;
        $etext->element_id = $element->id;
        $etext->record_id = $item->id;
        $etext->record_type = get_class($item);
        $etext->save();

        $item[$element->name] = $etext;

        return $etext;
    }

    /**
     * This sets up the POST request for a coverage field. It returns the 
     * ElementText for the coverage field.
     *
     * @return ElementText
     * @author Eric Rochester <erochest@virginia.edu>
     **/
    protected function setupCoverageData(
        $item, $text, $html=0, $map=1, $append=0
    ) {
        $etext = $this->addElementText($item, $this->_coverage, $text, $html);
        $this->toDelete($etext);

        $param = array(
            'mapon' => $map ? '1' : '0',
            'text'  => $text
        );

        if ($append) {
            if (!isset($_POST['Elements'][(string)$this->_coverage->id])) {
                $_POST['Elements'][(string)$this->_coverage->id] = array();
            }
            array_push(
                $_POST['Elements'][(string)$this->_coverage->id],
                $param
            );
        } else {
            $_POST['Elements'][(string)$this->_coverage->id] = array(
                '0' => $param
            );
        }

        return $etext;
    }

    /**
     * This pushes an item onto the queue of items to delete when the step's 
     * over.
     *
     * @param $obj Object This needs to define a ->delete() method, to be 
     * called later.
     *
     * @return void
     * @author Eric Rochester <erochest@virginia.edu>
     **/
    protected function toDelete($obj)
    {
        array_push($this->_todel, $obj);
    }

    // }}}

}

