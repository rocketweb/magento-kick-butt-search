<?php
class RocketWeb_Search_Model_Directsearchlayer extends Mage_CatalogSearch_Model_Layer {
	public function prepareProductCollection($collection)
	{
		$directsearchResultData = Mage::registry('direct_search_result_products');
		if($directsearchResultData) {
			$productIds = array();
			foreach($directsearchResultData as $item) {
				$productIds[]=$item['entity_id'];
			}
			$collection->addFieldToFilter('entity_id',array('in'=>$productIds));
		}
		
		$collection
			->addAttributeToSelect(Mage::getSingleton('catalog/config')->getProductAttributes())
			->setStore(Mage::app()->getStore())
			->addMinimalPrice()
			->addFinalPrice()
			->addTaxPercents()
			->addStoreFilter()
			->addUrlRewrite();

        $collection->addFieldToFilter('visibility', array('in' => array(Mage_Catalog_Model_Product_Visibility::VISIBILITY_BOTH, Mage_Catalog_Model_Product_Visibility::VISIBILITY_IN_SEARCH)));
	
		return $this;
	}
}