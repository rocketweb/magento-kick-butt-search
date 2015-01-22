<?php
class RocketWeb_Search_Model_Observer
{
	
	public function deleteDirectSearchResultSku($observer) {
		 $product = $observer->getEvent()->getProduct();
		 
		 $table = Mage::getSingleton('core/resource')->getTableName('rocketweb_search/query_sku');
		 
		 $conn = Mage::getSingleton('core/resource')->getConnection('core_write');
		 $query = "DELETE FROM `{$table}` WHERE ".$conn->quoteInto('entity_id = ? AND entity_type=\'product\'', $product->getId());
		 $conn->query($query);
	}
	
	public function markSearchIndexForReindex($observer) {
		$process = Mage::getSingleton('index/indexer')->getProcessByCode('catalogsearch_fulltext');
		$process->changeStatus(Mage_Index_Model_Process::STATUS_REQUIRE_REINDEX);
	}
	
	public function onCmsMainTabPrepareForm($observer) {
		$form = $observer->getEvent()->getForm();
		$baseFieldset = $form->getElement('base_fieldset');
		
		$baseFieldset->addField('is_searcheable', 'select', array(
			'label'  => Mage::helper('rocketweb_search')->__('Is Searchable?'),
			'title'  => Mage::helper('rocketweb_search')->__('Is Searchable?'),
			'name'      => 'is_searcheable',
			'values'    => Mage::getSingleton('adminhtml/system_config_source_yesno')->toOptionArray()
		));
		
		return $this;
	}
	
	public function triggerCmsPageIndexerEventsOnSave($observer) {
		$page = $observer->getEvent()->getDataObject();
		
		Mage::getSingleton('index/indexer')->processEntityAction(
            $page, Mage::getModel('rocketweb_search/entities_cms')->getEntityId(), Mage_Index_Model_Event::TYPE_SAVE
        );
		
		return $this;
	}
	
	public function triggerCmsPageIndexerEventsOnDelete($observer) {
		$page = $observer->getEvent()->getDataObject();
		
		Mage::getSingleton('index/indexer')->processEntityAction(
			$page, Mage::getModel('rocketweb_search/entities_cms')->getEntityId(), Mage_Index_Model_Event::TYPE_DELETE
		);
		
		return $this;
	}
	
	public function triggerBlogPageIndexerEventsOnSave($observer) {
		$model = $observer->getEvent()->getObject();
		
		if(Mage::getModel('rocketweb_search/entities_blog')->isEnabled()) {
			if($model instanceof AW_Blog_Model_Post) {
				Mage::getSingleton('index/indexer')->processEntityAction(
					$model, Mage::getModel('rocketweb_search/entities_blog')->getEntityId(), Mage_Index_Model_Event::TYPE_SAVE
				);
			}
		}
		
		return $this;
		
	}
	
	public function triggerBlogPageIndexerEventsOnDelete($observer) {
		$model = $observer->getEvent()->getObject();
		
		if(Mage::getModel('rocketweb_search/entities_blog')->isEnabled()) {
			if($model instanceof AW_Blog_Model_Blog) {
				Mage::getSingleton('index/indexer')->processEntityAction(
				$model, Mage::getModel('rocketweb_search/entities_blog')->getEntityId(), Mage_Index_Model_Event::TYPE_DELETE
				);
			}
		}
		
		return $this;
	}
	
	public function triggerCategoryIndexerEventsOnSave($observer) {
		$category = $observer->getEvent()->getDataObject();
		
		Mage::getSingleton('index/indexer')->processEntityAction(
			$category, Mage::getModel('rocketweb_search/entities_category')->getEntityId(), Mage_Index_Model_Event::TYPE_SAVE
		);
		
		return $this;
	}
	
	public function triggerCategoryIndexerEventsOnDelete($observer) {
		$category = $observer->getEvent()->getDataObject();
	
		Mage::getSingleton('index/indexer')->processEntityAction(
			$category, Mage::getModel('rocketweb_search/entities_category')->getEntityId(), Mage_Index_Model_Event::TYPE_DELETE
		);
	
		return $this;
	}
}
