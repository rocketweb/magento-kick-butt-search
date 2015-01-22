<?php
class RocketWeb_Search_Block_Adminhtml_Directsearch_Edit_Tab_Categories extends Mage_Adminhtml_Block_Widget_Grid
{
	public function __construct()
	{
		parent::__construct();
		$this->setId('dsresult_categoriesgrid');
		$this->setDefaultSort('entity_id');
		$this->setUseAjax(true);
		$this->setSaveParametersInSession(false);
	}
	
	public function getMultipleRows($item)
	{
		return null;
	}
	
	
	protected function _prepareCollection()
	{
		$store = $this->getWebsite();
		
		$collection = Mage::getModel('catalog/category')->getCollection();
		$collection->addNameToResult();
		$collection->addFieldToFilter('entity_id',array('neq'=>1));
		$collection->getSelect()->order('path ASC');
		
		$query_id = $this->getRequest()->getParam('id', null);
		if($query_id)
		{
			$query_sku_table = Mage::getSingleton('core/resource')->getTableName('rocketweb_search/query_sku');
			$collection->getSelect()->joinLeft(
					array('query_sku' => $query_sku_table),
					'e.entity_id = query_sku.entity_id AND query_sku.entity_type=\'category\' AND query_sku.query_id = '.$query_id ,
					array('position' => 'order')
			);
		}
		$this->setCollection($collection);
		parent::_prepareCollection();
		return $this;
	}
	
	protected function _prepareColumns()
	{
	
		$this->addColumn('in_categories',
				array(
						'header_css_class'  => 'a-center',
						'type'              => 'checkbox',
						'name'              => 'in_products',
						'values'            => $this->_getSelectedCategories(),
						'align'             => 'center',
						'index'             => 'entity_id'
				));
	
		$this->addColumn('entity_id',
				array(
						'header'=> Mage::helper('catalog')->__('ID'),
						'width' => '50px',
						'type'  => 'number',
						'index' => 'entity_id',
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
	
		$this->addColumn('name',array(
				'header'=> Mage::helper('catalog')->__('Name'),
				'index' => 'name',
				'renderer' => 'RocketWeb_Search_Block_Adminhtml_Directsearch_Renderer_Categoryname'
		));
		
		return parent::_prepareColumns();
	}
	
	public function getGridUrl()
	{
		return $this->_getData('grid_url') ? $this->_getData('grid_url') : $this->getUrl('*/*/categoriesgrid', array('_current'=>true));
	}
	
	public function getRowUrl($item)
	{
		return null;
	}
	
	protected function _getSelectedCategories()
	{
		$categories = $this->getCategories();
		if (!is_array($categories)) {
			$categories = array_keys($this->getSelectedCategories());
		}
		return $categories;
	}
	
	public function getSelectedCategories()
	{
		$query_id = $this->getRequest()->getParam('id', null);
		$ids = array();
		
		if($query_id)
		{
			$query = Mage::getModel('rocketweb_search/query')->load($query_id);
			if($query && $query->getId())
			{
				$query_data = $query->getData();
				if(!empty($query_data) && is_array($query_data) && array_key_exists('categories', $query_data) && !empty($query_data['categories']))
				{
					foreach($query_data['categories'] as $categoryItem)
					{
						$category = Mage::getModel('catalog/category')->load($categoryItem['entity_id']);
						if($category && $category->getId());
						$ids[$category->getId()] = array('position' => $categoryItem['order']);
					}
				}
			}
		}
		return $ids;
	}
}