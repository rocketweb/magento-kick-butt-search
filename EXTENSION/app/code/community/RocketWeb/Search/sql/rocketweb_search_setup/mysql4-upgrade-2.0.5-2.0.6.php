<?php 
$installer = $this;
$installer->startSetup();

$installer->run("
	ALTER TABLE  `{$this->getTable('rw_search_query_sku')}` DROP `sku`;
");

$installer->endSetup();