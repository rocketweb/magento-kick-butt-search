<?php
class RocketWeb_Search_Block_Adminhtml_Directsearch_Renderer_Products extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {
	public function render(Varien_Object $row) {
		$queryItem = Mage::getModel('rocketweb_search/query')->load($row->getQueryId());
		$products = $queryItem->getProducts();
		if(is_array($products) && count($products)) {
			$productCol = Mage::getModel('catalog/product')->getCollection();
			$productCol->addAttributeToSelect('sku');
			$productCol->addFieldToFilter('entity_id',array('in'=>$products));
			$ret = '';
			foreach($productCol as $product) {
				$ret.=$product->getSku().', ';
			}
			$ret = preg_replace('/, $/','',$ret);
			return $ret;
		}
		else {
			return '-';
		}
	}
}