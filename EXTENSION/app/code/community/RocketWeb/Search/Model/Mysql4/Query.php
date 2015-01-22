<?php

class RocketWeb_Search_Model_Mysql4_Query extends Mage_Core_Model_Mysql4_Abstract
{
	protected $_store = null;
	
    public function _construct()
    {    
        $this->_init('rocketweb_search/query', 'query_id');
    }
	
	//asign to store
	protected function _afterSave(Mage_Core_Model_Abstract $object) {
		$condition = $this->_getWriteAdapter()->quoteInto('query_id = ?', $object->getId());
		$this->_getWriteAdapter()->delete($this->getTable('rocketweb_search/query_store'), $condition);
		
		foreach ((array)$object->getData('stores') as $store) {
			$storeArray = array();
            $storeArray['query_id'] = $object->getId();
            $storeArray['store_id'] = $store;
			$this->_getWriteAdapter()->insert($this->getTable('rocketweb_search/query_store'), $storeArray);
		}
		
		$condition = $this->_getWriteAdapter()->quoteInto('query_id = ?', $object->getId());
		$this->_getWriteAdapter()->delete($this->getTable('rocketweb_search/query_sku'), $condition);
		
		$items = $object->getData('products');//var_dump($items);
		$addArray = array();
		if(is_array($items)) {
			foreach($items as $item) {
				if(empty($item['delete'])) {
					$addArray['query_id'] = $object->getId();
					$addArray['entity_type'] = 'product';
					$addArray['entity_id'] = $item['entity_id'];
					$addArray['order'] = $item['order'];
					$this->_getWriteAdapter()->insert($this->getTable('rocketweb_search/query_sku'), $addArray);
				}
			}	
		}
		
		
		$items = $object->getData('blogs');//var_dump($items);
		$addArray = array();
		if(is_array($items)) {
			foreach($items as $item) {
				if(empty($item['delete'])) {
					$addArray['query_id'] = $object->getId();
					$addArray['entity_type'] = 'blog';
					$addArray['entity_id'] = $item['entity_id'];
					$addArray['order'] = $item['order'];
					$this->_getWriteAdapter()->insert($this->getTable('rocketweb_search/query_sku'), $addArray);
				}
			}
		}
		
		$items = $object->getData('cms');//var_dump($items);
		$addArray = array();
		if(is_array($items)) {
			foreach($items as $item) {
				if(empty($item['delete'])) {
					$addArray['query_id'] = $object->getId();
					$addArray['entity_type'] = 'cms';
					$addArray['entity_id'] = $item['entity_id'];
					$addArray['order'] = $item['order'];
					$this->_getWriteAdapter()->insert($this->getTable('rocketweb_search/query_sku'), $addArray);
				}
			}
		}
		
		$items = $object->getData('categories');//var_dump($items);
		$addArray = array();
		if(is_array($items)) {
			foreach($items as $item) {
				if(empty($item['delete'])) {
					$addArray['query_id'] = $object->getId();
					$addArray['entity_type'] = 'category';
					$addArray['entity_id'] = $item['entity_id'];
					$addArray['order'] = $item['order'];
					$this->_getWriteAdapter()->insert($this->getTable('rocketweb_search/query_sku'), $addArray);
				}
			}
		}
		
		//die();
		
		
		return parent::_afterSave($object);
	}
	
	protected function _afterLoad(Mage_Core_Model_Abstract $object) {
		$select = $this->_getReadAdapter()->select()
            ->from($this->getTable('rocketweb_search/query_store'))
            ->where('query_id = ?', $object->getId());
			
		if ($data = $this->_getReadAdapter()->fetchAll($select)) {
			$storesArray = array();
            foreach ($data as $row) {
                $storesArray[] = $row['store_id'];
            }
            $object->setData('store_id', $storesArray);
		}
		
		
		$productIds = array();
		$cmsIds = array();
		$blogIds = array();
		$categoryIds = array();
		
		$select = $this->_getReadAdapter()->select()
            ->from($this->getTable('rocketweb_search/query_sku'))
            ->where('query_id = ?', $object->getId())
			->order('order ASC ');
		if ($data = $this->_getReadAdapter()->fetchAll($select)) {
            foreach ($data as $row) {
                $entity = array('order' => $row['order'],'entity_id'=>$row['entity_id'] );
                switch($row['entity_type']) {
                	case 'product':
                		$productIds[]=$entity;
                		break;
                	case 'cms':
                		$cmsIds[]=$entity;
                		break;
                	case 'blog':
                		$blogIds[]=$entity;
                		break;
                	case 'category':
                		$categoryIds[]=$entity;
                		break;
                }
            }
            $object->setData('products', $productIds);
            $object->setData('blogs', $blogIds);
            $object->setData('cms', $cmsIds);
            $object->setData('categories', $categoryIds);
		}

		return parent::_afterLoad($object);
	}
	
	protected function _getLoadSelect($field, $value, $object) {
		$select = parent::_getLoadSelect($field, $value, $object);
		$storeId = $object->getStoreId();
		
		if ($storeId) {
            $select->join(
                        array('dsqs' => $this->getTable('rw_search_query_store')),
                        $this->getMainTable().'.query_id = `dsqs`.query_id'
                    )
                    ->where('status=1 AND `dsqs`.store_id IN (' . Mage_Core_Model_App::ADMIN_STORE_ID . ', ?) ', $storeId)
                    ->order('store_id DESC')
                    ->limit(1);
        }
        return $select;
	}
	
	public function lookupStoreIds($id) {
        return $this->_getReadAdapter()->fetchCol($this->_getReadAdapter()->select()
            ->from($this->getTable('rocketweb_search/query_store'), 'store_id')
            ->where("{$this->getIdFieldName()} = ?", $id)
        );
    }
	
	
	public function setStore($store) {
        $this->_store = $store;
        return $this;
    }
	
	public function getStore() {
        return Mage::app()->getStore($this->_store);
    }
    
    protected function _afterDelete(Mage_Core_Model_Abstract $object)
    {
    	$condition = $this->_getWriteAdapter()->quoteInto('query_id = ?', $object->getId());
    	$this->_getWriteAdapter()->delete($this->getTable('rocketweb_search/query_sku'), $condition);
    	
    	return parent::_afterDelete($object);
    }
	
}