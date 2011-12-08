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

/**
 * This class manages the plugin itself. It defines controllers for all the 
 * hooks and filters.
 **/
class NeatlineFeaturesPlugin
{
    // Vars {{{
    /**
     * This is a pointer to the current database.
     *
     * @var Object
     **/
    private $_db;

    /**
     * This is a list of the hooks this manager defines.
     *
     * @var array
     **/
    private static $_hooks = array(
        'install',
        'uninstall'
    );

    /**
     * This is a list of the filters this manager defines.
     *
     * @var array
     **/
    private static $_filters = array(
    );
    // }}}

    // Class setup {{{
    function __construct()
    {
        $this->_db = get_db();
        self::addHooksAndFilters();
    }

    /**
     * Iterate over the hooks and filters, define callbacks.
     *
     * @return void
     * @author Eric Rochester <erochest@virginia.edu>
     **/
    public function addHooksAndFilters()
    {
        foreach (self::$_hooks as $hook_name) {
            $function_name = Inflector::variablize($hook_name);
            get_plugin_broker()->addHook(
                $hook_name,
                array($this, $function_name),
                'NeatlineFeatures'
            );
        }

        foreach (self::$_filters as $filter_name) {
            $function_name = Inflector::variablize($filter_name);
            add_filter($filter_name, array($this, $function_name));
        }
    }
    // }}}

    // Hooks {{{
    /**
     * This installs the NeatlineFeatures plugin.
     *
     * @return void
     * @author Eric Rochester <erochest@virginia.edu>
     **/
    public function install()
    {
    }

    /**
     * This uninstalls the NeatlineFeatures plugin.
     *
     * @return void
     * @author Eric Rochester <erochest@virginia.edu>
     **/
    public function uninstall()
    {
    }
    // }}}
}

