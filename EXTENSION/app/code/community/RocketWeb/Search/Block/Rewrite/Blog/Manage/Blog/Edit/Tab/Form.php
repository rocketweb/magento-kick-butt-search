<?php 
class RocketWeb_Search_Block_Rewrite_Blog_Manage_Blog_Edit_Tab_Form extends AW_Blog_Block_Manage_Blog_Edit_Tab_Form {
	protected function _prepareForm() {
		$ret = parent::_prepareForm();
		$form = $this->getForm();
		
		if (Mage::getSingleton('adminhtml/session')->getBlogData()) {
			$data = Mage::getSingleton('adminhtml/session')->getBlogData();
		}
		elseif (Mage::registry('blog_data')) { 
			$data = Mage::registry('blog_data')->getData();
		}
		
		if(!isset($data['is_searcheable'])) {
			$data['is_searcheable'] = 1;
		}
		
		$fieldset = $form->getElement('blog_form');
		$fieldset->addField('is_searcheable', 'select', array(
			'label'  => Mage::helper('rocketweb_search')->__('Is Searchable?'),
			'title'  => Mage::helper('rocketweb_search')->__('Is Searchable?'),
			'name'   => 'is_searcheable',
			'values' => Mage::getSingleton('adminhtml/system_config_source_yesno')->toOptionArray(),
			'value'=>1
		));
		if (Mage::getSingleton('adminhtml/session')->getBlogData()) {
			$form->setValues($data);
			Mage::getSingleton('adminhtml/session')->setBlogData(null);
		} elseif (Mage::registry('blog_data')) {
			Mage::registry('blog_data')->setTags(Mage::helper('blog')->convertSlashes(Mage::registry('blog_data')->getTags()));
			$form->setValues($data);
		}
		
		return $ret;
	}
}