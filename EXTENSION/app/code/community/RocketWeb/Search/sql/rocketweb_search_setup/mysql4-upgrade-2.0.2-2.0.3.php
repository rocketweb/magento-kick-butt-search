<?php

$installer = $this;

$installer->startSetup();

if(Mage::helper('rocketweb_search')->isAwBlogModuleEnabled()) {
	$installer->run("ALTER TABLE  `{$installer->getTable('blog/blog')}` ADD  `is_searcheable` TINYINT( 1 ) UNSIGNED NOT NULL");
	$installer->run("UPDATE {$installer->getTable('blog/blog')} SET is_searcheable=1");
}


$installer->endSetup();