<?php 
class RocketWeb_Search_Block_Entity_Cms_Result_Renderer extends RocketWeb_Search_Block_Entity_Abstract_Renderer {
	public function getTitle() {
		return $this->getCmsPage()->getTitle();
	}
	
	public function getDetailUrl() {
		return Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB).$this->getCmsPage()->getIdentifier();
	}
	
	public function getSnippet() {
		$text = $this->getCmsPage()->getContent();
		$text = $this->filterTagsAndBlocks($text);
		return $text;
	}
	
	protected function filterTagsAndBlocks($data) {
		$ret = preg_replace("#\s+#siu", ' ', trim(strip_tags($data)));
		$ret = preg_replace('#\{\{.*\}\}#iu','',$ret);
		$snippetLength = Mage::getStoreConfig('rocketweb_search/cms_search/snippet_length');
		if(!$snippetLength) $snippetLength=200;
		$ret = $this->excerpt($ret, Mage::helper('catalogsearch')->getQuery()->getQueryText(),(int) $snippetLength/2);
		$ret = $this->highlight($ret, Mage::helper('catalogsearch')->getQuery()->getQueryText());
		return $ret;
	}
	
	
}