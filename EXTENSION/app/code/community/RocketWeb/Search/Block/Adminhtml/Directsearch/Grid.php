<?php

class RocketWeb_Search_Block_Adminhtml_Directsearch_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
  public function __construct()
  {
      parent::__construct();
      $this->setId('directsearchGrid');
      $this->setDefaultSort('query_id');
      $this->setDefaultDir('ASC');
      $this->setSaveParametersInSession(true);
  }

  protected function _prepareCollection()
  {
      $collection = Mage::getModel('rocketweb_search/query')->getCollection();
	  $collection->setFirstStoreFlag(true);
	  $collection->load();
      $this->setCollection($collection);
      return parent::_prepareCollection();
  }

  protected function _prepareColumns()
  {
      $this->addColumn('query_id', array(
          'header'    => Mage::helper('rocketweb_search')->__('ID'),
          'align'     =>'right',
          'width'     => '50px',
          'index'     => 'query_id',
      ));

      $this->addColumn('search_phrase', array(
          'header'    => Mage::helper('rocketweb_search')->__('Search Phrase'),
          'align'     =>'left',
          'index'     => 'search_phrase',
      ));
	  
      $this->addColumn('store_id', array(
            'header'        => Mage::helper('rocketweb_search')->__('Store View'),
            'index'         => 'store_id',
            'type'          => 'store',
            'store_all'     => true,
            'store_view'    => true,
            'sortable'      => false,
            'filter_condition_callback'
                            => array($this, '_filterStoreCondition'),
        ));
	  
      $this->addColumn('status', array(
          'header'    => Mage::helper('rocketweb_search')->__('Status'),
          'align'     => 'left',
          'width'     => '80px',
          'index'     => 'status',
          'type'      => 'options',
          'options'   => array(
              1 => 'Enabled',
              2 => 'Disabled',
          ),
      ));
      
      $this->addColumn('products',array(
      		'header'    =>  Mage::helper('rocketweb_search')->__('Products'),
      		'filter'    => false,
      		'sortable'  => false,
      		'renderer'  => 'RocketWeb_Search_Block_Adminhtml_Directsearch_Renderer_Products'
      ));
      
      $this->addColumn('cms',array(
      		'header'    =>  Mage::helper('rocketweb_search')->__('CMS Pages'),
      		'filter'    => false,
      		'sortable'  => false,
      		'renderer'  => 'RocketWeb_Search_Block_Adminhtml_Directsearch_Renderer_Cms'
      ));
      
      $this->addColumn('categories',array(
      		'header'    =>  Mage::helper('rocketweb_search')->__('Categories'),
      		'filter'    => false,
      		'sortable'  => false,
      		'renderer'  => 'RocketWeb_Search_Block_Adminhtml_Directsearch_Renderer_Categories'
      ));
      
      if(Mage::helper('rocketweb_search')->isAwBlogModuleEnabled()) {
	      $this->addColumn('blogs',array(
	      		'header'    =>  Mage::helper('rocketweb_search')->__('Blog Posts'),
	      		'filter'    => false,
	      		'sortable'  => false,
	      		'renderer'  => 'RocketWeb_Search_Block_Adminhtml_Directsearch_Renderer_Blogs'
	      ));
      }
	  
      $this->addColumn('action',
            array(
                'header'    =>  Mage::helper('rocketweb_search')->__('Action'),
                'width'     => '100',
                'type'      => 'action',
                'getter'    => 'getId',
                'actions'   => array(
                    array(
                        'caption'   => Mage::helper('rocketweb_search')->__('Edit'),
                        'url'       => array('base'=> '*/*/edit'),
                        'field'     => 'id'
                    )
                ),
                'filter'    => false,
                'sortable'  => false,
                'index'     => 'stores',
                'is_system' => true,
      ));
		
	  
     return parent::_prepareColumns();
  }

    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('query_id');
        $this->getMassactionBlock()->setFormFieldName('query_ids');

        $this->getMassactionBlock()->addItem('delete', array(
             'label'    => Mage::helper('rocketweb_search')->__('Delete'),
             'url'      => $this->getUrl('*/*/massDelete'),
             'confirm'  => Mage::helper('rocketweb_search')->__('Are you sure?')
        ));
        
        return $this;
    }

  public function getRowUrl($row)
  {
      return $this->getUrl('*/*/edit', array('id' => $row->getId()));
  }

  protected function _afterLoadCollection()
  {
        $this->getCollection()->walk('afterLoad');
        parent::_afterLoadCollection();
  }
  
  protected function _filterStoreCondition($collection, $column)
  {
        if (!$value = $column->getFilter()->getValue()) {
            return;
        }

        $this->getCollection()->addStoreFilter($value);
  }

}