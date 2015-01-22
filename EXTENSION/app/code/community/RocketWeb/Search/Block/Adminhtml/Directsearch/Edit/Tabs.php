<?php

class RocketWeb_Search_Block_Adminhtml_Directsearch_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{

  public function __construct()
  {
      parent::__construct();
      $this->setId('directsearch_tabs');
      $this->setDestElementId('edit_form');
      $this->setTitle(Mage::helper('rocketweb_search')->__('Direct Search Results Information'));
  }

  protected function _beforeToHtml()
  {
      $this->addTab('form_section', array(
          'label'     => Mage::helper('rocketweb_search')->__('Direct Search Results Information'),
          'title'     => Mage::helper('rocketweb_search')->__('Direct Search Results Information'),
          'content'   => $this->getLayout()->createBlock('rocketweb_search/adminhtml_directsearch_edit_tab_form')->toHtml(),
      ));

      $this->addTab('form_products', array(
          'label'     => Mage::helper('rocketweb_search')->__('Products'),
          'title'     => Mage::helper('rocketweb_search')->__('Products'),
          'url'       => $this->getUrl('*/*/products', array('_current' => true)),
          'class'     => 'ajax',
      ));
      
      $this->addTab('form_cms',array(
      	'label'     => Mage::helper('rocketweb_search')->__('CMS Pages'),
      	'title'     => Mage::helper('rocketweb_search')->__('CMS Pages'),
      	'url'       => $this->getUrl('*/*/cms', array('_current' => true)),
      	'class'     => 'ajax',
      ));
      
      $this->addTab('form_category',array(
      		'label'     => Mage::helper('rocketweb_search')->__('Categories'),
      		'title'     => Mage::helper('rocketweb_search')->__('Categories'),
      		'url'       => $this->getUrl('*/*/categories', array('_current' => true)),
      		'class'     => 'ajax',
      ));
      
      if(Mage::helper('rocketweb_search')->isAwBlogModuleEnabled()) {
      	$this->addTab('form_blog',array(
      			'label'     => Mage::helper('rocketweb_search')->__('Blog Posts'),
      			'title'     => Mage::helper('rocketweb_search')->__('Blog Posts'),
      			'url'       => $this->getUrl('*/*/blogs', array('_current' => true)),
      			'class'     => 'ajax',
      	));
      }
     
      return parent::_beforeToHtml();
  }
}