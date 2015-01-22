<?php
$installer = $this;
$installer->startSetup();

$installer->installEntities();

$attributeId = Mage::getResourceModel('eav/entity_attribute')->getIdByCode('catalog_product','search_boost');
if ($attributeId) {
	$attribute = Mage::getModel('catalog/resource_eav_attribute')->load($attributeId);
	$attribute->setIsSearchable(1)->setIsRequired(0)->setIsConfigurable(0)->save();
}

$installer->endSetup();