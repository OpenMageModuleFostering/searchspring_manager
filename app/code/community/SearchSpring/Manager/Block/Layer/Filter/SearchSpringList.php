<?php

class SearchSpring_Manager_Block_Layer_Filter_SearchSpringList extends Mage_Catalog_Block_Layer_Filter_Abstract
{

	protected $_filterModelName = 'searchspring_manager/layer_filter_searchSpring';
	protected $_searchSpringFacet;

	/**
	 * Set the SearchSpring facet response
	 */
	public function setSearchSpringFacet($facet) {
		$this->_searchSpringFacet = $facet;
		return $this;
	}

	protected function _prepareFilter()
	{
		parent::_prepareFilter();
		$this->_filter->setSearchSpringFacet($this->_searchSpringFacet);
		return $this;
	}

	/**
	 * Return the filter model
	 *
	 * @return Mage_Catalog_Model_Layer_Filter_Attribute
	 */
	public function getFilter() {
		return $this->_filter;
	}

	/**
	 * Return whether or not to display the filter
	 *
	 * @return bool
	 */
	public function showFilter() {
		return $this->getItemsCount() > 0;
	}
}
