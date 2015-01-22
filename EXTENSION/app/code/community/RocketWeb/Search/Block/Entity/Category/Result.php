<?php 
class RocketWeb_Search_Block_Entity_Category_Result extends RocketWeb_Search_Block_Entity_Abstract {
	public function getResultCollection() {
		if(!isset($this->_collection)) {
			$collection = Mage::getModel('catalog/category')->getCollection();
			$collection->setStoreId(Mage::app()->getStore()->getId());
			$collection->addAttributeToFilter('is_active',1);
			$collection->addAttributeToFilter('is_searcheable',1);
			$collection->addAttributeToSelect('name');
			$collection->addAttributeToSelect('description');

            if($collection instanceof Mage_Catalog_Model_Resource_Category_Flat_Collection) {
                $collection->getSelect()->joinInner(
                    array('search_result' => $this->getTable('rocketweb_search/result')),
                    $this->getConnection()->quoteInto(
                        'search_result.product_id=main_table.entity_id AND search_result.query_id=? AND entity_type=\'category\'',
                        Mage::helper('catalogsearch')->getQuery()->getId()
                    ),
                    array('relevance' => 'relevance')
                );
            }
            else {
                $collection->getSelect()->joinInner(
                    array('search_result' => $this->getTable('rocketweb_search/result')),
                    $this->getConnection()->quoteInto(
                        'search_result.product_id=e.entity_id AND search_result.query_id=? AND entity_type=\'category\'',
                        Mage::helper('catalogsearch')->getQuery()->getId()
                    ),
                    array('relevance' => 'relevance')
                );
            }


			
			$collection->getSelect()->order('relevance DESC');
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
	
	public function getRendererBlock($category) {
		$block = Mage::app()->getLayout()->createBlock($this->_renderer_block_type,'category_page_result_'.$category->getId());
		$block->setTemplate($this->_renderer_template);
		$block->setCategory($category);
		return $block;
	}
	
	public function getTitle() {
		$title = Mage::getStoreConfig('rocketweb_search/category_search/results_title');
		$title = str_replace('{{query}}',Mage::helper('catalogsearch')->getQuery()->getQueryText(),$title);
		$title = $this->htmlEscape($title);
		return $title;
	}
	
	protected function _prepareLayout()
	{
		parent::_prepareLayout();
		$pager = $this->getLayout()->createBlock('page/html_pager', 'category.searchresults.pager');
	
		$pager->setAvailableLimit(array(Mage::getStoreConfig('rocketweb_search/category_search/num_per_page')));
		$pager->setShowPerPage(Mage::getStoreConfig('rocketweb_search/category_search/num_per_page'));
		$pager->setLimit(Mage::getStoreConfig('rocketweb_search/category_search/num_per_page'));
		$pager->setPageVarName('cap');
		$pager->setLimitVarName('cal');
		$pager->setCollection($this->getResultCollection());
	
	
		$this->setChild('pager', $pager);
		return $this;
	}
}