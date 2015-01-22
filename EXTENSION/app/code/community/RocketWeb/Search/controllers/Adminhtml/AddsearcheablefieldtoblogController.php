<?php
class RocketWeb_Search_Adminhtml_AddsearcheablefieldtoblogController extends Mage_Adminhtml_Controller_Action
{
	public function indexAction() {
		$response = array();
		$response['type'] = '';
		$response['message'] = '';
		
		if(Mage::helper('rocketweb_search')->blogHasIsSearcheableField()) {
			$response['type'] = 'error';
			$response['message'] = $this->__('"Is Searchable" attribute already present');
		}
		else {
			$res = Mage::getSingleton('core/resource');
			$conn = $res->getConnection('core_write');
			if(Mage::helper('rocketweb_search')->isAwBlogModuleEnabled()) {
				try {
					$conn->query("ALTER TABLE  `{$res->getTableName('blog/blog')}` ADD  `is_searcheable` TINYINT( 1 ) UNSIGNED NOT NULL");
					$conn->query("UPDATE {$res->getTableName('blog/blog')} SET is_searcheable=1");
					$response['type'] = 'success';
					$response['message'] = 'Attribute added sucesfully';
					Mage::dispatchEvent('adminhtml_cache_flush_all');
        			Mage::app()->getCacheInstance()->flush();
				}
				catch(Exception $ex) {
					$response['type'] = 'error';
					$response['message'] = $ex->getMessage();
				}
			}
		}
		
		echo json_encode($response);
		exit;
	}
}