<?php
class RocketWeb_Search_Model_Rewrite_Mysql4_Fulltext extends Mage_CatalogSearch_Model_Mysql4_Fulltext {
	
    static $productTypes = array('configurable', 'bundle', 'grouped');
    
	/**
	 * Init resource model
	 *
	 */
	protected function _construct()
	{
		$this->_init('rocketweb_search/fulltext', 'product_id');
		$this->_engine = Mage::helper('catalogsearch')->getEngine();
	}
	
	public function prepareResult($object, $queryText, $query)
    {
            if(Mage::getStoreConfig('rocketweb_search/search_weights/enable_Escape')){
                $queryText = Mage::helper('rocketweb_search')->escapeForFulltext($queryText);
            }
        if (!$query->getIsProcessed()) {
            $searchType = $object->getSearchType($query->getStoreId());

            $stringHelper = Mage::helper('core/string');
            /* @var $stringHelper Mage_Core_Helper_String */

            $bind = array(
                ':query' => $queryText
            );
            $like = array();

            $fulltextCond   = '';
            $likeCond       = '';
            $separateCond   = '';

            if ($searchType == Mage_CatalogSearch_Model_Fulltext::SEARCH_TYPE_LIKE
                || $searchType == Mage_CatalogSearch_Model_Fulltext::SEARCH_TYPE_COMBINE) {
                $words = $stringHelper->splitWords($queryText, true, $query->getMaxQueryWords());
                $likeI = 0;
                foreach ($words as $word) {
                    $like[] = '`s`.`data_index` LIKE :likew' . $likeI;
                    $bind[':likew' . $likeI] = '%' . $word . '%';
                    $likeI ++;
                }
                if ($like) {
                    $likeCond = '(' . join(' OR ', $like) . ')';
                }
            }
            
			// daniel & pgrigoruta modified
            if ($searchType == Mage_CatalogSearch_Model_Fulltext::SEARCH_TYPE_FULLTEXT
                || $searchType == Mage_CatalogSearch_Model_Fulltext::SEARCH_TYPE_COMBINE) {
                if ($this->isWeightedSearchEnabled($query->getStoreId()))
                {
                	$fulltextCond = 'MATCH (`s`.`name_index`, `s`.`data_index`,`s`.`sku_index`) AGAINST (:query)';
                }
                else
                {
                	$fulltextCond = 'MATCH (`s`.`data_index`) AGAINST (:query)';
                }
            }
            if ($searchType == Mage_CatalogSearch_Model_Fulltext::SEARCH_TYPE_COMBINE && $likeCond) {
                $separateCond = ' OR ';
            }

            if ($this->isWeightedSearchEnabled( $query->getStoreId()))
            {
            	$prod_name_weight = $this->getProductNameWeight($query->getStoreId());
            	$sku_weight = $this->getSkuWeight($query->getStoreId());
            	
            	$sql = sprintf("INSERT INTO `{$this->getTable('rocketweb_search/result')}` "
	                . "(SELECT STRAIGHT_JOIN '%d', `s`.`product_id`,`s`.entity_type, (`s`.`product_boost`) * MATCH (`s`.`name_index`) "
	                . "AGAINST (:query) * " . $prod_name_weight  . " + "
	                . " (`s`.`product_boost`) *  MATCH (`s`.sku_index) "
	                . "AGAINST (:query) * " . $sku_weight  . " + "		
	                . " (`s`.`product_boost`) *  MATCH (`s`.`data_index`) "
	                . "AGAINST (:query) FROM `{$this->getMainTable()}` AS `s` "
	                . "LEFT JOIN `{$this->getTable('catalog/product')}` AS `e` "
	                . "ON `e`.`entity_id`=`s`.`product_id` WHERE (%s%s%s) AND `s`.`store_id`='%d')"
	                . " ON DUPLICATE KEY UPDATE `relevance`=VALUES(`relevance`)",
	                $query->getId(),
	                $fulltextCond,
	                $separateCond,
	                $likeCond,
	                $query->getStoreId()
	            );
            }
            else
            {
	            $sql = sprintf("INSERT INTO `{$this->getTable('rocketweb_search/result')}` "
	                . "(SELECT STRAIGHT_JOIN '%d', `s`.`product_id`,`s`.entity_type, (`s`.`product_boost`) *  MATCH (`s`.`data_index`) "
	                . "AGAINST (:query) FROM `{$this->getMainTable()}` AS `s` " // removed IN BOOLEAN MODE
	                . "LEFT JOIN `{$this->getTable('catalog/product')}` AS `e` "
	                . "ON `e`.`entity_id`=`s`.`product_id` WHERE (%s%s%s) AND `s`.`store_id`='%d')"
	                . " ON DUPLICATE KEY UPDATE `relevance`=VALUES(`relevance`)",
	                $query->getId(),
	                $fulltextCond,
	                $separateCond,
	                $likeCond,
	                $query->getStoreId()
	            );
            }
            
            $this->_getWriteAdapter()->query($sql, $bind);
            $query->setIsProcessed(1);
        }

        return $this;
    }
    
    
    protected function _prepareProductIndex($indexData, $productData, $storeId)
    {
    	$index_name = array();
    	$index_sku = array();
    	$index_boost = array();

    
    	$index = array();
    
    	foreach ($this->_getSearchableAttributes('static') as $attribute) {
    		if (isset($productData[$attribute->getAttributeCode()])) {
    			if ($value = $this->_getAttributeValue($attribute->getId(), $productData[$attribute->getAttributeCode()], $storeId)) {
    				if(Mage::getStoreConfig('rocketweb_search/search_weights/enable_Escape')){
                                    $value = Mage::helper('rocketweb_search')->escapeForFulltext($value);
                                }
    				//For grouped products
    				if (isset($index[$attribute->getAttributeCode()])) {
    					if (!is_array($index[$attribute->getAttributeCode()])) {
    						$index[$attribute->getAttributeCode()] = array($index[$attribute->getAttributeCode()]);
    					}
    					$index[$attribute->getAttributeCode()][] = $value;
    				}
    				//For other types of products
    				else {
    					$index[$attribute->getAttributeCode()] = $value;
    
    					if($this->isWeightedSearchEnabled( $storeId))
    					{
    						if($attribute->getAttributeCode() == "name")
    						{
    							$index_name[$attribute->getAttributeCode()] = $value;
    						}
    						if($attribute->getAttributeCode() == "sku")
    						{
    							$index_sku[$attribute->getAttributeCode()] = $value;
    						}
    					}
    				}
    			}
    		}
    	}

    	foreach ($indexData as $attributeData) {
    		foreach ($attributeData as $attributeId => $attributeValue) {
    			$value = $this->_getAttributeValue($attributeId, $attributeValue, $storeId);
    			if (!is_null($value) && $value !== false) {
    				$code = $this->_getSearchableAttribute($attributeId)->getAttributeCode();
    				//For grouped products
    				if (isset($index[$code])) {
    					if (!is_array($index[$code])) {
    						$index[$code] = array($index[$code]);
    					}
    					$index[$code][] = $value;
    				}
    				//For other types of products
    				else {
    					if ($code == "name")
    					{
    						if($this->isWeightedSearchEnabled($storeId))
    						{
    							$index_name[$code] = isset($indexData[$productData['entity_id']][$attributeId])?$indexData[$productData['entity_id']][$attributeId]:$value;
                                $index[$code] = $value;
    						}
                            else {
                                $index[$code] = $value;
                            }
    					}
    					elseif($code == 'sku') {
    						if($this->isWeightedSearchEnabled($storeId))
    						{
    							$index_sku[$code] = $value;
                                $index[$code] = $value;
    						}
                            else {
                                $index[$code] = $value;
                            }
    					}
    					elseif($code == 'search_boost') {
    						$index_boost = isset($indexData[$productData['entity_id']][$attributeId])?$indexData[$productData['entity_id']][$attributeId]:1;
    					}
    					else
    					{
    						$index[$code] = $value;
    					}
    				}
    
    			}
    		}
    	}

    
    	$product = $this->_getProductEmulator()
    	->setId($productData['entity_id'])
    	->setTypeId($productData['type_id'])
    	->setStoreId($storeId);
    	$typeInstance = $this->_getProductTypeInstance($productData['type_id']);
        
        if(in_array($productData['type_id'], self::$productTypes)){
            $index_sku = $this->getChildsSku(array_keys($indexData), $index_sku, $productData['entity_id']);
            $index['sku'] = Mage::helper('catalogsearch')->prepareIndexdata($index_sku, $this->_separator);
        }
        
    	if ($data = $typeInstance->getSearchableData($product)) {
    		$index['options'] = $data;
    	}
    
    	if (isset($productData['in_stock'])) {
    		$index['in_stock'] = $productData['in_stock'];
    	}
    	
    	if(empty($index_boost)) {
    		$index_boost = 1;
    	}
    
    	if ($this->_engine) {
    		if ($this->_engine->allowAdvancedIndex()) {
    			$index += $this->_engine->addAllowedAdvancedIndexField($productData);
    		}
    
    		if($this->isWeightedSearchEnabled($storeId))
    		{
    			$this->_engine->prepareEntityIndex(
    					array(
    							'data_index' => $index,
    							'name_index' => $index_name,
    							'sku_index'  => $index_sku,
    							'boost_index' => isset($index_boost)?$index_boost:1,
    					),
    					$this->_separator);
    		}
    		else
    		{
    			return $this->_engine->prepareEntityIndex(
    					array(
    							'data_index' => $index,
    							'boost_index' => isset($index_boost)?$index_boost:1,
    					), 
    					$this->_separator);
    		}
    	}
    
    	if($this->isWeightedSearchEnabled($storeId))
    	{
    		return array(
    				'name_index' => Mage::helper('catalogsearch')->prepareIndexdata($index_name, $this->_separator),
    				'sku_index'  => Mage::helper('catalogsearch')->prepareIndexdata($index_sku, $this->_separator),
     				'data_index' => Mage::helper('catalogsearch')->prepareIndexdata($index, $this->_separator),
    				'boost_index' => isset($index_boost)?$index_boost:1
    		);
    	} else if(Mage::getStoreConfig('rocketweb_search/search_weights/enable_sku')){
                return array(
    				'name_index' => Mage::helper('catalogsearch')->prepareIndexdata($index_name, $this->_separator),
                                'sku_index'  => Mage::helper('catalogsearch')->prepareIndexdata($index_sku, $this->_separator),
    				'boost_index' => isset($index_boost)?$index_boost:1
    		);
        }
    	else
    	{
    		return array(
    				'name_index' => Mage::helper('catalogsearch')->prepareIndexdata($index_name, $this->_separator),
    				'boost_index' => isset($index_boost)?$index_boost:1
    		);
    	}
    }
    
    
    
