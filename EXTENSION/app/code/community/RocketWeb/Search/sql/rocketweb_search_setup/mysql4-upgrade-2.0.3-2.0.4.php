<?php 
$installer = $this;
$installer->startSetup();

$entityTypeId     = $installer->getEntityTypeId('catalog_category');
$attributeSetId   = $installer->getDefaultAttributeSetId($entityTypeId);
$attributeGroupId = $installer->getDefaultAttributeGroupId($entityTypeId, $attributeSetId);

$installer->addAttribute('catalog_category', 'is_searcheable',  array(
		'type'                       => 'int',
		'label'                      => 'Is Searchable?',
		'input'                      => 'select',
		'source'                     => 'eav/entity_attribute_source_boolean',
		'default'                    => '1',
		'sort_order'                 => 15,
		'global'                     => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
		'group'                      => 'General',
));
$installer->addAttributeToGroup(
		$entityTypeId,
		$attributeSetId,
		$attributeGroupId,
		'is_searcheable',
		'15'                
);
$installer->endSetup();