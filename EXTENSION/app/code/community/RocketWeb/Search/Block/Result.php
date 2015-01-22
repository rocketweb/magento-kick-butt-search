<?php
class RocketWeb_Search_Block_Result extends Mage_CatalogSearch_Block_Result {
	public function setListOrders()
	{
		$category = Mage::getSingleton('catalog/layer')->getCurrentCategory();
		/* @var $category Mage_Catalog_Model_Category */
		$availableOrders = $category->getAvailableSortByOptions();
		unset($availableOrders['position']);
		$availableOrders = array_merge(array(
				'directresult_position' => $this->__('Relevance')
		), $availableOrders);
	
		$this->getListBlock()
			->setAvailableOrders($availableOrders)
			->setDefaultDirection('desc')
			->setSortBy('directresult_position');
	
		return $this;
	}
	
}