    public function getProductNameWeight($storeId)
    {
    	if(!$this->isWeightedSearchEnabled()) {
    		return 1;
    	}
    	 
    	$product_name_weight = (int) Mage::getStoreConfig('rocketweb_search/search_weights/title_weight', $storeId);
    	 
    	return $product_name_weight;
    }
    
    public function getSkuWeight($storeId) {
    	if(!$this->isWeightedSearchEnabled()) {
    		return 1;
    	}
    	
    	$sku_weight = (int) Mage::getStoreConfig('rocketweb_search/search_weights/sku_weight', $storeId);
    	
    	return $sku_weight;
    }
    
    public function isWeightedSearchEnabled($storeId = null) {
    	return Mage::getStoreConfig('rocketweb_search/search_weights/enable', $storeId);
    } 
    
    public function resetSearchResults()
    {
    	$adapter = $this->_getWriteAdapter();
    	$adapter->update($this->getTable('catalogsearch/search_query'), array('is_processed' => 0));
    	$adapter->delete($this->getTable('rocketweb_search/result'));
    
    	Mage::dispatchEvent('catalogsearch_reset_search_result');
    
    	return $this;
    }
    
    
    
    public function saveProductIndexes($storeId, $productIndexes) {
    	$this->_saveProductIndexes($storeId, $productIndexes);
    	return $this;
    }
    
    
    public function getChildsSku($productIds, $index_sku = array(), $productId = null){
        if(!Mage::getStoreConfig('rocketweb_search/search_weights/enable_sku')){
            if(Mage::getStoreConfig('rocketweb_search/search_weights/enable_Escape')){
                return Mage::helper('rocketweb_search')->escapeForFulltext($index_sku);
            }
            return $index_sku;
        }
        
        $adapter = $this->_getWriteAdapter();
        $tableName = $this->getTable('catalog/product');
        $select = $adapter  ->select()
                            ->from(
                                array('t_default' => $tableName),
                                array('entity_id', 'sku'))
                            ->where('t_default.entity_id IN (?)', $productIds);
        $query = $adapter->query($select);
        $sku = array();
        while ($row = $query->fetch()) {
            if(Mage::getStoreConfig('rocketweb_search/search_weights/enable_Escape')){
                $sku['sku'][$row['entity_id']] = Mage::helper('rocketweb_search')->escapeForFulltext($row['sku']);
            } else {
                $sku['sku'][$row['entity_id']] = $row['sku'];
            }
        }
        if(isset($productId) && !empty($index_sku) && !array_key_exists($productId, $sku['sku'])){
            if(Mage::getStoreConfig('rocketweb_search/search_weights/enable_Escape')){
                $sku['sku'][$productId] = Mage::helper('rocketweb_search')->escapeForFulltext($index_sku['sku']);
            } else {
                $sku['sku'][$productId] = $index_sku['sku'];
            }
        }
        
        return $sku;
    }
}