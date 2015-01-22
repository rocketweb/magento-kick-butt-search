<?php
class RocketWeb_Search_Block_Adminhtml_Directsearch_Renderer_Cms extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {
	public function render(Varien_Object $row) {
		$queryItem = Mage::getModel('rocketweb_search/query')->load($row->getQueryId());
		$cms = $queryItem->getCms();
		if(is_array($cms) && count($cms)) {
			$cmsCol = Mage::getModel('cms/page')->getCollection();
			$cmsCol->addFieldToFilter('page_id',array('in'=>$cms));
			$ret = '';
			foreach($cmsCol as $cmsPage) {
				$ret.=$cmsPage->getTitle().', ';
			}
			$ret = preg_replace('/, $/','',$ret);
			return $ret;
		}
		else {
			return '-';
		}
	}
}