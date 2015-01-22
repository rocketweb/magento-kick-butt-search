<?php 
class RocketWeb_Search_Model_Fulltext extends Mage_CatalogSearch_Model_Fulltext {
	public function rebuildIndex($storeId = null, $productIds = null)
	{
		Mage::dispatchEvent('catalogsearch_index_process_start', array(
		'store_id'      => $storeId,
		'product_ids'   => $productIds
		));
	
		$this->getResource()->rebuildIndex($storeId, $productIds);
		
		$enabledEntities = Mage::helper('rocketweb_search/entity')->getEnabledEntities();
		foreach($enabledEntities as $entity) {
			$entity->rebuildFulltextResourceIndex($this->getResource(),$storeId, $productIds);
		}
		
	
		Mage::dispatchEvent('catalogsearch_index_process_complete', array());
	
		return $this;
	}
	
}