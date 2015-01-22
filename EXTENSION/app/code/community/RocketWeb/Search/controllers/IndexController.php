<?php
class RocketWeb_Search_IndexController extends Mage_Core_Controller_Front_Action {
	public function indexAction() {
		$query_id = (int) Mage::app()->getRequest()->getParam('query_id');
		$query = Mage::getModel('rocketweb_search/query')->load($query_id);
				
		if(count($query->getProducts()) != 0) {
			Mage::register('direct_search_result_products',$query->getProducts());
		}
		
		$this->loadLayout();
		
		if(count($query->getProducts()) == 0) {
			$this->getLayout()->getBlock('content')->unsetChild('search.result');
		}
		
		if($query->getCms()) {
			Mage::register('direct_search_result_cms',$query->getCms());
			
			$cmsBlock = $this->getLayout()->createBlock('rocketweb_search/entity_cms_directresult','search.cms.result');
			$cmsBlock->setTemplate('rocketweb_search/cms_result.phtml');
			$cmsBlock->setRenderer('rocketweb_search/entity_cms_result_renderer','rocketweb_search/cms_result_renderer.phtml');
			$this->getLayout()->getBlock('content')->append($cmsBlock);
		}
		if($query->getBlogs()) {
			Mage::register('direct_search_result_blogs',$query->getBlogs());
			
			$blogsBlock = $this->getLayout()->createBlock('rocketweb_search/entity_blog_directresult','search.blog.result');
			$blogsBlock->setTemplate('rocketweb_search/blog_result.phtml');
			$blogsBlock->setRenderer('rocketweb_search/entity_blog_result_renderer','rocketweb_search/blog_result_renderer.phtml');
			$this->getLayout()->getBlock('content')->append($blogsBlock);
		}
		if($query->getCategories()) {
			Mage::register('direct_search_result_categories',$query->getCategories());
		
			$categoryBlock = $this->getLayout()->createBlock('rocketweb_search/entity_category_directresult','search.category.result');
			$categoryBlock->setTemplate('rocketweb_search/category_result.phtml');
			$categoryBlock->setRenderer('rocketweb_search/entity_category_result_renderer','rocketweb_search/category_result_renderer.phtml');
			$this->getLayout()->getBlock('content')->append($categoryBlock);
		}
		
		
		$this->renderLayout();
	}
}
