<?php
class RocketWeb_Search_Block_Product_List extends Mage_Catalog_Block_Product_List {
	protected function _getProductCollection()
    {
    	if (is_null($this->_productCollection)) {
			$this->_productCollection = $this->getLayer()->getProductCollection();
		}
		return $this->_productCollection;
	}
	
}