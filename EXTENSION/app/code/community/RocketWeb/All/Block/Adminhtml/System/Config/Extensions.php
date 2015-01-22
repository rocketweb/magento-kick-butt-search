<?php

/**
 * RocketWeb
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * @category   RocketWeb
 * @package    RocketWeb_All
 * @copyright  Copyright (c) 2012 RocketWeb (http://rocketweb.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @author     RocketWeb
 */

/**
 * Config Extensions
 */
class RocketWeb_All_Block_Adminhtml_System_Config_Extensions extends Mage_Adminhtml_Block_System_Config_Form_Field {

    protected function _prepareLayout() {

        parent::_prepareLayout();
        if (!$this->getTemplate()) {
            $this->setTemplate('rocketweb_all/adminhtml/installed_extensions.phtml');
        }
        return $this;
    }

    /**
     * Unset some non-related element parameters
     *
     * @param Varien_Data_Form_Element_Abstract $element
     * @return string
     */
    public function render(Varien_Data_Form_Element_Abstract $element) {

        $this->addData(array('rows'	=> Mage::helper('rocketweb_all')->getExtensions()));
        $this->setScriptPath(Mage::getBaseDir('design'));
        return $this->fetchView($this->getTemplateFile());
    }

    /**
     * @param Varien_Data_Form_Element_Abstract $element
     * @return string
     */
    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element) {


        return $this->_toHtml();
    }
}