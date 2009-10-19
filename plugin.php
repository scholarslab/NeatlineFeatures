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
/*
function neatline_install()
{
	$writer = new Zend_Log_Writer_Stream(LOGS_DIR . DIRECTORY_SEPARATOR . "neatline.log");
	$logger = new Zend_Log($writer);

	set_option('neatline_version', NEATLINE_PLUGIN_VERSION);

	$geoserver_config_addy = NEATLINE_GEOSERVER . "/rest/namespaces" ;
	$client = new Zend_Http_Client($geoserver_config_addy);
	$client->setAuth(NEATLINE_GEOSERVER_ADMINUSER, NEATLINE_GEOSERVER_ADMINPW);

	if ( !preg_match( NEATLINE_GEOSERVER_NAMESPACE_URL, $client->request(Zend_Http_Client::GET)->getBody() ) ) {
		$namespace_json =
	"{'namespace' : { 'prefix': '" . NEATLINE_GEOSERVER_NAMESPACE_PREFIX . "', 'uri': '" . NEATLINE_GEOSERVER_NAMESPACE_URL . "'} }";
		$response = $client->setRawData($namespace_json, 'text/json')->request(Zend_Http_Client::POST);
		if ($response->isSuccessful()) {
		 $logger->log("Neatline GeoServer namespace " . NEATLINE_GEOSERVER_NAMESPACE_PREFIX
		 . "(" . NEATLINE_GEOSERVER_NAMESPACE_URL . ")" . " added to GeoServer config.", Zend_Log::INFO);
		}
		else {
		 $logger->log("Failed to add Neatline/GeoServer namespace: check  Neatline config.", Zend_Log::ERROR);
		}
	}
}
*/

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


