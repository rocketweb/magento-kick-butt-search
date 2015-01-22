<?php 
class RocketWeb_Search_Block_Entity_Category_Directresult extends RocketWeb_Search_Block_Entity_Category_Result {
	public function getResultCollection() {
		if(!isset($this->_collection)) {
			$collection = Mage::getModel('catalog/category')->getCollection();
			$collection->setStoreId(Mage::app()->getStore()->getId());
			$collection->addAttributeToFilter('is_active',1);
			$collection->addAttributeToSelect('name');
			$collection->addAttributeToSelect('description');
			
			$categories = Mage::registry('direct_search_result_categories');
			usort($categories, array($this, '_sortByOrder'));
			$categoryIds=array();
			foreach($categories as $categoryId) {
				$categoryIds[]=$categoryId['entity_id'];
			}
			$collection->addAttributeToFilter('entity_id',array('in'=>$categoryIds));
			if(count($categoryIds)) {
				$collection->getSelect()->order(new Zend_Db_Expr("FIELD(e.entity_id,".implode(',',$categoryIds).")"));
			}
			
			$curPage = (int) Mage::app()->getRequest()->getParam('cap');
			if(!$curPage) $curPage =1;
			$collection->setCurPage($curPage)->setPageSize(Mage::getStoreConfig('rocketweb_search/category_search/num_per_page'));
			
			$this->_collection = $collection;
			
			return $this->_collection;
		}
		else {
			return $this->_collection;
		}
	}
	
	protected function _sortByOrder($a, $b) {
		if($a['order'] == $b['order']) return 0;
		if($a['order'] < $b['order']) return -1;
		else return 1;
	}
}