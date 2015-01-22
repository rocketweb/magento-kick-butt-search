<?php 
abstract class RocketWeb_Search_Model_Entities_Abstract {
	
	public abstract function getEntityId();
	
	public abstract function isEnabled();
	
	public abstract function getEnabledConfigXmlPath();
	
	public abstract function getTitleFieldNames();
	
	public abstract function getContentFieldNames();

	
	//RocketWeb_Search_Model_Indexer_Fulltext::_processEvent
	public abstract function processFulltextIndexerEvent($data);
	
	//RocketWeb_Search_Model_Indexer_Fulltext::getMatchedEntities
	public abstract function getMatchedEntities();
	
	
	//RocketWeb_Search_Model_Indexer_Fulltext::_registerCmsPageEvent
	public abstract function registerFulltextIndexerEvent(Mage_CatalogSearch_Model_Indexer_Fulltext $indexer,Mage_Index_Model_Event $event);
	
	//Mage_CatalogSearch_Model_Mysql4_Fulltext::rebuildCmsIndex
	public abstract function rebuildFulltextResourceIndex(Mage_CatalogSearch_Model_Mysql4_Fulltext $fulltextResource,$storeId = null, $pageIds = null);
	
	//Mage_CatalogSearch_Model_Mysql4_Fulltext::cleanCmsIndex
	public abstract function cleanFulltextResourceIndex(Mage_CatalogSearch_Model_Mysql4_Fulltext $fulltextResource,$storeId = null, $pageIds = null);
	
	//Mage_CatalogSearch_Model_Mysql4_Fulltext::_getSearcheableCmsPageCollection
	public abstract function getSearcheableCollection($ids,$storeId);
	
	
	protected function _getIndexer()
	{
		return Mage::getSingleton('rocketweb_search/fulltext');
	}
	
	protected function getEngine() {
		return Mage::helper('catalogsearch')->getEngine();
	}
	
	protected function filterData($data) {
		$ret = preg_replace("#\s+#siu", ' ', trim(strip_tags($data)));
		$ret = preg_replace('#\{\{.*\}\}#iu','',$ret);
		return $ret;
	}
}