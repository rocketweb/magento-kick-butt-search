<?php

$installer = $this;

$installer->startSetup();

$installer->run("
	ALTER TABLE `{$installer->getTable('cms/page')}`
		 ADD COLUMN `is_searcheable` TINYINT(2) 
");

$installer->endSetup();