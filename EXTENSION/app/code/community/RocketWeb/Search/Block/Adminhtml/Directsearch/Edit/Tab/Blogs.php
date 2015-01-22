<?php 
class RocketWeb_Search_Block_Adminhtml_Directsearch_Edit_Tab_Blogs extends AW_Blog_Block_Manage_Blog_Grid
{
	public function __construct()
	{
		parent::__construct();
		$this->setId('dsresult_bloggrid');
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
		$collection = Mage::getModel('blog/blog')->getCollection();
	
		$query_id = $this->getRequest()->getParam('id', null);
		if($query_id)
		{
			$query_sku_table = Mage::getSingleton('core/resource')->getTableName('rocketweb_search/query_sku');
			$collection->getSelect()->joinLeft(
					array('query_sku' => $query_sku_table),
					'main_table.post_id = query_sku.entity_id AND query_sku.query_id = '.$query_id ,
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
		$this->addColumn('in_blogs',array(
				'header_css_class'  => 'a-center',
				'type'              => 'checkbox',
				'name'              => 'in_blogs',
				'values'            => $this->_getSelectedBlogs(),
				'align'             => 'center',
				'index'             => 'post_id'
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
	
		$this->removeColumn('action');
	}
	
	protected function _getSelectedBlogs()
	{
		$blogs = $this->getBlogs();
		if (!is_array($blogs)) {
			$blogs = array_keys($this->getSelectedBlogs());
		}
		return $blogs;
	}
	
	public function getSelectedBlogs()
	{
	
		$query_id = $this->getRequest()->getParam('id', null);
		$ids = array();
	
		if($query_id)
		{
			$query = Mage::getModel('rocketweb_search/query')->load($query_id);
			if($query && $query->getId())
			{
				$query_data = $query->getData();
				if(!empty($query_data) && is_array($query_data) && array_key_exists('blogs', $query_data) && !empty($query_data['blogs']))
				{
					foreach($query_data['blogs'] as $blog)
					{
						$page = Mage::getModel('blog/post')->load($blog['entity_id'],'post_id');
						if($page && $page->getPostId());
						$ids[$page->getPostId()] = array('position' => $blog['order']);
					}
				}
			}
		}
		return $ids;
	}
	
	public function getRowUrl($row) {
		return '';
	}
	
	protected function _prepareMassaction() {
		return $this;
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