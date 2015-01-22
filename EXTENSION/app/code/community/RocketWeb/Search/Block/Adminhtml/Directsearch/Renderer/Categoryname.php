<?php 
class RocketWeb_Search_Block_Adminhtml_Directsearch_Renderer_Categoryname extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {
	public function render(Varien_Object $row) {
		$ret = '';
		$category = Mage::getModel('catalog/category')->load($row->getEntityId());
		$categoryIds = explode('/', $category->getPath());
		foreach($categoryIds as $categoryId) {
			if($categoryId == 1) {
				continue;
			}
			$ret.=Mage::helper('rocketweb_search')->getCategoryName($categoryId).' > ';
		}
		$ret = preg_replace('/ > $/','',$ret);
		return $ret;
	}
}