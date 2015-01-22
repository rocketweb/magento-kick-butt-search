<?php

$installer = $this;

$installer->startSetup();

$installer->run("
	CREATE TABLE IF NOT EXISTS `{$installer->getTable('rw_search_result')}` (
	  `query_id` int(10) unsigned NOT NULL COMMENT 'Query ID',
	  `product_id` int(10) unsigned NOT NULL COMMENT 'Product ID',
	  `entity_type` varchar(20) NOT NULL,
	  `relevance` decimal(20,4) NOT NULL DEFAULT '0.0000' COMMENT 'Relevance',
	  PRIMARY KEY (`query_id`,`product_id`,`entity_type`),
	  KEY `IDX_CATALOGSEARCH_RESULT_QUERY_ID` (`query_id`),
	  KEY `IDX_CATALOGSEARCH_RESULT_PRODUCT_ID` (`product_id`)
	) ENGINE=InnoDB DEFAULT CHARSET=utf8
");

$installer->run("
	ALTER TABLE `{$installer->getTable('rw_search_result')}`
  		ADD CONSTRAINT `FK_RW_SEARCH_RESULT_QUERY_ID_CATALOGSEARCH_QUERY_QUERY_ID` FOREIGN KEY (`query_id`) REFERENCES `{$installer->getTable('catalogsearch_query')}` (`query_id`) ON DELETE CASCADE ON UPDATE CASCADE;
");

$installer->endSetup();