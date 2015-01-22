<?php 
class RocketWeb_Search_Block_Entity_Cms_Directresult extends RocketWeb_Search_Block_Entity_Cms_Result {
	public function getResultCollection() {
		if(!isset($this->_collection)) {
			$collection = Mage::getModel('cms/page')->getCollection();
			$cms = Mage::registry('direct_search_result_cms');
			usort($cms, array($this, '_sortByOrder'));
			$cmsIds=array();
			foreach($cms as $cmsId) {
				$cmsIds[]=$cmsId['entity_id'];
			}
			
			$collection->addFieldToFilter('main_table.page_id',array('in'=>$cmsIds));
			$collection->addFieldToFilter('is_active',1);
			$collection->addStoreFilter(Mage::app()->getStore());
			$curPage = (int) Mage::app()->getRequest()->getParam('cp');
			if(!$curPage) $curPage =1;
			$collection->setCurPage($curPage)->setPageSize(Mage::getStoreConfig('rocketweb_search/cms_search/num_per_page'));
			
			if(count($cmsIds)) {
				$collection->getSelect()->order(new Zend_Db_Expr("FIELD(main_table.page_id,".implode(',',$cmsIds).")"));
			}
			$collection->load();
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