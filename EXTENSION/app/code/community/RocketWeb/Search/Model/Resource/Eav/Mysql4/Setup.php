<?php
class RocketWeb_Search_Model_Resource_Eav_Mysql4_Setup extends Mage_Eav_Model_Entity_Setup {
	public function getDefaultEntities()
	{
		
		return array(
				'catalog_product' => array(
					'entity_model'      => 'catalog/product',
					'attribute_model'   => 'catalog/resource_eav_attribute',
					'table'             => 'catalog/product',
					'additional_attribute_table' => 'catalog/eav_attribute',
					'entity_attribute_collection' => 'catalog/product_attribute_collection',
					'attributes'        => array(
						'search_boost' => array(
							'type'              => 'int',
							'backend'           => '',
							'group'             => 'General',
							'sort_order'        => 500,
							'label'             => 'Search Boost',
							'input'             => 'text',
							'global'            => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
							'user_defined'      => '1',
							'default'           => '0',
							'searchable'        => '1',
							'apply_to'          => 'simple,configurable,bundle,grouped',
							'requried'          => '0',
							'configurable'      => '0'
						)
					)
				)
		);
	}
}