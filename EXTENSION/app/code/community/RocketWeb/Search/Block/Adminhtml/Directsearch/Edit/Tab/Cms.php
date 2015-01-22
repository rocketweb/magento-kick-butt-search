<?php 
class RocketWeb_Search_Block_Adminhtml_Directsearch_Edit_Tab_Cms extends Mage_Adminhtml_Block_Cms_Page_Grid
{
	public function __construct()
	{
		parent::__construct();
		$this->setId('dsresult_cmsgrid');
		$this->setDefaultSort('page_id');
		$this->setUseAjax(true);
		$this->setSaveParametersInSession(false);
	}
	
	protected function getWebsite()
	{
		$storeId = (int)$this->getRequest()->getParam('store', 0);
		return Mage::app()->getStore($storeId);
	}
	
	protected function _prepareCollection()
	{
		$store = $this->getWebsite();
		$collection = Mage::getModel('cms/page')->getCollection();
		
		$query_id = $this->getRequest()->getParam('id', null);
		if($query_id)
		{
			$query_sku_table = Mage::getSingleton('core/resource')->getTableName('rocketweb_search/query_sku');
			$collection->getSelect()->joinLeft(
					array('query_sku' => $query_sku_table),
					'main_table.page_id = query_sku.entity_id AND query_sku.query_id = '.$query_id ,
					array('position' => 'order')
			);
		}
		
		if ($store->getId()) $collection->addStoreFilter($store);
		$collection->addFieldToFilter('is_active',1);
		$this->setCollection($collection);
		parent::_prepareCollection();
		return $this;
	}
	
	protected function _prepareColumns()
	{
		$this->addColumn('in_cms',array(
			'header_css_class'  => 'a-center',
			'type'              => 'checkbox',
			'name'              => 'in_cms',
			'values'            => $this->_getSelectedCms(),
			'align'             => 'center',
			'index'             => 'page_id'
		));
		$this->addColumn('page_id',array(
			'header'=> Mage::helper('rocketweb_search')->__('ID'),
			'width' => '50px',
			'type'  => 'number',
			'index' => 'page_id',
		));
		$this->addColumn('position', array(
			'header'            => Mage::helper('catalog')->__('Order'),
			'name'              => 'position',
			'width'             => 60,
			'type'              => 'input',
			'validate_class'    => 'validate-number',
			'index'             => 'position',
			'editable'          => true,
			'edit_only'         => true
		));
		
		parent::_prepareColumns();
		
		$this->removeColumn('page_actions');
	}
	
	protected function _getSelectedCms()
	{
		$cms = $this->getCms();
		if (!is_array($cms)) {
			$cms = array_keys($this->getSelectedCms());
		}
		return $cms;
	}
	
	public function getSelectedCms()
	{
	
		$query_id = $this->getRequest()->getParam('id', null);
		$ids = array();
	
		if($query_id)
		{
			$query = Mage::getModel('rocketweb_search/query')->load($query_id);
			if($query && $query->getId())
			{
				$query_data = $query->getData();
				if(!empty($query_data) && is_array($query_data) && array_key_exists('cms', $query_data) && !empty($query_data['cms']))
				{
					foreach($query_data['cms'] as $cms)
					{	
						$page = Mage::getModel('cms/page')->load($cms['entity_id']);
						if($page && $page->getPageId());
						$ids[$page->getPageId()] = array('position' => $cms['order']);
					}
				}
			}
		}
		return $ids;
	}
	
	public function getRowUrl($row) {
		return '';
	}
	
	
	/**
	 * Remove existing column. Adding this method here as it is not implemented in older Magento versions
	 *
	 * @param string $columnId
	 * @return Mage_Adminhtml_Block_Widget_Grid
	 */
	public function removeColumn($columnId)
	{
		if (isset($this->_columns[$columnId])) {
			unset($this->_columns[$columnId]);
			if ($this->_lastColumnId == $columnId) {
				$this->_lastColumnId = key($this->_columns);
			}
		}
		return $this;
	}
}