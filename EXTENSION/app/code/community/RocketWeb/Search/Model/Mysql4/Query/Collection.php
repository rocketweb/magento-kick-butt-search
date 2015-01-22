<?php

class RocketWeb_Search_Model_Mysql4_Query_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
	protected $_previewFlag;
	
	
    public function _construct()
    {
        parent::_construct();
        $this->_init('rocketweb_search/query');
		
		$this->_map['fields']['query_id'] = 'main_table.query_id';
        $this->_map['fields']['store']   = 'store_table.store_id';
		$this->_map['fields']['sku']   = 'sku_table.store_id';
    }
	
	
	public function setFirstStoreFlag($flag = false)
    {
        $this->_previewFlag = $flag;
        return $this;
    }
	
	protected function _afterLoad()
    {
        if ($this->_previewFlag) {
            $items = $this->getColumnValues('query_id');
            if (count($items)) {
                $select = $this->getConnection()->select()
                        ->from($this->getTable('rocketweb_search/query_store'))
                        ->where($this->getTable('rocketweb_search/query_store').'.query_id IN (?)', $items);
                if ($result = $this->getConnection()->fetchPairs($select)) {
                    foreach ($this as $item) {
                        if (!isset($result[$item->getData('query_id')])) {
                            continue;
                        }
                        if ($result[$item->getData('query_id')] == 0) {
                            $stores = Mage::app()->getStores(false, true);
                            $storeId = current($stores)->getId();
                            $storeCode = key($stores);
                        } else {
                            $storeId = $result[$item->getData('query_id')];
                            $storeCode = Mage::app()->getStore($storeId)->getCode();
                        }
                        $item->setData('_first_store_id', $storeId);
                        $item->setData('store_code', $storeCode);
                    }
                }
            }
        }

        parent::_afterLoad();
    }


	public function addStoreFilter($store, $withAdmin = true)
    {
        if ($store instanceof Mage_Core_Model_Store) {
            $store = array($store->getId());
        }
        $this->addFilter('store', array('in' => ($withAdmin ? array(0, $store) : $store)), 'public');
        return $this;
    }

	protected function _renderFiltersBefore()
    {
        if ($this->getFilter('store')) {
            $this->getSelect()->join(
                array('store_table' => $this->getTable('rocketweb_search/query_store')),
                'main_table.query_id = store_table.query_id',
                array()
            )->group('main_table.query_id');
        }
        return parent::_renderFiltersBefore();
    }
	
	public function getSelectCountSql()
    {
        $countSelect = parent::getSelectCountSql();

        $countSelect->reset(Zend_Db_Select::GROUP);

        return $countSelect;
    }

}