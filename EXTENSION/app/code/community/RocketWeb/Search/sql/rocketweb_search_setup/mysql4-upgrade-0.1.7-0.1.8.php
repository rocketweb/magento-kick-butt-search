<?php
$process = Mage::getSingleton('index/indexer')->getProcessByCode('catalogsearch_fulltext');
$process->changeStatus(Mage_Index_Model_Process::STATUS_REQUIRE_REINDEX);