<?php
/**
 * Layer.php
 *
 * @copyright B7 Interactive, LLC. All Rights Reserved.
 */

/**
 * Class SearchSpring_Manager_Model_Layer
 *
 * Overrides existing Layer object
 *
 * @author James Bathgate <james@b7interactive.com>
 */
class SearchSpring_Manager_Model_Layer extends Mage_Catalog_Model_Layer
{

	/**
	 * Retrieve current layer search collection
	 */
	public function getProductCollection()
	{
		if (!$this->isSearchSpringEnabled()) {
			return parent::getProductCollection();
		}

		if (isset($this->_productCollections[$this->getCurrentCategory()->getId()])) {
			$collection = $this->_productCollections[$this->getCurrentCategory()->getId()];
		} else {
			$apiFactory = new SearchSpring_Manager_Factory_ApiFactory();
			$api = $apiFactory->makeSearchAdapter();

			$searchRequestBodyFactory = new SearchSpring_Manager_Factory_SearchRequestBodyFactory();
			$searchRequestBody = $searchRequestBodyFactory->make();

			$collection = new SearchSpring_Manager_Entity_SearchCollection($api, $searchRequestBody);
			$collection->addFilter('category_ids', $this->getCurrentCategory()->getId());
			$collection->addFilterOnly('category_ids', explode(',', $this->getCurrentCategory()->getChildren()));

			$this->_productCollections[$this->getCurrentCategory()->getId()] = $collection;
		}

		return $collection;
	}

	/**
	 * Returns whether SearchSpring is enabled for the current category or not
	 *
	 * @return bool
	 */
	public function isSearchSpringEnabled()
	{
		return $this->getCurrentCategory()->getIsSearchSpringEnabled();
	}

	public function getFilterableAttributes()
	{
		if($this->isSearchSpringEnabled()) {
			return $this->getProductCollection()->getResults()->getFacets();
		} else {
			return parent::getFilterableAttributes();
		}
	}

}
