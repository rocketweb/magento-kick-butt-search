<?php

$installer = $this;

$installer->startSetup();

$installer->run("

-- DROP TABLE IF EXISTS {$this->getTable('rw_search_query')};
CREATE TABLE {$this->getTable('rw_search_query')} (
  `query_id` int(11) unsigned NOT NULL auto_increment,
  `search_phrase` varchar(255) NOT NULL default '',
  `product_sku` varchar(255) NOT NULL default '',
  `status` smallint(6) NOT NULL default '0',
  PRIMARY KEY (`query_id`),
  KEY `search_phrase` (`search_phrase`),
  KEY `ix_table1_field1` (`query_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

");

$installer->endSetup(); 