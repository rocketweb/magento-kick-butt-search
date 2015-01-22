<?php 
class RocketWeb_Search_Block_Entity_Cms_Result extends RocketWeb_Search_Block_Entity_Abstract {
	public function getResultCollection() {
		if(!isset($this->_collection)) {
			$collection = Mage::getModel('cms/page')->getCollection();
			
			$collection->getSelect()->joinInner(
					array('search_result' => $this->getTable('rocketweb_search/result')),
					$this->getConnection()->quoteInto(
							'search_result.product_id=main_table.page_id AND search_result.query_id=? AND entity_type=\'cms_page\'',
							Mage::helper('catalogsearch')->getQuery()->getId()
					),
					array('relevance' => 'relevance')
			);
			
			$collection->addFieldToFilter('is_active',1);
			$collection->addFieldToFilter('is_searcheable',1);
			$collection->addStoreFilter(Mage::app()->getStore());
			$collection->getSelect()->order('relevance DESC');
			$curPage = (int) Mage::app()->getRequest()->getParam('cp');
			if(!$curPage) $curPage =1;
			$collection->setCurPage($curPage)->setPageSize(Mage::getStoreConfig('rocketweb_search/cms_search/num_per_page'));
			
			$this->_collection = $collection;
			
			return $this->_collection;
		}
		else {
			return $this->_collection;
		}
	}
	
	public function getRendererBlock($cms_page) {
		$block = Mage::app()->getLayout()->createBlock($this->_renderer_block_type,'cms_page_result_'.$cms_page->getPageId());
		$block->setTemplate($this->_renderer_template);
		$block->setCmsPage($cms_page);
		return $block;
	}
	
	public function getTitle() {
		$title = Mage::getStoreConfig('rocketweb_search/cms_search/results_title');
		$title = str_replace('{{query}}',Mage::helper('catalogsearch')->getQuery()->getQueryText(),$title);
		$title = $this->htmlEscape($title);
		return $title;
	}
	
	
	protected function _prepareLayout()
	{
		parent::_prepareLayout();
		$pager = $this->getLayout()->createBlock('page/html_pager', 'cms.searchresults.pager');
		
		$pager->setAvailableLimit(array(Mage::getStoreConfig('rocketweb_search/cms_search/num_per_page')));
		$pager->setShowPerPage(Mage::getStoreConfig('rocketweb_search/cms_search/num_per_page'));
		$pager->setLimit(Mage::getStoreConfig('rocketweb_search/cms_search/num_per_page'));
		$pager->setPageVarName('cp');
		$pager->setLimitVarName('cl');
		$pager->setCollection($this->getResultCollection());
		
		
		$this->setChild('pager', $pager);
		//$this->getCmsPagesCollection()->load();
		return $this;
	}
	
}