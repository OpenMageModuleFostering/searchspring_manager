<?php

class SearchSpring_Manager_Model_Layer_Filter_SearchSpringCategory extends SearchSpring_Manager_Model_Layer_Filter_SearchSpring
{

	protected $_itemModelName = 'searchspring_manager/layer_filter_searchSpringCategoryItem';

	protected $_childCategories = array();

	public function getCurrentValue() {
		if(!isset($this->_currentValue)) {
			$this->_currentValue = Mage::app()->getRequest()->getParam($this->getRequestVar());
		}

		if(is_null($this->_currentValue)) {
			$this->_currentValue = $this->getLayer()->getCurrentCategory()->getId();
		}

		return $this->_currentValue;
	}

	public function getCurrentCategory() {
		if(!isset($this->_currentCategory)) {
			$categoryId = $this->getCurrentValue();
			$this->_currentCategory = Mage::getModel('catalog/category')->load($categoryId);
		}

		return $this->_currentCategory;
	}

	/**
	 * Get data array for building attribute filter items
	 *
	 * @return array
	 */
	protected function _getItemsData()
	{
		$childCategories = $this->_getChildCategories();

		$items = array();

		foreach($this->_searchSpringFacet->values as $value) {

			if(!isset($this->_childCategories[$value->value])) {
				continue;
			}

			$category = $this->_childCategories[$value->value];

			$item = array(
				'label' => $category->getName(),
				'count' => $value->count,
				'type' => $value->type,
				'value' => $value->value
			);

			$items[] = $item;
		}

		return $items;
	}

	public function getChildCategory($id) {
		if(empty($this->_childCategories)) {
			$this->_getChildCategories();
		}

		return $this->_childCategories[$id];
	}

	protected function _getChildCategories() {
		if(empty($this->_childCategories)) {
			$category = $this->getCurrentCategory();
			$children = $category->getChildrenCategories();

			foreach ($children as $child) {
				$this->_childCategories[$child->getId()] = $child;
			}
		}

		return $this->_childCategories;

	}

	public function getParents() {
		$pathIds = array_reverse(explode(',', $this->getCurrentCategory()->getPathInStore()));
		$categories = Mage::getResourceModel('catalog/category_collection')
			->setStore(Mage::app()->getStore())
			->addAttributeToSelect('name')
			->addAttributeToSelect('url_key')
			->addFieldToFilter('entity_id', array('in' => $pathIds))
			->addFieldToFilter('is_active', 1)
			->setOrder('level')
			->load()
			->getItems();

		return $categories;
	}

	/**
	 * Return the name of the facet
	 *
	 * @return string
	 */
	public function getName() {
		return $this->__('Category');
	}

}
