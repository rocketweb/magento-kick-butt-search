<?php 
class RocketWeb_Search_Block_Entity_Category_Result_Renderer extends RocketWeb_Search_Block_Entity_Abstract_Renderer {
	public function getCategory() {
		if(!$this->getData('category')->getName()) {
			$this->setData('category',Mage::getModel('catalog/category')->load($this->getData('category')->getId()));
		}
		return $this->getData('category');
	}
	
	public function getTitle() {
		return $this->getCategory()->getName();
	}
	
	public function getDetailUrl() {
		return $this->getCategory()->getUrl();
	}
	
	public function getSnippet() {
		$text = $this->getCategory()->getDescription();
		$text = $this->filterTagsAndBlocks($text);
		return $text;
	}
	
	protected function filterTagsAndBlocks($data) {
		$ret = preg_replace("#\s+#siu", ' ', trim(strip_tags($data)));
		$ret = preg_replace('#\{\{.*\}\}#iu','',$ret);
		$snippetLength = Mage::getStoreConfig('rocketweb_search/category_search/snippet_length');
		if(!$snippetLength) $snippetLength=200;
		$ret = $this->excerpt($ret, Mage::helper('catalogsearch')->getQuery()->getQueryText(),(int) $snippetLength/2);
		$ret = $this->highlight($ret, Mage::helper('catalogsearch')->getQuery()->getQueryText());
		return $ret;
	}
}