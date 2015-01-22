<?php 
class RocketWeb_Search_Model_Entities_Category extends RocketWeb_Search_Model_Entities_Abstract {
	public function getEntityId() {
		return 'category';
	}
	
	public function getEnabledConfigXmlPath() {
		return 'rocketweb_search/category_search/enable';
	}
	
	public function getTitleFieldNames() {
		return array('name');
	}
	
	public function getContentFieldNames() {
		return array('description');
	}
	
	public function isEnabled() {
		return Mage::getStoreConfig($this->getEnabledConfigXmlPath());
	}
	
	public function getMatchedEntities() {
		return array(
				Mage_Index_Model_Event::TYPE_SAVE,
				Mage_Index_Model_Event::TYPE_DELETE
		);
	}
	
	public function processFulltextIndexerEvent($data) {
		if (!empty($data['catalogsearch_update_category_id'])) {
			$catId = $data['catalogsearch_update_category_id'];
			$catIds = array($catId);
			$this->rebuildFulltextResourceIndex($this->_getIndexer()->getResource(),null, $catIds);
			$this->_getIndexer()->resetSearchResults();
		}
		else if(!empty($data['catalogsearch_delete_category_id'])) {
			$catId = $data['catalogsearch_delete_category_id'];
			$catIds = array($catId);
			$this->cleanFulltextResourceIndex($this->_getIndexer()->getResource(),null, $catIds);
			$this->_getIndexer()->resetSearchResults();
		}
	}
	
	public function registerFulltextIndexerEvent(Mage_CatalogSearch_Model_Indexer_Fulltext $indexer,Mage_Index_Model_Event $event) {
		switch ($event->getType()) {
			case Mage_Index_Model_Event::TYPE_SAVE:
				$category = $event->getDataObject();
				$event->addNewData('catalogsearch_update_category_id', $category->getId());
				break;
			case Mage_Index_Model_Event::TYPE_DELETE:
				$category = $event->getDataObject();
				$event->addNewData('catalogsearch_delete_category_id', $category->getId());
				break;
		}
		return $indexer;
	}
	
	public function rebuildFulltextResourceIndex(Mage_CatalogSearch_Model_Mysql4_Fulltext $fulltextResource,$storeId = null, $pageIds = null) {
		$storeIds = array_keys(Mage::app()->getStores());
		foreach ($storeIds as $storeId) {
			$this->_rebuildCategoryStoreIndex($fulltextResource,$storeId, $pageIds);
		}
		return $fulltextResource;
	}
	
	public function cleanFulltextResourceIndex(Mage_CatalogSearch_Model_Mysql4_Fulltext $fulltextResource,$storeId = null, $pageIds = null) {
		$this->getEngine()->cleanIndex($storeId, $pageIds,'category');
		return $fulltextResource;
	}
	
	public function getSearcheableCollection($ids,$storeId) {
		if(is_null($ids)) $ids = array();
		else if(!is_array($ids)) $ids = array($ids);
		
		$categories = Mage::getModel('catalog/category')->getCollection();
		$categories->addAttributeToFilter('is_active',1);
		$categories->addAttributeToFilter('is_searcheable',1);
		
		foreach($this->getTitleFieldNames() as $field) {
			$categories->addAttributeToSelect($field);
		}
		foreach($this->getContentFieldNames() as $field) {
			$categories->addAttributeToSelect($field);
		} 
		
		if($storeId) {
			$categories->setStoreId($storeId);
		}
			
		if(count($ids)) {
			$categories->addAttributeToFilter('entity_id',array('in'=>$ids));
		}
			
		return $categories;
	}
	
	protected function _rebuildCategoryStoreIndex(Mage_CatalogSearch_Model_Mysql4_Fulltext $fulltextResource,$storeId, $pageIds = null) {
		$this->cleanFulltextResourceIndex($fulltextResource,$storeId, $pageIds);
		
		$categories = $this->getSearcheableCollection($pageIds,$storeId);
		$categories->load();
		
		$entityIndexes = array();
		foreach($categories as $category) {
			$entityIndex = array();
			$entityIndex['entity_type'] = 'category';
			$entityIndex['store_id'] = $storeId;
			$entityIndex['boost_index'] = 1;
			
			$titleFieldData = '';
			foreach($this->getTitleFieldNames() as $name) {
				$titleFieldData.=$this->filterData($category->getData($name)).'|';
			}
				
			$contentFieldData = '';
			foreach($this->getContentFieldNames() as $name) {
				$contentFieldData.=$this->filterData($category->getData($name)).'|';
			}
				
			$entityIndex['name_index'] = $titleFieldData;
			$entityIndex['data_index'] = $entityIndex['name_index'].$contentFieldData;
			$entityIndex['sku_index'] = '';
				
			$entityIndexes[$category->getId()] = $entityIndex;
		}
		
		$fulltextResource->saveProductIndexes($storeId, $entityIndexes);
		$fulltextResource->resetSearchResults();
		return $this;
	}
}