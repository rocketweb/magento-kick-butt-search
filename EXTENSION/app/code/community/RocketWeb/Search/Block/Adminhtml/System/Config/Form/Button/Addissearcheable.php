<?php 
class RocketWeb_Search_Block_Adminhtml_System_Config_Form_Button_Addissearcheable extends Mage_Adminhtml_Block_System_Config_Form_Field {
	
	protected function _construct()
	{
		parent::_construct();
		$this->setTemplate('rocketweb_search/admin/addissearcheablebutton.phtml');
	}
	
	public function getButtonHtml()
	{
		$button = $this->getLayout()->createBlock('adminhtml/widget_button')->setData(array(
				'id'        => 'addissearcheable',
				'label'     => $this->helper('adminhtml')->__('Add "Is Searchable" attribute to blog pages'),
				'onclick'   => 'javascript:check(); return false;'
		));
	
		return $button->toHtml();
	}
	
	protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
	{
		if(!Mage::helper('rocketweb_search')->isBlogSearchEnabled()) {
			return '';
		}
		if(Mage::helper('rocketweb_search')->blogHasIsSearcheableField()) {
			return '';
		}
		return $this->_toHtml();
	}
	
	
	protected function getAjaxAddissearcheableUrl() {
		return Mage::getUrl('rocketweb_search/adminhtml_addsearcheablefieldtoblog/index');
	}
}