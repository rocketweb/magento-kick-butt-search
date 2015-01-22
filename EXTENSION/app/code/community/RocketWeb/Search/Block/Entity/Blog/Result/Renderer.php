<?php 
class RocketWeb_Search_Block_Entity_Blog_Result_Renderer extends RocketWeb_Search_Block_Entity_Abstract_Renderer {
	public function getTitle() {
		return $this->getPost()->getTitle();
	}
	
	public function getDetailUrl() {
		$route = Mage::getStoreConfig('blog/blog/route');
		if(!$route) $route = defined('AW_Blog_Helper_Data::DEFAULT_ROOT')?AW_Blog_Helper_Data::DEFAULT_ROOT:'blog';
		$route = Mage::getUrl($route);
		return $route.$this->getPost()->getIdentifier();
	}
	
	public function getSnippet() {
		$text = $this->getPost()->getPostContent();
		$text = $this->filterTagsAndBlocks($text);
		return $text;
	}
	
	protected function filterTagsAndBlocks($data) {
		$ret = preg_replace("#\s+#siu", ' ', trim(strip_tags($data)));
		$ret = preg_replace('#\{\{.*\}\}#iu','',$ret);
		$snippetLength = Mage::getStoreConfig('rocketweb_search/aw_blog_search/snippet_length');
		if(!$snippetLength) $snippetLength=200;
		$ret = $this->excerpt($ret, Mage::helper('catalogsearch')->getQuery()->getQueryText(),(int) $snippetLength/2);
		$ret = $this->highlight($ret, Mage::helper('catalogsearch')->getQuery()->getQueryText());
		return $ret;
	}
	
	
}