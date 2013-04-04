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
 * This has miscellaneous tests for various bugs.
 **/
class NeatlineFeatures_Bug_Test extends NeatlineFeatures_Test
{
    /**
     * This tests that null WKT data is encoded as an empty string.
     *
     * @return void
     * @author Eric Rochester <erochest@virginia.edu>
     **/
    public function testNullWkt()
    {
        $this->dispatch('/items/add');

        // I was looking inside the right element using
        // assertNotContentsContains, but it was throwing an error (appeared to
        // be a Zend problem). So I'm falling back on this.
        $this->assertNotContains('wkt: null', $this->response->outputBody());
    }
}
