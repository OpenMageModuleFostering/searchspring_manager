<?php

class SearchSpring_Manager_Block_Layer_Filter_SearchSpringCategory extends SearchSpring_Manager_Block_Layer_Filter_SearchSpringList
{

	protected $_filterModelName = 'searchspring_manager/layer_filter_searchSpringCategory';
	protected $_searchSpringFacet;

	/**
	 * Return the name of the facet
	 *
	 * @return string
	 */
	public function getName() {
		return $this->__('Category');
	}

	public function showFilter() {
		return true;
	}

	/**
	 * Passthrough Parent Categories - from filter model
	 *
	 * @return array
	 */
	public function getParents() {
		if (!$this->hasData('parent_categories')) {
			$parents = $this->_filter->getParents();
			if (!is_array($parents))
				$parents = array();
			$this->setData('parent_categories', $parents);
		}
		return $this->getData('parent_categories');
	}

}
