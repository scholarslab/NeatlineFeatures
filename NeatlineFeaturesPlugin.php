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

require_once NEATLINE_FEATURES_PLUGIN_DIR .
    '/lib/NeatlineFeatures/Utils/View.php';
require_once NEATLINE_FEATURES_PLUGIN_DIR .
    '/lib/NeatlineFeatures_Functions.php';
require_once NEATLINE_FEATURES_PLUGIN_DIR .
    '/models/Table/Table_NeatlineFeature.php';

/**
 * This class manages the plugin itself. It defines controllers for all the
 * hooks and filters.
 **/
class NeatlineFeaturesPlugin extends Omeka_Plugin_AbstractPlugin
{
    // Vars {{{

    /**
     * This is a list of the hooks this manager defines.
     *
     * @var array
     **/
    protected $_hooks = array(
        'install',
        'uninstall',
        'upgrade',
        'admin_head',
        'public_head',
        'after_save_item',
        'before_delete_item',
        'initialize'
    );

    /**
     * This is a list of the filters this manager defines.
     *
     * @var array
     **/
    protected $_filters = array(
        'filterInputItemDublinCoreCoverage' =>
            array('ElementInput', 'Item', 'Dublin Core', 'Coverage'),
        'filterDisplayItemDublinCoreCoverage' =>
            array('Display', 'Item', 'Dublin Core', 'Coverage')
    );
    // }}}

    // Hooks {{{
    /**
     * This installs the NeatlineFeatures plugin.
     *
     * @return void
     * @author Eric Rochester <erochest@virginia.edu>
     **/
    public function hookInstall($args)
    {
        // NeatlineFeatures_Functions::flog('/tmp/nlfeatures.log', "(hook) install");
        $sql = "
            CREATE TABLE IF NOT EXISTS `{$this->_db->prefix}neatline_features` (
                id              INT(10)        UNSIGNED NOT NULL AUTO_INCREMENT,
                added           TIMESTAMP      DEFAULT CURRENT_TIMESTAMP,
                item_id         INT(10)        UNSIGNED NOT NULL,
                element_text_id INT(10)        UNSIGNED NOT NULL,
                is_map          TINYINT(1)     NOT NULL DEFAULT 0,
                geo             TEXT           ,
                zoom            SMALLINT(2)    NOT NULL DEFAULT 3,
                center_lon      DECIMAL(20, 7) NOT NULL DEFAULT 0.0,
                center_lat      DECIMAL(20, 7) NOT NULL DEFAULT 0.0,
                base_layer      VARCHAR(6)     DEFAULT NULL,
                CONSTRAINT PRIMARY KEY (id),
                INDEX (item_id, element_text_id)
            ) ENGINE=innodb DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;";
        $this->_db->query($sql);
    }

    /**
     * This uninstalls the NeatlineFeatures plugin.
     *
     * @return void
     * @author Eric Rochester <erochest@virginia.edu>
     **/
    public function hookUninstall()
    {
        // NeatlineFeatures_Functions::flog('/tmp/nlfeatures.log', "(hook) uninstall");
        $sql = "DROP TABLE IF EXISTS `{$this->_db->prefix}neatline_features`;";
        $this->_db->query($sql);
    }

    /**
     * This upgrades the database schema, if needed.
     *
     * @param string $oldVersion The previous version.
     * @param string $newVersion The current, new version.
     *
     * @return void
     * @author Eric Rochester
     **/
    public function hookUpgrade($oldVersion, $newVersion)
    {
        // NeatlineFeatures_Functions::flog('/tmp/nlfeatures.log', "(hook) upgrade");
        $table = $this->_db->getTable('NeatlineFeature');
        $name  = $table->getTableName();

        try {
            $this->_db->query("ALTER TABLE $name CHANGE COLUMN wkt geo TEXT;");
        } catch (Exception $e) {
        }
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
        $head = get_view()->headScript();
        $head->appendScript('', 'text/javascript', array('src' => $uri));
    }

    /**
     * This returns the name of the minified JS file for passing to queue_js_file.
     *
     * @return string
     * @author Eric Rochester <erochest@virginia.edu>
     **/
    private function _nlMinJs()
    {
        $version = get_plugin_ini('NeatlineFeatures', 'version');
        return "neatline-features-{$version}-min";
    }

