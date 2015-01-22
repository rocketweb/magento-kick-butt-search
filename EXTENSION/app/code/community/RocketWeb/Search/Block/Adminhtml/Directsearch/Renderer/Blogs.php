<?php
class RocketWeb_Search_Block_Adminhtml_Directsearch_Renderer_Blogs extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {
	public function render(Varien_Object $row) {
		$queryItem = Mage::getModel('rocketweb_search/query')->load($row->getQueryId());
		$blogs = $queryItem->getBlogs();
		if(is_array($blogs) && count($blogs)) {
			$blogCol = Mage::getModel('blog/post')->getCollection();
			$blogCol->addFieldToFilter('post_id',array('in'=>$blogs));
			$ret = '';
			foreach($blogCol as $blog) {
				$ret.=$blog->getTitle().', ';
			}
			$ret = preg_replace('/, $/','',$ret);
			return $ret;
		}
		else {
			return '-';
		}
	}
}