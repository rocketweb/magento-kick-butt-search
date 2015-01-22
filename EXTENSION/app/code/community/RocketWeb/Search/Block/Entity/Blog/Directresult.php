<?php 
class RocketWeb_Search_Block_Entity_Blog_Directresult extends RocketWeb_Search_Block_Entity_Blog_Result {
	public function getResultCollection() {
		if(!isset($this->_collection)) {
			$collection = Mage::getModel('blog/blog')->getCollection();
			$blogs = Mage::registry('direct_search_result_blogs');
			usort($blogs, array($this, '_sortByOrder'));
			$blogIds=array();
			foreach($blogs as $blogItem) {
				$blogIds[]=$blogItem['entity_id'];
			}
			
			$collection->addFieldToFilter('main_table.post_id',array('in'=>$blogIds));
			$collection->addFieldToFilter('status',1);
			$collection->addStoreFilter(Mage::app()->getStore());
			//$collection->getSelect()->order('relevance DESC');
			$curPage = (int) Mage::app()->getRequest()->getParam('bp');
			if(!$curPage) $curPage =1;
			$collection->setCurPage($curPage)->setPageSize(Mage::getStoreConfig('rocketweb_search/aw_blog_search/num_per_page'));
			
			if(count($blogIds)) {
				$collection->getSelect()->order(new Zend_Db_Expr("FIELD(main_table.post_id,".implode(',',$blogIds).")"));
			}
			
			$this->_collection = $collection;
				
			return $this->_collection;
		}
	}
	
	protected function _sortByOrder($a, $b) {
		if($a['order'] == $b['order']) return 0;
		if($a['order'] < $b['order']) return -1;
		else return 1;
	}
}