<?php
$installer = $this;
$installer->startSetup();

$installer->run("
	CREATE TABLE IF NOT EXISTS `{$installer->getTable('rw_fulltext')}` (
  `product_id` int(10) unsigned NOT NULL COMMENT 'Product ID',
  `entity_type` varchar(20) NOT NULL,
  `store_id` smallint(5) unsigned NOT NULL COMMENT 'Store ID',
  `data_index` longtext COMMENT 'Data index',
  `fulltext_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Entity ID',
  `name_index` text,
  `sku_index` text,
  `product_boost` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`fulltext_id`),
  UNIQUE KEY `product_id` (`product_id`,`entity_type`,`store_id`),
  FULLTEXT KEY `FTI_CATALOGSEARCH_FULLTEXT_DATA_INDEX` (`data_index`),
  FULLTEXT KEY `name_index` (`name_index`),
  FULLTEXT KEY `sku_index` (`sku_index`),
  FULLTEXT KEY `name_data_sku_index` (`name_index`,`sku_index`,`data_index`)
) ENGINE=MyISAM   DEFAULT CHARSET=utf8  ;		
");


$installer->endSetup();