<?php

class SearchSpring_Manager_Model_Layer_Filter_SearchSpring extends Mage_Catalog_Model_Layer_Filter_Abstract
{

	protected $_searchSpringFacet;
	protected $_currentValue;

	protected $_itemModelName = 'searchspring_manager/layer_filter_searchSpringItem';

	/**
	 * Apply attribute option to State
	 *
	 * @param   Zend_Controller_Request_Abstract $request
	 * @param   Varien_Object $filterBlock
	 * @return  Mage_Catalog_Model_Layer_Filter_Attribute
	 */
	public function apply(Zend_Controller_Request_Abstract $request, $filterBlock)
	{
		return $this;
	}

	/**
	 * Returns the multiple mode of the SearchSpring Facet
	 */
	public function getMultipleMode() {

		return isset($this->_searchSpringFacet->multiple)?$this->_searchSpringFacet->multiple:'single';
	}

	/**
	 * Set searchspring facet data
	 *
	 * @param $facet
	 * @return $this
	 */
	public function setSearchSpringFacet($facet) {
		$this->_searchSpringFacet = $facet;
		$this->_requestVar = 'filter_' . $facet->field;
		return $this;
	}

	/**
	 * Initialize filter items
	 *
	 * @return  Mage_Catalog_Model_Layer_Filter_Abstract
	 */
	protected function _initItems()
	{
		$data = $this->_getItemsData();
		$items = array();
		foreach ($data as $itemData) {
			$items[] = $this->_createItem(
				$itemData['label'],
				$itemData['value'],
				$itemData['count']
			);
		}
		$this->_items = $items;


		return $this;
	}

	/**
	 * Create filter item object
	 *
	 * @param   string $label
	 * @param   mixed $value
	 * @param   int $count
	 * @return  Mage_Catalog_Model_Layer_Filter_Item
	 */
	protected function _createItem($label, $value, $count=0)
	{
		$item = Mage::getModel($this->_itemModelName)
		->setFilter($this)
		->setLabel($label)
		->setValue($value)
		->setCount($count);

		return $item;
	}

	public function getCurrentValue() {
		if(!isset($this->_currentValue)) {
			$this->_currentValue = Mage::app()->getRequest()->getParam($this->getRequestVar());
		}
		return $this->_currentValue;
	}

	/**
	 * Get data array for building attribute filter items
	 *
	 * @return array
	 */
	protected function _getItemsData()
	{
		$items = array();

		foreach($this->_searchSpringFacet->values as $value) {
			$item = array(
				'label' => $value->label,
				'count' => $value->count,
				'type' => $value->type
			);

			if($value->type == 'value') {
				$item['value'] = $value->value;
			} else if($value->type == 'range') {
				$value = 'RANGE:' . $value->low . ' TO ' . $value->high;
				/*	'low' => $value->low,
					'high' => $value->high
				);*/
				$item['value'] = $value;
			}
			$items[] = $item;
		}

		return $items;
	}

	/**
	 * Return the name of the facet
	 *
	 * @return string
	 */
	public function getName() {
		return $this->_searchSpringFacet->label;
	}

}
