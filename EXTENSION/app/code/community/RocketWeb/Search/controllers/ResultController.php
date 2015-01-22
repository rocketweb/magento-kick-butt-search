<?php
require_once 'Mage/CatalogSearch/controllers/ResultController.php';

class RocketWeb_Search_ResultController extends Mage_CatalogSearch_ResultController
{
    /**
     * Display search result
     */
    public function indexAction()
    {
        $query = Mage::helper('catalogsearch')->getQuery();
        /* @var $query Mage_CatalogSearch_Model_Query */
        $query->setStoreId(Mage::app()->getStore()->getId());

        if($query->getQueryText()) {
        	$queryCollection = Mage::getModel('rocketweb_search/query')->getCollection();
			$queryCollection->addFieldToFilter('search_phrase',$query->getQueryText());
			$queryCollection->addFieldToFilter('status',1);
			$queryCollection->addStoreFilter(Mage::app()->getStore());
			if(count($queryCollection)) {
				$data = $queryCollection->getFirstItem();
				$data = Mage::getModel('rocketweb_search/query')->load($data->getQueryId());
				$resultCount = count($data->getProducts()) + count($data->getCms()) + count($data->getBlogs()) + count($data->getCategories());
				
				$redirectUrl = '';
				if($resultCount == 1) {
					$redirectUrl = $this->_getDirectRedirectUrl($data);
					
				}
				if($redirectUrl) {
					$this->_redirectUrl($redirectUrl);
				}
				else {
					$this->_redirect('search/index/index', array('query_id' => $data->getQueryId(), 'q' => $query->getQueryText()));
				}
				
			}
			else {
				return parent::indexAction();
			}
        }
    }
    
    protected function _getDirectRedirectUrl($data) {
    	if(count($data->getProducts())) {
    		$res = $data->getProducts();
    		$res = array_shift($res);
    		$item = Mage::getModel('catalog/product')->load($res['entity_id']);
    		if($item && $item->getId()) {
    			return  $item->getUrlModel()->getUrl($item, array('_ignore_category'=>true));
    		}
    	}
    	
    	if(count($data->getCms())) {
    		$res = $data->getCms();
    		$res = array_shift($res);
    		$item = Mage::getModel('cms/page')->load($res['entity_id']);
    		if($item && $item->getPageId()) {
    			return Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB).$item->getIdentifier();
    		}
    	}
    	
    	if(count($data->getCategories())) {
    		$res = $data->getCategories();
    		$res = array_shift($res);
    		$item = Mage::getModel('catalog/category')->load($res['entity_id']);
    		if($item && $item->getId()) {
    			return $item->getUrl();
    		}
    	}
    	
    	if(count($data->getBlogs()) && Mage::helper('rocketweb_search')->isAwBlogModuleEnabled()) {
    		$res = $data->getBlogs();
    		$res = array_shift($res);
    		$item = Mage::getModel('blog/post')->load($res['entity_id'],'post_id');
    		
    		$route = Mage::getStoreConfig('blog/blog/route');
    		if(!$route) $route = defined('AW_Blog_Helper_Data::DEFAULT_ROOT')?AW_Blog_Helper_Data::DEFAULT_ROOT:'blog';
    		$route = Mage::getUrl($route);
    		if($item && $item->getPostId()) {
    			return $route.$item->getIdentifier();
    		}
    	}
    	
    	return '';
    }
}
