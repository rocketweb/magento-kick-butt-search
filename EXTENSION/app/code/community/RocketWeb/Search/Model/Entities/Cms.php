<?php 
class RocketWeb_Search_Model_Entities_Cms extends RocketWeb_Search_Model_Entities_Abstract {
	
	public function getEntityId() {
		return 'cms_page';	
	}
	
	public function getEnabledConfigXmlPath() {
		return 'rocketweb_search/cms_search/enable';
	}
	
	public function getTitleFieldNames() {
		return array('title','content_heading');
	}
	
	public function getContentFieldNames() {
		return array('content');
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
		if (!empty($data['catalogsearch_update_cms_page_id'])) {
			$pageId = $data['catalogsearch_update_cms_page_id'];
			$pageIds = array($pageId);
			$this->rebuildFulltextResourceIndex($this->_getIndexer()->getResource(),null, $pageIds);
			$this->_getIndexer()->resetSearchResults();
		}
		else if(!empty($data['catalogsearch_delete_cms_page_id'])) {
			$pageId = $data['catalogsearch_delete_cms_page_id'];
			$pageIds = array($pageId);
			$this->cleanFulltextResourceIndex($this->_getIndexer()->getResource(),null, $pageIds);
			$this->_getIndexer()->resetSearchResults();
		}
	}
	
	public function registerFulltextIndexerEvent(Mage_CatalogSearch_Model_Indexer_Fulltext $indexer,Mage_Index_Model_Event $event) {
		switch ($event->getType()) {
			case Mage_Index_Model_Event::TYPE_SAVE:
				$page = $event->getDataObject();
				$event->addNewData('catalogsearch_update_cms_page_id', $page->getId());
				break;
			case Mage_Index_Model_Event::TYPE_DELETE:
				$page = $event->getDataObject();
				$event->addNewData('catalogsearch_delete_cms_page_id', $page->getId());
				break;
		}
		return $indexer;
	}
	
	public function rebuildFulltextResourceIndex(Mage_CatalogSearch_Model_Mysql4_Fulltext $fulltextResource,$storeId = null, $pageIds = null) {
		$storeIds = array_keys(Mage::app()->getStores());
		foreach ($storeIds as $storeId) {
			$this->_rebuildCmsStoreIndex($fulltextResource,$storeId, $pageIds);
		}
		return $fulltextResource;
	}
	
	public function cleanFulltextResourceIndex(Mage_CatalogSearch_Model_Mysql4_Fulltext $fulltextResource,$storeId = null, $pageIds = null) {
		$this->getEngine()->cleanIndex($storeId, $pageIds,'cms_page');
		return $fulltextResource;
	}
	
	public function getSearcheableCollection($ids,$storeId) {
		if(is_null($ids)) $ids = array();
		else if(!is_array($ids)) $ids = array($ids);
		 
		$cmsPages = Mage::getModel('cms/page')->getCollection();
		$cmsPages->addFieldToFilter('is_active',1);
		$cmsPages->addFieldToFilter('is_searcheable',1);
		if(count($ids)) {
			$cmsPages->addFieldToFilter('page_id',array('in'=>$ids));
		}
		if($storeId) {
			$cmsPages->addStoreFilter($storeId);
		}
		 
		return $cmsPages;
	}
	
	protected function _rebuildCmsStoreIndex(Mage_CatalogSearch_Model_Mysql4_Fulltext $fulltextResource,$storeId, $pageIds = null) {
		$this->cleanFulltextResourceIndex($fulltextResource,$storeId, $pageIds);
		$cmsPages = $this->getSearcheableCollection($pageIds,$storeId);
		$cmsPages->load();
		
		$entityIndexes = array();
		foreach($cmsPages as $cmsPage) {
			$entityIndex = array();
			$entityIndex['entity_type'] = 'cms_page';
			$entityIndex['store_id'] = $storeId;
			$entityIndex['boost_index'] = 1;
			
			$titleFieldData = '';
			foreach($this->getTitleFieldNames() as $name) {
				$titleFieldData.=$this->filterData($cmsPage->getData($name)).'|';	
			}
			
			$contentFieldData = '';
			foreach($this->getContentFieldNames() as $name) {
				$contentFieldData.=$this->filterData($cmsPage->getData($name)).'|';
			}
			
			$entityIndex['name_index'] = $titleFieldData;
			$entityIndex['data_index'] = $entityIndex['name_index'].$contentFieldData;
			$entityIndex['sku_index'] = '';
			
			$entityIndexes[$cmsPage->getPageId()] = $entityIndex;
		}
		
		$fulltextResource->saveProductIndexes($storeId, $entityIndexes);
		$fulltextResource->resetSearchResults();
		return $this;
	}
	
	
}