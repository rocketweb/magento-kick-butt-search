<?php 
abstract class RocketWeb_Search_Block_Entity_Abstract extends Mage_Core_Block_Template {
	public abstract function getResultCollection();
	
	protected function getConnection() {
		return Mage::getSingleton('core/resource')->getConnection('core_read');
	}
	protected function getTable($tableName) {
		return Mage::getSingleton('core/resource')->getTableName($tableName);
	}
	
	public function setRenderer($block_type,$template) {
		$this->_renderer_block_type = $block_type;
		$this->_renderer_template = $template;
	}
	
	public function getPagerHtml()
	{
		return $this->getChildHtml('pager');
	}
}