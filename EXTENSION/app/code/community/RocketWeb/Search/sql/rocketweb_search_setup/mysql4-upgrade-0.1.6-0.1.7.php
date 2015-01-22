<?php
$installer = $this;

$installer->startSetup();

$installer->getConnection()->addColumn($installer->getTable('catalogsearch_fulltext'), 'product_boost', "INT UNSIGNED NOT NULL DEFAULT  '0'");

$installer->endSetup();