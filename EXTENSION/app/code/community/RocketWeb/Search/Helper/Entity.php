<?php 
class RocketWeb_Search_Helper_Entity extends Mage_Core_Helper_Data {
	public function getEnabledEntities() {
		if(!isset($this->_enabledEntities)) {
			$availableEntities = $this->getAvailableEntities();
			$enabledEntities = array();
			foreach($availableEntities as $entitiy) {
				if($entitiy->isEnabled()) {
					$enabledEntities[]=$entitiy;
				}
			}
			$this->_enabledEntities = $enabledEntities;
		}
		return $this->_enabledEntities;
	}
	
	public function getAvailableEntities() {
		$availableEntities = array();
		$nodes = Mage::getConfig()->getNode('searcheable_entities');
		foreach($nodes->children() as $node) {
			$entitiy = Mage::getModel($node->model);
			if(!is_object($entitiy)) {
				throw new Exception("searcheable_entities {$node->model} does not exist!");
			}
			else {
				$availableEntities[]=$entitiy;
			}
		}
		return $availableEntities;
	}
}