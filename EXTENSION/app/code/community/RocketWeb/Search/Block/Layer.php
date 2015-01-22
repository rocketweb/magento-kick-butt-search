<?php
class RocketWeb_Search_Block_Layer extends Mage_CatalogSearch_Block_Layer {
	public function getLayer()
	{
		return Mage::getSingleton('rocketweb_search/directsearchlayer');
	}
}