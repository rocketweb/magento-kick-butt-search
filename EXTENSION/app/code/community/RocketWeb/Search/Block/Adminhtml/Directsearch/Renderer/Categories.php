<?php
class RocketWeb_Search_Block_Adminhtml_Directsearch_Renderer_Categories extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {
	public function render(Varien_Object $row) {
		$queryItem = Mage::getModel('rocketweb_search/query')->load($row->getQueryId());
		$categories = $queryItem->getCategories();
		if(is_array($categories) && count($categories)) {
			$categoryCol = Mage::getModel('catalog/category')->getCollection();
			$categoryCol->addNameToResult();
			$categoryCol->addFieldToFilter('entity_id',array('in'=>$categories));
			$ret = '';
			foreach($categoryCol as $category) {
				$ret.=$category->getName().', ';
			}
			$ret = preg_replace('/, $/','',$ret);
			return $ret;
		}
		else {
			return '-';
		}
	}
}