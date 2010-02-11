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
		$item = $this->findById($id,"Item");


	}
	


}