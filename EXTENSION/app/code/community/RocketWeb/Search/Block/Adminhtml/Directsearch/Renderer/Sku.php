<?php
class RocketWeb_Search_Block_Adminhtml_Directsearch_Renderer_Sku extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {
	public function render(Varien_Object $row) {
		$value =  $row->getData($this->getColumn()->getIndex());
		$ret = '';
		
		foreach($value as $val) {
			$ret.=$val['sku'].', ';
		}
		$ret = preg_replace('/, $/','',$ret);
		
		return $ret;
	}
}