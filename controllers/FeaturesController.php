<?php

/**
 * @package Neatline
 **/

class NeatlineFeatures_FeaturesController extends Omeka_Controller_Action
{


	/* returns the wkt for any particular Item's coverages */

	public function wktAction()
	{
		$id = (!$id) ? $this->getRequest()->getParam('id') : $id;
		$item = $this->findById($id,"Item");
		try {
			$coverages = $item->getElementTextsByElementNameAndSetName( 'Coverage', 'Dublin Core');
		}
		catch (Omeka_Record_Exception $e) {
		}
		$this->view->wkts = array();
		foreach($coverages as $coverage) {
			if ( $this->isWKT($coverage->text) ) {
				array_push($this->view->wkts, $coverage->text);
			}
		}
	}
	
	public function gmlAction()
	{
		if ($this->getRequest()->isGet()) {	
			$coverages = array();
			$id = (!$id) ? $this->getRequest()->getParam('id') : $id;
			$item = $this->findById($id,"Item");
			try {
				$coverages = $item->getElementTextsByElementNameAndSetName( 'Coverage', 'Dublin Core');
			}
			catch (Omeka_Record_Exception $e) {
			}
			$this->view->gmls = array();
			foreach($coverages as $coverage) {
				if ( $this->isGML($coverage->text) ) {
					array_push($this->view->gmls, $coverage->text);
				}
			}
		}
		else {
			// this must be a POST update
			// we accept the body of the POST as GML to be persisted into the dc:coverage
			$coverages = array();
			$id = (!$id) ? $this->getRequest()->getParam('id') : $id;
			$item = $this->findById($id,"Item");
			try {
				$coverages = $item->getElementTextsByElementNameAndSetName( 'Coverage', 'Dublin Core');
			}
			catch (Omeka_Record_Exception $e) {
			}
			$gmls_coverages = array();
			foreach($coverages as $coverage) {
				if ( $this->isGML($coverage->text) ) {
					array_push($gmls_coverages, $coverage);
				}
			}
			if (count($gml_coverages) > 0) {
				// there is a coverage into which to persist
				// use only the first for now
				$coverage = $gml_coverages[0];
				$coverage->setText($this->getRequest()->getBody);
				$item->save();
			}
			else {
				// this is an Item without pre-existing geodata
			}
		}
	}
	
	private function isGML($i) {
		return (stripos($i,"<gml") == 0) || (stripos($i,"<wfs") == 0) ;
	}
/*
	private function isWKT($i)
	{
		$j = strtoupper( $this->strstrb($i, '(') );
		switch($j) {
			case "POINT":
				return true;
				break;
			case "LINESTRING":
				return true;
				break;
			case "POLYGON":
				return true;
				break;
			case "MULTIPOINT":
				return true;
				break;
			case "MULTILINESTRING":
				return true;
				break;
			case "MULTIPOLYGON":
				return true;
				break;
			case "GEOMETRYCOLLECTION":
				return true;
				break;
			case "MULTIPOINT":
				return true;
				break;
		}
		return false;
	}
		
	private function strstrb($h,$n){
		return array_shift(explode($n,$h,2));
	}
*/

}
?>