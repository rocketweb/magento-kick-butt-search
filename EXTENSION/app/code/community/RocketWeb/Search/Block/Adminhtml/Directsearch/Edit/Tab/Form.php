<?php

class RocketWeb_Search_Block_Adminhtml_Directsearch_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
  protected function _prepareForm()
  {
      $form = new Varien_Data_Form();
      $form->setFormExcludedFieldList('product_sku');
      $this->setForm($form);
      $fieldset = $form->addFieldset('directsearch_form', array('legend'=>Mage::helper('rocketweb_search')->__('Direct Search Result information')));
     
      $fieldset->addField('search_phrase', 'text', array(
          'label'     => Mage::helper('rocketweb_search')->__('Search Phrase'),
          'class'     => 'required-entry',
          'required'  => true,
          'name'      => 'search_phrase',
      ));
	  
	  $fieldset->addField('store_id', 'multiselect', array(
                'name'      => 'stores[]',
                'label'     => Mage::helper('rocketweb_search')->__('Store View'),
                'title'     => Mage::helper('rocketweb_search')->__('Store View'),
                'required'  => true,
                'values'    => Mage::getSingleton('adminhtml/system_store')->getStoreValuesForForm(false, true)
            ));

	  $fieldset->addField('query_status', 'select', array(
          'label'     => Mage::helper('rocketweb_search')->__('Status'),
          'name'      => 'query_status',
          'values'    => array(
              array(
                  'value'     => 1,
                  'label'     => Mage::helper('rocketweb_search')->__('Enabled'),
              ),

              array(
                  'value'     => 2,
                  'label'     => Mage::helper('rocketweb_search')->__('Disabled'),
              ),
          ),
      ));
     
     
      if ( Mage::getSingleton('adminhtml/session')->getDirectQueryData() )
      {
          $form->setValues(Mage::getSingleton('adminhtml/session')->getDirectQueryData());
          Mage::getSingleton('adminhtml/session')->setDirectQueryData(null);
      } elseif ( Mage::registry('query_data') ) {
          $form->setValues(Mage::registry('query_data')->getData());
      }
      return parent::_prepareForm();
  }
}