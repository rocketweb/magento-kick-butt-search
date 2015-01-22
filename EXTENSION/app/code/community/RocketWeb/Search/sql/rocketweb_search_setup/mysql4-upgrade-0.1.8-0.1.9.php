<?php
$installer = $this;
$installer->startSetup();

$installer->getConnection()->addColumn($installer->getTable('rw_search_query_sku'), 'order', 'INT NOT NULL DEFAULT 0');
$installer->getConnection()->addKey($installer->getTable('rw_search_query_sku'), 'order', 'order');

$installer->endSetup();