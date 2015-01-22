<?php

class RocketWeb_Search_Block_Adminhtml_Directsearch_Edit_Tab_Products extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('dsresult_productgrid');
        $this->setDefaultSort('entity_id');
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
        $collection = Mage::getModel('catalog/product')->getCollection()
            ->addAttributeToSelect('name')
            ->addAttributeToSelect('sku')
            ->addAttributeToSelect('price');

        $query_id = $this->getRequest()->getParam('id', null);
        if($query_id)
        {
            $query_sku_table = Mage::getSingleton('core/resource')->getTableName('rocketweb_search/query_sku');
            $collection->getSelect()->joinLeft(
                array('query_sku' => $query_sku_table),
                'e.entity_id = query_sku.entity_id AND query_sku.entity_type=\'product\' AND query_sku.query_id = '.$query_id ,
                array('position' => 'order')
            );
        }

        if ($store->getId()) $collection->addStoreFilter($store);

        Mage::getSingleton('catalog/product_status')->addSaleableFilterToCollection($collection);
        $_visibility = array(
            Mage_Catalog_Model_Product_Visibility::VISIBILITY_BOTH,
            Mage_Catalog_Model_Product_Visibility::VISIBILITY_IN_CATALOG
        );

        $collection->addAttributeToFilter('visibility', $_visibility);
        $this->setCollection($collection);
        parent::_prepareCollection();
        $this->getCollection()->addWebsiteNamesToResult();
        return $this;
    }

    protected function _addColumnFilterToCollection($column)
    {
        if ($this->getCollection()) {
            if ($column->getId() == 'websites') {
                $this->getCollection()->joinField('websites',
                    'catalog/product_website',
                    'website_id',
                    'product_id=entity_id',
                    null,
                    'left');
            }
        }

        // Set custom filter for in product flag
        if ($column->getId() == 'in_products') {
            $productIds = $this->_getSelectedProducts();
            if (empty($productIds)) {
                $productIds = 0;
            }
            if ($column->getFilter()->getValue()) {
                $this->getCollection()->addFieldToFilter('entity_id', array('in'=>$productIds));
            } else {
                if($productIds) {
                    $this->getCollection()->addFieldToFilter('entity_id', array('nin'=>$productIds));
                }
            }
        } else {
            parent::_addColumnFilterToCollection($column);
        }
        return $this;

    }

    protected function _prepareColumns()
    {

        $this->addColumn('in_products',
            array(
                'header_css_class'  => 'a-center',
                'type'              => 'checkbox',
                'name'              => 'in_products',
                'values'            => $this->_getSelectedProducts(),
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





        $this->addColumn('name',
            array(
                'header'=> Mage::helper('catalog')->__('Name'),
                'index' => 'name',
            ));

        $store = $this->_getStore();
        if ($store->getId()) {
            $this->addColumn('custom_name',
                array(
                    'header'=> Mage::helper('catalog')->__('Name in %s', $store->getName()),
                    'index' => 'custom_name',
                ));
        }

        $this->addColumn('type',
            array(
                'header'=> Mage::helper('catalog')->__('Type'),
                'width' => '60px',
                'index' => 'type_id',
                'type'  => 'options',
                'options' => Mage::getSingleton('catalog/product_type')->getOptionArray(),
            ));

        $sets = Mage::getResourceModel('eav/entity_attribute_set_collection')
            ->setEntityTypeFilter(Mage::getModel('catalog/product')->getResource()->getTypeId())
            ->load()
            ->toOptionHash();

        $this->addColumn('set_name',
            array(
                'header'=> Mage::helper('catalog')->__('Attrib. Set Name'),
                'width' => '100px',
                'index' => 'attribute_set_id',
                'type'  => 'options',
                'options' => $sets,
            ));

        $this->addColumn('sku',
            array(
                'header'=> Mage::helper('catalog')->__('SKU'),
                'width' => '80px',
                'index' => 'sku',
            ));

        $store = $this->_getStore();
        $this->addColumn('price',
            array(
                'header'=> Mage::helper('catalog')->__('Price'),
                'type'  => 'price',
                'currency_code' => $store->getBaseCurrency()->getCode(),
                'index' => 'price',
            ));

        if (Mage::helper('catalog')->isModuleEnabled('Mage_CatalogInventory')) {
            $this->addColumn('qty',
                array(
                    'header'=> Mage::helper('catalog')->__('Qty'),
                    'width' => '100px',
                    'type'  => 'number',
                    'index' => 'qty',
                ));
        }

        $this->addColumn('visibility',
            array(
                'header'=> Mage::helper('catalog')->__('Visibility'),
                'width' => '70px',
                'index' => 'visibility',
                'type'  => 'options',
                'options' => Mage::getModel('catalog/product_visibility')->getOptionArray(),
            ));

        $this->addColumn('status',
            array(
                'header'=> Mage::helper('catalog')->__('Status'),
                'width' => '70px',
                'index' => 'status',
                'type'  => 'options',
                'options' => Mage::getSingleton('catalog/product_status')->getOptionArray(),
            ));

        if (!Mage::app()->isSingleStoreMode()) {
            $this->addColumn('websites',
                array(
                    'header'=> Mage::helper('catalog')->__('Websites'),
                    'width' => '100px',
                    'sortable'  => false,
                    'index'     => 'websites',
                    'type'      => 'options',
                    'options'   => Mage::getModel('core/website')->getCollection()->toOptionHash(),
                ));
        }

        return parent::_prepareColumns();
    }

    protected function _getStore()
    {
        $storeId = $this->getRequest()->getParam('store', 0);
        return Mage::app()->getStore($storeId);
    }

    public function getGridUrl()
    {
        return $this->_getData('grid_url') ? $this->_getData('grid_url') : $this->getUrl('*/*/productsgrid', array('_current'=>true));
    }

    public function getRowUrl($item)
    {
        return null;
    }

    protected function _getSelectedProducts()
    {
        $products = $this->getProducts();
        if (!is_array($products)) {
            $products = array_keys($this->getSelectedProducts());
        }
        return $products;
    }

    public function getSelectedProducts()
    {

        $query_id = $this->getRequest()->getParam('id', null);
        $ids = array();

        if($query_id)
        {
            $query = Mage::getModel('rocketweb_search/query')->load($query_id);
            if($query && $query->getId())
            {
                $query_data = $query->getData();
                if(!empty($query_data) && is_array($query_data) && array_key_exists('products', $query_data) && !empty($query_data['products']))
                {
                    foreach($query_data['products'] as $productItem)
                    {
                        $product = Mage::getModel('catalog/product')->load($productItem['entity_id']);
                        if($product && $product->getId());
                        $ids[$product->getId()] = array('position' => $productItem['order']);
                    }
                }
            }
        }
        return $ids;
    }
}
