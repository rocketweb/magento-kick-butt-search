<?php
class RocketWeb_Search_Helper_Data extends Mage_Core_Helper_Abstract
{
	public function getCategoryName($id) {
		if(!isset($this->_categoryPool[$id])) {
			$this->_categoryPool[$id] = Mage::getModel('catalog/category')->load($id);
		}
		return $this->_categoryPool[$id]->getName();
	}
	
	public function getProductCollectionForSkus($skus) {
		if(!isset($this->_productCollection)) {
			$collection = Mage::getModel('catalog/product')->getCollection();
			
			$collection
				->addAttributeToSelect(Mage::getSingleton('catalog/config')->getProductAttributes())
				->addMinimalPrice()
				->addFinalPrice()
				->addTaxPercents();
			
			$collection->addAttributeToFilter( 'sku', array( 'in' => $skus ) );
            $collection->addFieldToFilter('visibility', Mage_Catalog_Model_Product_Visibility::VISIBILITY_BOTH);
			
			$this->_productCollection = $collection;
		}
		
		return $this->_productCollection;
	}
	
	
	
	public function isBlogSearchEnabled() {
		if($this->isAwBlogModuleEnabled()) {
			return Mage::getStoreConfig('rocketweb_search/aw_blog_search/enable');
		}
		else {
			return false;
		}
	}
	
	public function blogHasIsSearcheableField() {
		$connection = Mage::getSingleton('core/resource');
		$read = $connection->getConnection('core_read');
		if ($read->tableColumnExists($connection->getTableName('blog/blog'), 'is_searcheable')) {
			return true;
		}
		else {
			return false;
		}
	}
	
	public function isAwBlogModuleEnabled() {
		if(method_exists(Mage::helper('core'), 'isModuleEnabled')) {
			return Mage::helper('core')->isModuleEnabled('AW_Blog');
		}
		else {
			if (!Mage::getConfig()->getNode('modules/AW_Blog')) {
				return false;
			}
			
			$isActive = Mage::getConfig()->getNode('modules/AW_Blog/active');
			if (!$isActive || !in_array((string)$isActive, array('true', '1'))) {
				return false;
			}
			return true;
		}
	}
        
        
        public function escapeForFulltext($text) {
            $replaceArray = array(
                0 => array('what'=>'-','with_what'=>'minus'),
                1 => array('what'=>'+','with_what'=>'plus'),
            );

            foreach($replaceArray as $replaceItem) {
                $text = str_replace($replaceItem['what'],$replaceItem['with_what'],$text);
            }

            return $text;
        }
}