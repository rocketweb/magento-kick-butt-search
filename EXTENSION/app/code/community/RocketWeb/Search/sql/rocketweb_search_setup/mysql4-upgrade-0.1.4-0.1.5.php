<?php

$installer = $this;

$installer->startSetup();

$installer->getConnection()->addColumn($installer->getTable('catalogsearch_fulltext'), 'name_index', 'text NULL');
$installer->getConnection()->addKey($installer->getTable('catalogsearch_fulltext'), 'name_index', 'name_index', 'fulltext');

$installer->getConnection()->addColumn($installer->getTable('catalogsearch_fulltext'), 'sku_index', 'text NULL');
$installer->getConnection()->addKey($installer->getTable('catalogsearch_fulltext'), 'sku_index', 'sku_index', 'fulltext');

$installer->getConnection()->addKey($installer->getTable('catalogsearch_fulltext'), 'name_data_sku_index', array('name_index','sku_index','data_index'), 'fulltext');

$installer->endSetup();