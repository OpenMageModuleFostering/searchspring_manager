<?php

class SearchSpring_Manager_Block_Layer_View extends Mage_Catalog_Block_Layer_View {

	protected $_defaultToolbarBlock = 'catalog/product_list_toolbar';

	/**
	 * Internal constructor
	 */
	protected function _construct()
	{
		parent::_construct();
		if ($this->isSearchSpringEnabled()) {
			// Register our version of the layer model for the benifit of the product_list block
			Mage::register('current_layer', $this->getLayer(), true);
		}
	}

	/**
	 * Apply filters from a request to a collection
	 *
	 * TODO - When we start to have more layer filter
	 * types, we'll want to move this logic into the
	 * apply functions of each filter model.
	 *
	 * @param Zend_Controller_Request_Abstract $request
	 * @param $collection
	 */
	protected function _applyFilters(Zend_Controller_Request_Abstract $request, $collection) {
		$params = $request->getParams();
		foreach($params as $param => $values) {
			if(strpos($param, 'filter_') === 0) {

				// convert non-arrays into arrays
				if(!is_array($values)) {
					$values = array($values);
				}

				$field = substr($param, 7);

				foreach($values as $value) {
					if(strpos($value, 'RANGE:') === 0) {
						list($low, $high) = explode(' TO ', substr($value, 6));
						$collection->addFilter($field . '.low', $low);
						$collection->addFilter($field . '.high', $high);
					} else {
						$collection->addFilter($field, $value);
					}
				}
			}
		}
	}

	/**
	 * Prepare child blocks
	 *
	 * @return Mage_Catalog_Block_Layer_View
	 */
	protected function _prepareLayout()
	{
		if ($this->isSearchSpringEnabled()) {
			$head = Mage::app()->getLayout()->getBlock('head');
			$head->addItem('skin_css', 'searchspring/css/styles.css');

			// Load toolbar to apply pagination/sorting to collection
			$toolbarBlock = $this->getLayout()->createBlock($this->_defaultToolbarBlock, microtime());
			$collection = $this->getLayer()->getProductCollection();
			$toolbarBlock->setCollection($collection);

			$this->_applyFilters($this->getRequest(), $collection);

			$stateBlock = $this->getLayout()->createBlock($this->_stateBlockName, 'ss_layer_view_state')
				->setLayer($this->getLayer());

			$stateBlock->setTemplate('searchspring_manager/layer/state.phtml');

			$this->setChild('layer_state', $stateBlock);

			$filterableAttributes = $this->_getFilterableAttributes();

			foreach ($filterableAttributes as $attribute) {
				if ($attribute->field == 'category_ids') {
					$blockType = 'searchspring_manager/layer_filter_searchSpringCategory';
					$template = 'searchspring_manager/layer/category_filter.phtml';
				} else {
					$blockType = 'searchspring_manager/layer_filter_searchSpringList';
					$template = 'searchspring_manager/layer/filter.phtml';
				}

				$blocks[$attribute->field] = $this->getLayout()->createBlock($blockType)
					->setLayer($this->getLayer())
					->setSearchSpringFacet($attribute)
					->init()
					->setTemplate($template);

				$this->setChild($attribute->field . '_filter', $blocks[$attribute->field]);
			}


			foreach($collection->getFilterSummary() as $summaryFilter) {
				// Skip Category
				if($summaryFilter->field == 'category_ids') {
					continue;
				}

				$filter = $blocks[$summaryFilter->field]->getFilter();
				$this->getLayer()->getState()->addFilter($this->_createFilterItem($filter, $summaryFilter->filterValue, $summaryFilter->value));
			}

			return $this;
		} else {
			return parent::_prepareLayout();
		}
	}

	protected function _getEmptyCategoryAttribute() {
		$attribute = new stdClass();
		$attribute->field = 'category_ids';
		$attribute->label = 'category_ids';
		$attribute->type = 'list';
		$attribute->collapse = 0;
		$attribute->facet_active = 1;
		$attribute->values = array();
		return $attribute;
	}

	/**
	 * Create filter item object
	 *
	 * @param   SearchSpring_Manager_Model_Layer_Filter_SearchSpring $filter
	 * @param   string $label
	 * @param   mixed $value
	 * @param   int $count
	 * @return  Mage_Catalog_Model_Layer_Filter_Item
	 */
	protected function _createFilterItem($filter, $label, $value, $count=0)
	{
		$item = Mage::getModel('searchspring_manager/layer_filter_searchSpringItem')
			->setFilter($filter)
			->setLabel($label)
			->setValue($value)
			->setCount($count);

		return $item;
	}

	/**
	 * Get all layer filters
	 *
	 * @return array
	 */
	public function getFilters()
	{
		if ($this->isSearchSpringEnabled()) {
			$filters = array();
			if ($categoryFilter = $this->_getCategoryFilter()) {
				$filters[] = $categoryFilter;
			}

			$filterableAttributes = $this->_getFilterableAttributes();
			foreach ($filterableAttributes as $attribute) {
				$filters[] = $this->getChild($attribute->field . '_filter');
			}

			return $filters;
		} else {
			return parent::getFilters();
		}
	}

	public function isSearchSpringEnabled()
	{
		return $this->getLayer()->isSearchSpringEnabled();
	}

	/**
	 * Get layer object
	 *
	 * @return Mage_Catalog_Model_Layer
	 */
	public function getLayer()
	{
		return Mage::getSingleton('searchspring_manager/layer');
	}

}
