<?php
class RocketWeb_Search_Block_Adminhtml_Directsearch_Edit_Tab_Sku extends Mage_Adminhtml_Block_Widget
    implements Varien_Data_Form_Element_Renderer_Interface {
	
	/**
	 * Initialize block
	 */
	public function __construct()
	{
		$this->setTemplate('rocketweb_search/directsearchresult/edit/product_sku.phtml');
	}
	
	/**
	 * Form element instance
	 *
	 * @var Varien_Data_Form_Element_Abstract
	 */
	protected $_element;
	
	/**
	 * Render HTML
	 *
	 * @param Varien_Data_Form_Element_Abstract $element
	 * @return string
	 */
	public function render(Varien_Data_Form_Element_Abstract $element)
	{
		$this->setElement($element);
		return $this->toHtml();
	}
	
	/**
	 * Set form element instance
	 *
	 * @param Varien_Data_Form_Element_Abstract $element
	 * @return Mage_Adminhtml_Block_Catalog_Product_Edit_Tab_Price_Group_Abstract
	 */
	public function setElement(Varien_Data_Form_Element_Abstract $element)
	{
		$this->_element = $element;
		return $this;
	}
	
	/**
	 * Retrieve form element instance
	 *
	 * @return Varien_Data_Form_Element_Abstract
	 */
	public function getElement()
	{
		return $this->_element;
	}
	
	/**
	 * Retrieve 'add group price item' button HTML
	 *
	 * @return string
	 */
	public function getAddButtonHtml()
	{
		return $this->getChildHtml('add_button');
	}
	
	/**
	 * Retrieve Group Price entity attribute
	 *
	 * @return Mage_Catalog_Model_Resource_Eav_Attribute
	 */
	public function getAttribute()
	{
		return $this->getElement()->getEntityAttribute();
	}
	
	/**
	 * Prepare global layout
	 * Add "Add tier" button to layout
	 *
	 * @return Mage_Adminhtml_Block_Catalog_Product_Edit_Tab_Price_Tier
	 */
	protected function _prepareLayout()
	{
		$button = $this->getLayout()->createBlock('adminhtml/widget_button')
		->setData(array(
				'label' => Mage::helper('catalog')->__('Add Product SKU'),
				'onclick' => 'return productSkuControl.addItem()',
				'class' => 'add'
		));
		$button->setName('add_product_sku_button');
	
		$this->setChild('add_button', $button);
		return parent::_prepareLayout();
	}
	
	
	/**
	 * Prepare group price values
	 *
	 * @return array
	 */
	public function getValues()
	{
		$values = array();
		$data = $this->getElement()->getValue();
	
		if (is_array($data)) {
			$values = $this->_sortValues($data);
		}
	
	
		return $values;
	}
	
	/**
	 * Sort values
	 *
	 * @param array $data
	 * @return array
	 */
	protected function _sortValues($data)
    {
        usort($data, array($this, '_sortProductSkus'));
        return $data;
    }
    
    protected function _sortProductSkus($a, $b)
    {
    	if($a['order'] == $b['order']) return 0;
    	if($a['order'] < $b['order']) return -1;
    	else return 1;	
    
    }
}