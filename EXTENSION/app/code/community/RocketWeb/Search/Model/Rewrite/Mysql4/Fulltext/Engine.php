<?php
class RocketWeb_Search_Model_Rewrite_Mysql4_Fulltext_Engine extends Mage_CatalogSearch_Model_Mysql4_Fulltext_Engine {
	
	/**
	 * Init resource model
	 *
	 */
	protected function _construct()
	{
		$this->_init('rocketweb_search/fulltext', 'product_id');
	}
	
	public function saveEntityIndex($entityId, $storeId, $index, $entity = 'product')
	{
		if(!empty($index['entity_type']) && $index['entity_type'] != 'product') {
			$this->_getWriteAdapter()->insert($this->getMainTable(), array(
					'product_id'    => (int)$entityId,
					'store_id'      => $index['store_id'],
					'data_index'    => $index['data_index'],
					'name_index'    => $index['name_index'],
					'sku_index'     => $index['sku_index'],
					'product_boost' => $index['boost_index'],
					'entity_type'   => $index['entity_type']
			));
		}
		else {
			if($this->isWeightedSearchEnabled($storeId)) {
				$this->_getWriteAdapter()->insert($this->getMainTable(), array(
						'product_id'    => $entityId,
						'store_id'      => $storeId,
						'data_index'    => $index['data_index'],
						'name_index'    => $index['name_index'],
						'sku_index'     => $index['sku_index'],
						'product_boost' => $index['boost_index'],
						'entity_type'   => 'product'
				));
			}
			else {
				$this->_getWriteAdapter()->insert($this->getMainTable(), array(
						'product_id'    => $entityId,
						'store_id'      => $storeId,
						'data_index'    => $index['data_index'],
						'product_boost' => $index['boost_index'],
						'entity_type'   => 'product'
				));
			}
		}
		
		
		return $this;
	}
	
	
	public function saveEntityIndexes($storeId, $entityIndexes, $entity = 'product')
	{
		$adapter = $this->_getWriteAdapter();
		$data    = array();
		$storeId = (int)$storeId;
		foreach ($entityIndexes as $entityId => $index) {
			
			if(!empty($index['entity_type']) && $index['entity_type'] != 'product') {
				$data[] = array(
						'product_id'    => (int)$entityId,
						'store_id'      => $index['store_id'],
						'data_index'    => $index['data_index'],
						'name_index'    => $index['name_index'],
						'sku_index'     => $index['sku_index'],
						'product_boost' => $index['boost_index'],
						'entity_type'   => $index['entity_type']
				);
			}
			else {
				if($this->isWeightedSearchEnabled($storeId)) {
					$data[] = array(
							'product_id'    => (int)$entityId,
							'store_id'      => $storeId,
							'data_index'    => $index['data_index'],
							'name_index'    => $index['name_index'],
							'sku_index'     => $index['sku_index'],
							'product_boost' => $index['boost_index'],
							'entity_type'   => 'product'
					);
				}
				else {
					$data[] = array(
							'product_id'    => (int)$entityId,
							'store_id'      => $storeId,
							'data_index'    => $index['data_index'],
							'product_boost' => $index['boost_index'],
							'entity_type'   => 'product'
					);
				}
			}
		}

		if ($data) {
			if($this->isWeightedSearchEnabled($storeId)) {
				$adapter->insertOnDuplicate($this->getMainTable(), $data, array('data_index', 'name_index', 'sku_index', 'product_boost'));
			}
			else {
				$adapter->insertOnDuplicate($this->getMainTable(), $data, array('data_index', 'product_boost'));
			}
		}
	
		return $this;
	}
	
	public function prepareEntityIndex($index, $separator = ' ')
	{
		if (isset($index['name_index']) || isset($index['sku_index'])) {
			return array(
					'data_index' => Mage::helper('catalogsearch')->prepareIndexdata($index['data_index'], $separator),
					'name_index' => Mage::helper('catalogsearch')->prepareIndexdata($index['name_index'], $separator),
					'sku_index' => Mage::helper('catalogsearch')->prepareIndexdata($index['sku_index'], $separator),
					'boost_index' => $index['boost_index'],
					'entity_type'   => 'product'
			);
		}
		else {
			 return array(
					'data_index' => Mage::helper('catalogsearch')->prepareIndexdata($index['data_index'], $separator),
					'boost_index' => $index['boost_index'],
			 		'entity_type'   => 'product'
			);
		}
		
	}
	
	
	public function isWeightedSearchEnabled($storeId = null) {
		return Mage::getStoreConfig('rocketweb_search/search_weights/enable', $storeId);
	}
	
	
	/**
	 * Remove entity data from fulltext search table
	 *
	 * @param int $storeId
	 * @param int $entityId
	 * @param string $entity 'product'|'cms'
	 * @return Mage_CatalogSearch_Model_Resource_Fulltext_Engine
	 */
	public function cleanIndex($storeId = null, $entityId = null, $entity = 'product')
	{
		$where = array();
	
		if (!is_null($storeId)) {
			$where[] = $this->_getWriteAdapter()->quoteInto('store_id=?', $storeId);
		}
		if (!is_null($entityId)) {
			$where[] = $this->_getWriteAdapter()->quoteInto('product_id IN (?)', $entityId);
		}
		
		$where[] = $this->_getWriteAdapter()->quoteInto('entity_type = ?',$entity);
	
		$this->_getWriteAdapter()->delete($this->getMainTable(), $where);
	
		return $this;
	}
}