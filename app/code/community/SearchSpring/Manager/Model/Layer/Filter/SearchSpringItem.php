<?php

class SearchSpring_Manager_Model_Layer_Filter_SearchSpringItem extends Mage_Catalog_Model_Layer_Filter_Item
{

	/**
	 * Get filter item url
	 *
	 * @return string
	 */
	public function getUrl()
	{
		$value = $this->_getUrlValue();

		$query = array(
			$this->getFilter()->getRequestVar() => $value,
			Mage::getBlockSingleton('page/html_pager')->getPageVarName() => null // exclude current page from urls
		);

		$url = Mage::getUrl('*/*/*', array('_current'=>true, '_use_rewrite'=>true, '_query'=>$query));

		return $url;
	}

	/**
	 * Get url for remove item from filter
	 *
	 * @return string
	 */
	public function getRemoveUrl()
	{
		// Get URL knows if the current value is already selected and will remove it as part of the URL.
		return $this->getUrl();
	}

	/**
	 * Returns true if the current value item is active
	 */
	public function isActive() {
		$currentValue = $this->getFilter()->getCurrentValue();
		$itemValue = $this->getValue();

		return $this->_containsValue($currentValue, $itemValue);
	}

	/**
	 * Get the value to set for the URL if the current item is selected.
	 */
	protected function _getUrlValue() {
		$currentValue = $this->getFilter()->getCurrentValue();
		$itemValue = $this->getValue();

		if($this->_containsValue($currentValue, $itemValue)) {
			return $this->_removeValue($currentValue, $itemValue);
		} else {
			return $this->_addValue($currentValue, $itemValue);
		}
	}

	/**
	 * Return the current value
	 */
	public function getValue() {
		$value = parent::getValue();
		if(isset($value->rangeLow)) {
			$value = 'RANGE:' . $value->rangeLow . ' TO ' . $value->rangeHigh;
		}
		return $value;
	}

	/**
	 * Check if itemValue is contained in the currentValue
	 *
	 * @param $currentValue
	 * @param $itemValue
	 * @return bool
	 */
	protected function _containsValue($currentValue, $itemValue) {
		if(is_null($currentValue)) {
			return false;
		} else if(!is_array($currentValue)) {
			return $currentValue == $itemValue;
		} else {
			return in_array($itemValue, $currentValue);
		}
	}

	/**
	 * Remove itemValue from the currentValue
	 *
	 * @param $currentValue
	 * @param $itemValue
	 * @return mixed
	 */
	protected function _removeValue($currentValue, $itemValue) {
		if(!is_array($currentValue)) {
			return null;
		} else {
			unset($currentValue[array_search($itemValue, $currentValue)]);
			return $currentValue;
		}
	}

	/**
	 * Add itemValue to the currentValue
	 *
	 * @param $currentValue
	 * @param $itemValue
	 * @return mixed
	 */
	protected function _addValue($currentValue, $itemValue) {
		if ($this->getFilter()->getMultipleMode() == 'multiple-intersect' || $this->getFilter()->getMultipleMode() == 'multiple-union') {
			if (is_null($currentValue)) {
				$currentValue = $itemValue;
			} else if (!is_array($currentValue)) {
				$currentValue = array($currentValue, $itemValue);
			} else {
				$currentValue[] = $itemValue;
			}
		} else {
			$currentValue = $itemValue;
		}
		return $currentValue;
	}

}
