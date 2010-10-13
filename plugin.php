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
add_filter(array('Form','Item','Dublin Core','Coverage'),"neatlinefeatures_map_widget");

function neatlinefeatures_uninstall()
{
	delete_option('neatlinefeatures_plugin_version');
}

function neatlinefeatures_install()
{
	set_option('neatlinefeatures_version', NEATLINEFEATURES_PLUGIN_VERSION);

}

// Add the routes from routes.ini in this plugin folder.
function neatlinefeatures_routes($router)
{
	$router->addConfig(new Zend_Config_Ini(NEATLINEFEATURES_PLUGIN_DIR .
	DIRECTORY_SEPARATOR . 'routes.ini', 'routes'));
}

function neatlinefeatures_map_widget($html,$inputNameStem,$value,$options,$record,$element)
{
	debug("value = " .  $value);
	debug("html_entity_decode(value) = " .  html_entity_decode($value));
	$div = __v()->partial('widgets/edit.phtml', array("item" => __v()->item, 
				"textarea" => __v()->formTextarea($inputNameStem . "[text]",html_entity_decode($value),array('class'=>'textinput', 'rows'=>5, 'cols'=>50))	, "inputNameStem" =>$inputNameStem, "value" => html_entity_decode($value), "options" => $options, "record" => $record, "element" => $element));
	return $div;
}

function neatlinefeatures_getMapItemType() {
	$types = get_db()->getTable("ItemType")->findBy(array("name" => "Historical map"));

	/*	 we need to add the following workaround because Omeka's ItemType table lacks filtering right now
	 the findBy above -should- take care of this for us, but it doesn't. we should be able to do this with a
	 filtering closure, but PHP is confusion */
	$tmp = array();
	foreach ($types as $itemtype) {
		if ($itemtype->name == 'Historical map') {
			array_push($tmp, $itemtype);
		}
	}
	$types = $tmp;

	$type = "NO NEATLINEMAPS INSTALLED";
	if (count($types) > 0) {
		$type = reset($types)->id; // a PHP idiom is that reset() returns the first element of an assoc array
	}
	return $type;
}

function neatlinefeatures_formatXML($output) {
	$sxml = new SimpleXMLElement($output);
	$dom = new DOMDocument('1.0');
	$dom->preserveWhiteSpace = false;
	$dom->formatOutput = true;
	$dom->loadXML($sxml->asXML());
	return $dom->saveXML();
}

