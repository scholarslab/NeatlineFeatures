<?php
class Neatline_Map_Item_Type_ID_Helper extends Zend_View_Helper_Abstract {

	function getNeatlineMapsItemTypeID() {
		return $this->findByName("Historical Map", "Itemtype")->id;
	}

}


?>