<?php

class RocketWeb_Search_Block_Adminhtml_Directsearch_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();
                 
        $this->_objectId = 'id';
        $this->_blockGroup = 'rocketweb_search';
        $this->_controller = 'adminhtml_directsearch';
        
        $this->_updateButton('save', 'label', Mage::helper('rocketweb_search')->__('Save Direct Search Result'));
        $this->_updateButton('delete', 'label', Mage::helper('rocketweb_search')->__('Delete Direct Search Result'));
		
        $this->_addButton('saveandcontinue', array(
            'label'     => Mage::helper('adminhtml')->__('Save And Continue Edit'),
            'onclick'   => 'saveAndContinueEdit()',
            'class'     => 'save',
        ), -100);

        $this->_formScripts[] = "
            function toggleEditor() {
                if (tinyMCE.getInstanceById('directquery_content') == null) {
                    tinyMCE.execCommand('mceAddControl', false, 'directquery_content');
                } else {
                    tinyMCE.execCommand('mceRemoveControl', false, 'directquery_content');
                }
            }

            function saveAndContinueEdit(){
                editForm.submit($('edit_form').action+'back/edit/');
            }
        ";
    }

    public function getHeaderText()
    {
        if( Mage::registry('query_data') && Mage::registry('query_data')->getId() ) {
            return Mage::helper('rocketweb_search')->__("Edit Direct Search Result '%s'", $this->htmlEscape(Mage::registry('query_data')->getSearchPhrase()));
        } else {
            return Mage::helper('rocketweb_search')->__('Add Direct Search Result');
        }
    }
}