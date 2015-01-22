<?php 
$installer = $this;

$installer->startSetup();

$installer->run("
	ALTER TABLE  `{$this->getTable('rw_search_query_sku')}` ADD  `entity_id` INT UNSIGNED NOT NULL AFTER  `query_id` ,
	ADD  `entity_type` VARCHAR( 20 ) NOT NULL AFTER  `entity_id`
");

$conn = Mage::getSingleton('core/resource')->getConnection('core_write');
$sql = "SELECT * FROM {$this->getTable('rw_search_query_sku')}";

foreach ($conn->fetchAll($sql) as $arr_row) {
	$product = Mage::getModel('catalog/product')->loadByAttribute('sku',$arr_row['sku']);	
	if($product && $product->getName()) {
		$updateSql = "UPDATE {$this->getTable('rw_search_query_sku')} SET entity_type='product', entity_id={$product->getId()} 
						WHERE query_id={$arr_row['query_id']} AND sku=".$conn->quote($arr_row['sku'])."";
		$conn->query($updateSql);
	}
}
$installer->run("
	ALTER TABLE  `{$this->getTable('rw_search_query_sku')}` DROP PRIMARY KEY ,
	ADD PRIMARY KEY (  `query_id` ,  `entity_id` ,  `entity_type` )
");

$installer->endSetup();