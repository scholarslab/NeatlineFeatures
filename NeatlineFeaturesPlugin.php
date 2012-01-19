<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4; */

/**
 * PHP version 5
 *
 * This is the Plugin object that sets up and contains the hooks and filters
 * for the plugins.
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

require_once NEATLINE_FEATURES_PLUGIN_DIR .
    '/lib/NeatlineFeatures/Utils/View.php';
require_once NEATLINE_FEATURES_PLUGIN_DIR .
    '/lib/NeatlineFeatures_Functions.php';

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
        'uninstall',
        'admin_theme_header',
        'public_theme_header'
    );

    /**
     * This is a list of the filters this manager defines.
     *
     * @var array
     **/
    private static $_filters = array(
        array('formItemDublinCoreCoverage',
              array('Form', 'Item', 'Dublin Core', 'Coverage')),
        array('elementFormDisplayHtmlFlag',
              'element_form_display_html_flag'),
        array('displayItemDublinCoreCoverage',
              array('Display', 'Item', 'Dublin Core', 'Coverage'))
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
        foreach (self::$_hooks as $hookName) {
            $functionName = Inflector::variablize($hookName);
            get_plugin_broker()->addHook(
                $hookName,
                array($this, $functionName),
                'NeatlineFeatures'
            );
        }

        foreach (self::$_filters as $filterInfo) {
            $functionName = $filterInfo[0];
            add_filter($filterInfo[1], array($this, $functionName));
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

    /**
     * This is a utility function that appends a javascript URL.
     *
     * @param $uri string This is the URI to queue.
     *
     * @return void
     * @author Eric Rochester <erochest@virginia.edu>
     **/
    private function _queueJsUri($uri)
    {
        // We are also outputting the script tags to load OpenLayers here.
        $head = __v()->headScript();
        $head->appendScript(
            '',
            'text/javascript',
            array('src' => 'http://openlayers.org/api/OpenLayers.js')
        );
    }

    /**
     * This queues javascript and CSS for the admin header.
     *
     * @return void
     * @author Eric Rochester <erochest@virginia.edu>
     **/
    public function adminThemeHeader()
    {
        queue_css('nlfeatures');
        queue_css('nlfeature-editor');

        // We are also outputting the script tags to load OpenLayers here.
        $this->_queueJsUri('http://openlayers.org/api/OpenLayers.js');

        queue_js('nlfeatures');
        queue_js('editor/edit_features');
        queue_js('nlfeatures-simpletab');
        queue_js('nlfeatures-init');
    }

    /**
     * This queues javascript and CSS for the public header.
     *
     * @return void
     * @author Eric Rochester <erochest@virginia.edu>
     **/
    public function publicThemeHeader()
    {
        queue_css('nlfeatures');

        // We are also outputting the script tags to load OpenLayers here.
        $this->_queueJsUri('http://openlayers.org/api/OpenLayers.js');

        queue_js('nlfeatures');
        queue_js('nlfeatures-init');
    }

    // }}}

    // Filters {{{
    /**
     * This overrides the definition for the coverage form input.
     *
     * @param string       $html          An empty string.
     * @param string       $inputNameStem The stem of the input name.
     * @param string       $value         The initial value for the input.
     * @param array        $options       Additional options.
     * @param Omeka_Record $record        The element's record.
     * @param Element      $element       The Element.
     *
     * @return string The string containing the HTML for the customized element
     * form.
     * @author Eric Rochester <erochest@virginia.edu>
     **/
    public function formItemDublinCoreCoverage($html, $inputNameStem, $value,
        $options, $record, $element)
    {
        $util = new NeatlineFeatures_Utils_View();
        $util->setEditOptions(
            $inputNameStem, $value, $options, $record, $element
        );
        return $util->getEditControl();
    }

    /**
     * This turns off displaying the element form for the DC:Coverage field.
     *
     * @param string  $html    An empty string.
     * @param Element $element The Element.
     *
     * @return string The string containing the HTML for the "Use HTML"
     * element.
     * @author Eric Rochester <erochest@virginia.edu>
     **/
    public function elementFormDisplayHtmlFlag($html, $element)
    {
        if ($element->name == 'Coverage' &&
            $element->getElementSet()->name == 'Dublin Core') {
            return '<span>&nbsp;</span>';
        } else {
            return $html;
        }
    }

    /**
     * This displays the coverage data as a map, if applicable.
     *
     * @param string           $text        The original text for the element.
     * @param Omeka_Record     $record      The record that this text applies
     * to.
     * @param ElementText|null $elementText The ElementText record that stores
     * this text. (This is optional and defaults to null.)
     *
     * @return The HTML to generate the map.
     * @author Eric Rochester <erochest@virginia.edu>
     **/
    public function displayItemDublinCoreCoverage($text, $record, $elementText)
    {
        return NeatlineFeatures_Functions::displayCoverage(
            $text, $record, $elementText
        );
    }
    // }}}
}

