<?php

$installer = $this;

$installer->startSetup();

$stores = Mage::getModel('core/store')->getCollection();
$rootCategories = array();
foreach($stores as $store) {
	$rootCategories[]=$store->getRootCategoryId(); 
}

$categories = Mage::getModel('catalog/category')->getCollection();
$categories->addAttributeToFilter('entity_id',array('nin'=>array_unique($rootCategories)));
foreach($categories as $category) {
	$category = Mage::getModel('catalog/category')->load($category->getEntityId());
	$category->setIsSearcheable(1);
	$category->save();
}

$installer->endSetup();