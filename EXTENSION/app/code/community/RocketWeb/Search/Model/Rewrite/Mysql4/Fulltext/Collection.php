<?php
class RocketWeb_Search_Model_Rewrite_Mysql4_Fulltext_Collection extends Mage_CatalogSearch_Model_Mysql4_Fulltext_Collection {
	public function setOrder($attribute, $dir = 'desc')
	{
		if($attribute == 'directresult_position') {
			$directsearchResultData = Mage::registry('direct_search_result_products');
			usort($directsearchResultData, array($this, '_sortByOrder'));
			$productIds = array();
			foreach($directsearchResultData as $item) {
				$productIds[]=$item['entity_id'];
			}
			
			if(count($productIds)) {
				$this->getSelect()->order(new Zend_Db_Expr("FIELD(e.entity_id,".implode(',',$productIds).")"));
			}
		}
		else {
			parent::setOrder($attribute, $dir);
		}
		return $this;
	}
	
	
	protected function _sortByOrder($a, $b) {
		if($a['order'] == $b['order']) return 0;
		if($a['order'] < $b['order']) return -1;
		else return 1;
	}
	
	
	public function addSearchFilter($query)
	{
		Mage::getSingleton('catalogsearch/fulltext')->prepareResult();

		$this->getSelect()->joinInner(
				array('search_result' => $this->getTable('rocketweb_search/result')),
				$this->getConnection()->quoteInto(
						'search_result.product_id=e.entity_id AND search_result.query_id=? AND entity_type=\'product\'',
						$this->_getQuery()->getId()
				),
				array('relevance' => 'relevance')
		);

		return $this;
	}
	
}