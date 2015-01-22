<?php 
class RocketWeb_Search_Model_Entities_Blog extends RocketWeb_Search_Model_Entities_Abstract {
	
	public function getEntityId() {
		return 'blog';
	}
	
	public function getEnabledConfigXmlPath() {
		return 'rocketweb_search/aw_blog_search/enable';
	}
	
	public function getTitleFieldNames() {
		return array('title','tags');
	}
	
	public function getContentFieldNames() {
		return array('post_content');
	}
	
	public function isEnabled() {
		if(!isset($this->_is_enabled)) {
			if(Mage::helper('rocketweb_search')->isAwBlogModuleEnabled()) {
				$this->_is_enabled = Mage::getStoreConfig($this->getEnabledConfigXmlPath());
			}
			else {
				$this->_is_enabled = false;
			}
		}
		return $this->_is_enabled;
		
	}
	
	public function getMatchedEntities() {
		return array(
			Mage_Index_Model_Event::TYPE_SAVE,
			Mage_Index_Model_Event::TYPE_DELETE
		);
	}
	
	public function processFulltextIndexerEvent($data) {
		if (!empty($data['catalogsearch_update_blog_id'])) {
			$blogId = $data['catalogsearch_update_blog_id'];
			$blogIds = array($blogId);
			$this->rebuildFulltextResourceIndex($this->_getIndexer()->getResource(),null, $blogIds);
			$this->_getIndexer()->resetSearchResults();
		}
		else if(!empty($data['catalogsearch_delete_blog_id'])) {
			$blogId = $data['catalogsearch_delete_blog_id'];
			$blogIds = array($blogId);
			$this->cleanFulltextResourceIndex($this->_getIndexer()->getResource(),null, $blogIds);
			$this->_getIndexer()->resetSearchResults();
		}
	}
	public function registerFulltextIndexerEvent(Mage_CatalogSearch_Model_Indexer_Fulltext $indexer,Mage_Index_Model_Event $event) {
		switch ($event->getType()) {
			case Mage_Index_Model_Event::TYPE_SAVE:
				$blog = $event->getDataObject();
				$event->addNewData('catalogsearch_update_blog_id', $blog->getId());
				break;
			case Mage_Index_Model_Event::TYPE_DELETE:
				$blog = $event->getDataObject();
				$event->addNewData('catalogsearch_delete_blog_id', $blog->getId());
				break;
		}
		return $indexer;
	}
	
	public function rebuildFulltextResourceIndex(Mage_CatalogSearch_Model_Mysql4_Fulltext $fulltextResource,$storeId = null, $pageIds = null) {
		$storeIds = array_keys(Mage::app()->getStores());
		foreach ($storeIds as $storeId) {
			$this->_rebuildBlogStoreIndex($fulltextResource,$storeId, $pageIds);
		}
		return $fulltextResource;
	}
	
	public function cleanFulltextResourceIndex(Mage_CatalogSearch_Model_Mysql4_Fulltext $fulltextResource,$storeId = null, $pageIds = null) {
		$this->getEngine()->cleanIndex($storeId, $pageIds,'blog');
		return $fulltextResource;
	}
	
	protected function _rebuildBlogStoreIndex(Mage_CatalogSearch_Model_Mysql4_Fulltext $fulltextResource,$storeId, $pageIds = null)
	{
		$this->cleanFulltextResourceIndex($fulltextResource,$storeId, $pageIds);
		$blogPages = $this->getSearcheableCollection($pageIds,$storeId);
		$blogPages->load();
	
		$entityIndexes = array();
		foreach($blogPages as $page) {
			
			$titleFieldData = '';
			foreach($this->getTitleFieldNames() as $name) {
				$titleFieldData.=$this->filterData($page->getData($name)).'|';
			}
				
			$contentFieldData = '';
			foreach($this->getContentFieldNames() as $name) {
				$contentFieldData.=$this->filterData($page->getData($name)).'|';
			}
	
			$entityIndex = array();
			$entityIndex['entity_type'] = 'blog';
			$entityIndex['store_id'] = $storeId;
			$entityIndex['boost_index'] = 1;
			$entityIndex['name_index'] = $titleFieldData;
			$entityIndex['data_index'] = $entityIndex['name_index'].$contentFieldData;
			$entityIndex['sku_index'] = '';
	
			$entityIndexes[$page->getPostId()] = $entityIndex;
	
		}
		$fulltextResource->saveProductIndexes($storeId, $entityIndexes);
		$fulltextResource->resetSearchResults();
		return $this;
	}
	
	public function getSearcheableCollection($ids,$storeId) {
		if(is_null($ids)) $ids = array();
		else if(!is_array($ids)) $ids = array($ids);
	
		$blogPages = Mage::getModel('blog/blog')->getCollection();
		$blogPages->addFieldToFilter('status',1);
		$blogPages->addFieldToFilter('is_searcheable',1);
		if(count($ids)) {
			$blogPages->addFieldToFilter('main_table.post_id',array('in'=>$ids));
		}
		if($storeId) {
			$blogPages->addStoreFilter($storeId);
		}
		return $blogPages;
	}
}