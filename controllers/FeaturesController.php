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
		
		//$r = new ReflectionObject($item);
		//$mets = var_export($r->getMethods());
		$this->view->item = $item;
		$this->view->backgroundMap = $backgroundMap;
	}
	
	public function editAction()
	{
		
	}
	

	


}