<?php

/**
 * @package Neatline
 **/

class NeatlineFeatures_FeaturesController extends Omeka_Controller_Action
{

	public function init()
	{
	}

	public function showAction()
	{
		$logger = Omeka_Context::getInstance()->getLogger();

		$id = (!$id) ? $this->getRequest()->getParam('id') : $id;
		$backgroundMap = (!$backgroundMap) ? $this->getRequest()->getParam('backgroundMap') : $backgroundMap;

		$item = $this->findById($id,"Item");

		$this->view->item = $item;
		$backgroundMaps = explode($backgroundMap,',');

		$backgroundLayers = array();
		foreach ( $backgroundMaps as $mapid )
		{
			$map = $this->findById($mapid,"Item");
			$layertitle = $map->getElementTextsByElementNameAndSetName( 'Title', 'Dublin Core');
			$serviceaddy = $map->getServiceAddy();
			$layername = $map->getLayerName();
			$backgroundLayers[$layertitle] = array("layername" => $layername, "serviceaddy" => $serviceaddy);
		}
	}

	public function editAction()
	{
	}

	private function getServiceAddy($item)
	{
		try {
			$serviceaddys = $item->getElementTextsByElementNameAndSetName( 'Service address', 'Item Type Metadata');
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

	private function getLayerName($item)
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

}