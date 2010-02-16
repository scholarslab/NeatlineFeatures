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
		$writer = new Zend_Log_Writer_Stream(LOGS_DIR . DIRECTORY_SEPARATOR . "neatline.log");
		$logger = new Zend_Log($writer);

		$id = (!$id) ? $this->getRequest()->getParam('id') : $id;
		$backgroundMap = (!$backgroundMap) ? $this->getRequest()->getParam('backgroundMap') : $backgroundMap;

		$item = $this->findById($id,"Item");
		$this->view->item = $item;

		$backgroundMaps = explode(',',$backgroundMap);
		$backgroundLayers = array();
		foreach ( $backgroundMaps as $mapid )
		{
			$map = $this->findById($mapid,"Item");
			$layertitle = "A map with no title";
			try {
				$layertitles = $map->getElementTextsByElementNameAndSetName( 'Title', 'Dublin Core');
				$layertitle = $layertitles[0]->text;
			}
			catch (Omeka_Record_Exception $e) {
				$logger->err($e);
			}	
			$serviceaddy = $this->getServiceAddy($map);
			$layername = $this->getLayerName($map);
			$logger->info("title, addy, name: " . $layertitle . ", " . $serviceaddy . ", " . $layername);

			$backgroundLayers["$layertitle"] = array("layername" => $layername, "serviceaddy" => $serviceaddy);
		}
		$this->view->backgroundLayers = $backgroundLayers;
	}

	public function editAction()
	{		
		$writer = new Zend_Log_Writer_Stream(LOGS_DIR . DIRECTORY_SEPARATOR . "neatline.log");
		$logger = new Zend_Log($writer);

		$id = (!$id) ? $this->getRequest()->getParam('id') : $id;
		$backgroundMap = (!$backgroundMap) ? $this->getRequest()->getParam('backgroundMap') : $backgroundMap;

		$item = $this->findById($id,"Item");
		$this->view->item = $item;

		$backgroundMaps = explode(',',$backgroundMap);
		$backgroundLayers = array();
		foreach ( $backgroundMaps as $mapid )
		{
			$map = $this->findById($mapid,"Item");
			$layertitle = "A map with no title";
			try {
				$layertitles = $map->getElementTextsByElementNameAndSetName( 'Title', 'Dublin Core');
				$layertitle = $layertitles[0]->text;
			}
			catch (Omeka_Record_Exception $e) {
				$logger->err($e);
			}	
			$serviceaddy = $this->getServiceAddy($map);
			$layername = $this->getLayerName($map);
			$logger->info("title, addy, name: " . $layertitle . ", " . $serviceaddy . ", " . $layername);

			$backgroundLayers["$layertitle"] = array("layername" => $layername, "serviceaddy" => $serviceaddy);
		}
		$this->view->backgroundLayers = $backgroundLayers;

	}
	
	public function saveAction(){
		$writer = new Zend_Log_Writer_Stream(LOGS_DIR . DIRECTORY_SEPARATOR . "neatline.log");
		$logger = new Zend_Log($writer);

		$id = (!$id) ? $this->getRequest()->getParam('id') : $id;
		$shapes = (!$shapes) ? $this->getRequest()->getParam('shapes') : $shapes;
		$item = $this->findById($id,"Item");
		
		$logger->info("Here's what we got in wkts: " . $shapes);
		
		$this->_forward("edit");
	}

	private function getServiceAddy($item)
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