    /**
     * This queues javascript and CSS for the admin header.
     *
     * @return void
     * @author Eric Rochester <erochest@virginia.edu>
     **/
    public function hookAdminHead($args)
    {
        $fc         = Zend_Registry::get('bootstrap')
            ->getResource('frontcontroller');
        $req        = $fc->getRequest();
        $module     = $req->getModuleName();
        $controller = $req->getControllerName();
        $action     = $req->getActionName();

        if ($controller == 'items'
            && ($action == 'add' || $action == 'edit' || $action == 'show')) {
            queue_css_file('nlfeatures');
            queue_css_file('nlfeature-editor');

            // We are also outputting the script tags to load OpenLayers here.
            $this->_queueJsUri(
                "//maps.google.com/maps/api/js?v=3.8&sensor=false"
            );
            queue_js_file('libraries/openlayers/OpenLayers.min');
            queue_js_file('libraries/tile.stamen');

            if (getenv('APPLICATION_ENV') == 'development') {
                queue_js_file('nlfeatures');
                queue_js_file('editor/edit_features');
                queue_js_file('featureswidget');
            } else {
                queue_js_file($this->_nlMinJs());
            }
        }
    }

    /**
     * This queues javascript and CSS for the public header.
     *
     * @return void
     * @author Eric Rochester <erochest@virginia.edu>
     **/
    public function hookPublicHead()
    {
        $fc         = Zend_Registry::get('bootstrap')
            ->getResource('frontcontroller');
        $req        = $fc->getRequest();
        $module     = $req->getModuleName();
        $controller = $req->getControllerName();
        $action     = $req->getActionName();

        if ($controller == 'items'
            && ($action == 'edit' || $action == 'show')) {
            queue_css_file('nlfeatures');

            // We are also outputting the script tags to load OpenLayers here.
            $this->_queueJsUri(
                "//maps.google.com/maps/api/js?v=3.8&sensor=false"
            );
            queue_js_file('libraries/openlayers/OpenLayers.min');
            queue_js_file('libraries/tile.stamen');

            if (getenv('APPLICATION_ENV') == 'development') {
                queue_js_file('nlfeatures');
                queue_js_file('featureswidget');
            } else {
                queue_js_file($this->_nlMinJs());
            }
        }
    }

    /**
     * This saves the is_map field, whenever the item is saved in a POST 
     * request.
     *
     * @param $record Omeka_Record The record that was just saved.
     *
     * @return void
     * @author Eric Rochester <erochest@virginia.edu>
     **/
    public function hookAfterSaveItem($args)
    {
        // NeatlineFeatures_Functions::flog('/tmp/nlfeatures.log', "(hook) after_save_item");
        $record = $args['record'];
        $utils  = new NeatlineFeatures_Utils_View();
        $utils->setCoverageElement();

        $post = $utils->getPost();
        if (!is_null($post)) {
            $this
                ->_db
                ->getTable('NeatlineFeature')
                ->updateFeatures($record, $utils->getPost());
        }
    }

    /**
     * This deletes the NL Features data for this item.
     *
     * @param $record Omeka_Record The record that is to be deleted.
     *
     * @return void
     * @author Eric Rochester <erochest@virginia.edu>
     **/
    public function hookBeforeDeleteItem($args)
    {
        // NeatlineFeatures_Functions::flog('/tmp/nlfeatures.log', "(hook) before_delete_item");
        $record = $args['record'];
        $this
            ->_db
            ->getTable('NeatlineFeature')
            ->removeItemFeatures($record);
    }

    /**
     * Initialization.
     *
     * Adds tranlation source.
     *
     * @return void
     */
    public function hookInitialize()
    {
        // NeatlineFeatures_Functions::flog('/tmp/nlfeatures.log', "(hook) initialize");
        add_translation_source(dirname(__FILE__) . '/languages');
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
    public function filterInputItemDublinCoreCoverage($components, $args)
    {
        // $args keys => input_name_stem, value, record, element, index, is_html
        $util = new NeatlineFeatures_Utils_View();
        $util->setEditOptions(
            $args['record'],
            $args['element'],
            $args['value'],
            $args['input_name_stem'],
            $args['index']
        );
        // Default $components['inputs']:
        // <div class="input-block"><div class="input"><textarea name="Elements[38][0][text]" id="Elements-38-0-text" rows="3" cols="50"></textarea></div><div class="controls"><input type="submit" name="" value="Remove" class="remove-element red button"></div><label class="use-html">Use HTML<input type="hidden" name="Elements[38][0][html]" value="0"><input type="checkbox" name="Elements[38][0][html]" id="Elements-38-0-html" value="1" class="use-html-checkbox"></label></div>
        $components['input'] = $util->getEditControl();

        return $components;
    }

    /**
     * This displays the coverage data as a map, if applicable.
     *
     * @param string  $text  The original text for the element.
     * @param array   $args  The record and element text triggering this.
     *
     * @return The HTML to generate the map.
     * @author Eric Rochester <erochest@virginia.edu>
     **/
    public function filterDisplayItemDublinCoreCoverage($text, $args)
    {
        $record      = $args['record'];
        $elementText = $args['element_text'];
        return NeatlineFeatures_Functions::displayCoverage(
            $text, $record, $elementText
        );
    }

    // }}}
}

