<?php

class RocketWeb_Search_Model_Query extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('rocketweb_search/query');
    }
}