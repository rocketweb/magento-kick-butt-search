<?php

$installer = $this;

$installer->startSetup();

$installer->run("
	UPDATE `{$installer->getTable('cms/page')}` set is_searcheable=1
");

//exclude 404 & no cookies cms pages from search by default
$stores = Mage::getModel('core/store')->getCollection();
$excludedPages = array();
foreach($stores as $store) {
	$store = Mage::getModel('core/store')->load($store->getStoreId());
	$excludedPages[]=Mage::getStoreConfig('web/default/cms_no_route',$store);
	$excludedPages[]=Mage::getStoreConfig('web/default/cms_no_cookies',$store);
}
$excludedPages = array_unique($excludedPages);


$installer->run("
	UPDATE `{$installer->getTable('cms/page')}` set is_searcheable=0 WHERE identifier IN ('".implode('\',\'',$excludedPages)."')
");


$installer->endSetup();