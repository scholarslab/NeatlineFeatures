<?php

/**
 * @version $Id$
 * @copyright
 * @package neatline
 **/

define('NEATLINEFEATURES_PLUGIN_VERSION', get_plugin_ini('NeatlineFeatures', 'version'));
define('NEATLINEFEATURES_PLUGIN_DIR', dirname(__FILE__));
define('NEATLINEFEATURES_LIB_DIR', NEATLINEFEATURES_PLUGIN_DIR . DIRECTORY_SEPARATOR . 'libraries' . DIRECTORY_SEPARATOR);

add_plugin_hook('install', 'neatlinefeatures_install');
add_plugin_hook('uninstall', 'neatlinefeatures_uninstall');
add_plugin_hook('define_routes', 'neatlinefeatures_routes');
add_plugin_hook('define_acl', 'neatlinefeatures_define_acl');
add_filter(array('Form','Item','Dublin Core','Coverage'),"neatlinefeatures_map_widget");

//add_filter('admin_items_form_tabs', 'neatlinefeatures_location_tab');


function neatlinefeatures_uninstall()
{
	delete_option('neatlinefeatures_plugin_version');
}

function neatlinefeatures_install()
{
	$writer = new Zend_Log_Writer_Stream(LOGS_DIR . DIRECTORY_SEPARATOR . "neatline.log");
	$logger = new Zend_Log($writer);
	set_option('neatlinefeatures_version', NEATLINEFEATURES_PLUGIN_VERSION);

}

// Add the routes from routes.ini in this plugin folder.
function neatlinefeatures_routes($router)
{
	$router->addConfig(new Zend_Config_Ini(NEATLINEFEATURES_PLUGIN_DIR .
	DIRECTORY_SEPARATOR . 'routes.ini', 'routes'));
}

function neatlinefeatures_define_acl($acl)
{
	// only allow super users and admins to edit shapes
	$acl->loadResourceList(array('NeatlineFeatures_Features' => array('edit')));
}

function neatlinefeatures_map_widget($html,$inputNameStem,$value,$options,$record,$element)
{
	$writer = new Zend_Log_Writer_Stream(LOGS_DIR . DIRECTORY_SEPARATOR . "neatline.log");
	$logger = new Zend_Log($writer);
	$types = get_db()->getTable("ItemType")->findBy(array("name" => "Historical map"));
	
/*	 we need to add the following workaround because Omeka's ItemType table lacks filtering right now
	 the findBy above -should- take care of this for us, but it doesn't */
	$pull_by_name = create_function('$itemtype', "return ($itemtype->name == 'Historical map')");
	$types = array_filter($types,"pull_by_name");

	$type = "NO NEATLINEMAPS INSTALLED";
	if (count($types) > 0) {
		$type = reset($types)->id; // a PHP idiom is that reset() returns the first element of an assoc array
	}
	$div = __v()->partial('features/edit.phtml', array("logger" => $logger, "item" => __v()->item, "NEATLINEMAPS_ITEMTYPE" => $type,
				"textarea" => __v()->formTextarea($inputNameStem . "[text]",$value,array('class'=>'textinput', 'rows'=>5, 'cols'=>50))	, "inputNameStem" =>$inputNameStem, "value" => $value, "options" => $options, "record" => $record, "element" => $element));
	return $div;
}

/*
function neatlinefeatures_location_tab($tabs) {
	$tabs["locate"] = implode('',file(NEATLINEFEATURES_LIB_DIR . "edit.php"));
	return $tabs;
} */

function getServiceAddy($item)
{
	try {
		$serviceaddys = $item->getElementTextsByElementNameAndSetName( 'Service Address', 'Item Type Metadata');
	}
	catch (Omeka_Record_Exception $e) {
	}

	if ($serviceaddys) {
		$serviceaddy = $serviceaddys[0]->text;
	}
	if ($serviceaddy) {
		return $serviceaddy;
	}
	else {
		return NEATLINE_GEOSERVER . "/wms";
	}
}

function getLayerName($item)
{
	try {
		$serviceaddys = $item->getElementTextsByElementNameAndSetName( 'Layername', 'Item Type Metadata');
	}
	catch (Omeka_Record_Exception $e) {
	}

	if ($serviceaddys) {
		$serviceaddy = $serviceaddys[0]->text;
	}
	if ($serviceaddy) {
		return $serviceaddy;
	}
	else {
		return NEATLINE_GEOSERVER_NAMESPACE_PREFIX . ":" . $item->id;
	}
	

}
