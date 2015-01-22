<?php
class RocketWeb_Search_Block_Adminhtml_Directsearch extends Mage_Adminhtml_Block_Widget_Grid_Container
{
  public function __construct()
  {
    $this->_controller = 'adminhtml_directsearch';
    $this->_blockGroup = 'rocketweb_search';
    $this->_headerText = Mage::helper('rocketweb_search')->__('Direct Search Results Manager');
    $this->_addButtonLabel = Mage::helper('rocketweb_search')->__('Add Direct Search Result');
    parent::__construct();
  }
}