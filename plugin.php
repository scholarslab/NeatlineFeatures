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

// constants {{{
if (!defined('NEATLINE_FEATURES_PLUGIN_VERSION')) {
    define(
        'NEATLINE_FEATURES_PLUGIN_VERSION',
        get_plugin_ini('NeatlineFeatures', 'version')
    );
}
if (!defined('NEATLINE_FEATURES_PLUGIN_DIR')) {
    define(
        'NEATLINE_FEATURES_PLUGIN_DIR',
        dirname(__FILE__)
    );
}
// }}}

// requires {{{
require_once NEATLINE_FEATURES_PLUGIN_DIR . '/NeatlineFeaturesPlugin.php';
// }}}

// Instantiate the manager.
new NeatlineFeaturesPlugin;

