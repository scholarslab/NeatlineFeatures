<?php

/**
 * @version $Id$
 * @copyright
 * @package neatline
 **/

#require_once 'Curl.php';

define('NEATLINEFEATURES_PLUGIN_VERSION', get_plugin_ini('neatlinefeatures', 'version'));
define('NEATLINEFEATURES_PLUGIN_DIR', dirname(__FILE__));

/*
 define('NEATLINE_GEOSERVER', 'http://localhost:8080/geoserver');
 define('NEATLINE_GEOSERVER_NAMESPACE_PREFIX', 'neatline');
 define('NEATLINE_GEOSERVER_NAMESPACE_URL', 'http://www.neatline.org');
 define('NEATLINE_GEOSERVER_ADMINUSER', 'admin');
 define('NEATLINE_GEOSERVER_ADMINPW', 'geoserver');


 define('NEATLINE_SPATIAL_REFERENCE_SERVICE','http://spatialreference.org/ref');
 */

add_plugin_hook('install', 'neatlinefeatures_install');
add_plugin_hook('uninstall', 'neatlinefeatures_uninstall');

add_plugin_hook('define_routes', 'neatlinefeatures_routes');


function neatlinefeatures_uninstall()
{
	delete_option('neatlinefeatures_plugin_version');
}

function neatlinefeatures_install()
{
	$writer = new Zend_Log_Writer_Stream(LOGS_DIR . DIRECTORY_SEPARATOR . "neatline.log");
	$logger = new Zend_Log($writer);

	set_option('neatlinefeatures_version', NEATLINEFEATURES_PLUGIN_VERSION);

	# now we add 'Historic Map' item type
	$featitemtype = array(
     'name'       => "Geographic feature", 
      'description' => "Geographic feature "
      );

      $featitemtypemetadata = array(
      array(
              'name'        => "Shape", 
              'description' => "Shape in WKT form"             
              ));
              insert_item_type($featitemtype,$featitemtypemetadata);

}


/**
 * Add the routes from routes.ini in this plugin folder.
 *
 * @return void
 **/
function neatlinefeatures_routes($router)
{
	$router->addConfig(new Zend_Config_Ini(NEATLINEFEATURES_PLUGIN_DIR .
	DIRECTORY_SEPARATOR . 'routes.ini', 'routes'));
}


