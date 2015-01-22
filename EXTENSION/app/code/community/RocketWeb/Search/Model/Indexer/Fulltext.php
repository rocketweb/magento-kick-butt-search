<?php 
class RocketWeb_Search_Model_Indexer_Fulltext extends Mage_CatalogSearch_Model_Indexer_Fulltext {
	protected function getMatchedEntities() {
		$_matchedEntities = $this->_matchedEntities;
		$enabledEntities = Mage::helper('rocketweb_search/entity')->getEnabledEntities();
		foreach($enabledEntities as $entity) {
			$_matchedEntities[$entity->getEntityId()] = $entity->getMatchedEntities();
		}
				
		return $_matchedEntities;
	}
	
	/**
	 * Check if indexer matched specific entity and action type
	 *
	 * @param   string $entity
	 * @param   string $type
	 * @return  bool
	 */
	public function matchEntityAndType($entity, $type)
	{
		$_matchedEntities = $this->getMatchedEntities();
		if (isset($_matchedEntities[$entity])) {
			if (in_array($type, $_matchedEntities[$entity])) {
				return true;
			}
		}
		return false;
	}
	
	/**
	 * Register data required by process in event object
	 *
	 * @param Mage_Index_Model_Event $event
	 */
	protected function _registerEvent(Mage_Index_Model_Event $event)
	{
		parent::_registerEvent($event);
		
		$enabledEntities = Mage::helper('rocketweb_search/entity')->getEnabledEntities();
		foreach($enabledEntities as $entity) {
			$entity->registerFulltextIndexerEvent($this,$event);
		}
	}
	
	
	
	protected function _processEvent(Mage_Index_Model_Event $event)
	{
		parent::_processEvent($event);
		
		$data = $event->getNewData();
		
		$enabledEntities = Mage::helper('rocketweb_search/entity')->getEnabledEntities();
		foreach($enabledEntities as $entity) {
			$entity->processFulltextIndexerEvent($data);
		}

	}
	
	protected function _getIndexer()
	{
		return Mage::getSingleton('rocketweb_search/fulltext');
	}
	
}