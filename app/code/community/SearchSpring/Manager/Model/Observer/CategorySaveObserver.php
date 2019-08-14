<?php
/**
 * CategorySaveObserver.php
 *
 * @copyright B7 Interactive, LLC. All Rights Reserved.
 */

/**
 * Class SearchSpring_Manager_Model_Observer_CategorySaveObserver
 *
 * On a category change, trigger one of these methods
 *
 * @author Nate Brunette <nate@b7interactive.com>
 */
class SearchSpring_Manager_Model_Observer_CategorySaveObserver extends SearchSpring_Manager_Model_Observer_LiveIndexer
{
	/**
	 * Performs operations on a Varien_Object
	 *
	 * @var SearchSpring_Manager_VarienObject_Data $varienObjectData
	 */
	private $varienObjectData;

	/**
	 * Create a request body
	 *
	 * @var SearchSpring_Manager_Factory_IndexingRequestBodyFactory $requestBodyFactory
	 */
	private $requestBodyFactory;

	/**
	 * Constructor
	 */
	public function __construct()
	{
		$this->requestBodyFactory = new SearchSpring_Manager_Factory_IndexingRequestBodyFactory();
		$this->varienObjectData = new SearchSpring_Manager_VarienObject_Data();
	}

	/**
	 * After a category is saved
	 *
	 * We only push out an update to products and sub-products if the category name or status has changed
	 *
	 * @todo implement status change
	 *
	 * @param Varien_Event_Observer $productEvent
	 *
	 * @return bool
	 */
	public function afterSaveUpdateProductCategory(Varien_Event_Observer $productEvent)
	{
		if (!$this->isEnabled()) {
			return true;
		}

		/** @var Mage_Catalog_Model_Category $category */
		$category = $productEvent->getData('category');

		// if this is a new category return because we already sent the ids
		if (true === $this->varienObjectData->isNew($category)) {
			return true;
		}

		$updates = $this->varienObjectData->findUpdatedData($category);

		// if category name is not changed, this will not affect the product categories
		if (!isset($updates['name']) && !isset($updates['is_active'])) {
			return true;
		}

		$requestBody = $this->requestBodyFactory->make(
			SearchSpring_Manager_Entity_IndexingRequestBody::TYPE_CATEGORY,
			$category->getAllChildren(true)
		);
		$this->apiPushProductIds($requestBody);

		return true;
	}

	/**
	 * After category products have changed
	 *
	 * This is triggered when products are checked or unchecked for a category.  Only affects products of category.
	 *
	 * @param Varien_Event_Observer $productEvent
	 *
	 * @return bool
	 */
	public function afterProductChangeUpdateProductCategory(Varien_Event_Observer $productEvent)
	{
		if (!$this->isEnabled()) {
			return true;
		}

		$validator = new SearchSpring_Manager_Validator_ProductValidator();

		$productIds = $productEvent->getData('product_ids');
		foreach($productIds as $k => $productId) {
			$product = Mage::getModel('catalog/product')->load($productId);
			if(!$validator->isValid($product)) {
				unset($productIds[$k]);
			}
		}

		$requestBody = $this->requestBodyFactory->make(
			SearchSpring_Manager_Entity_IndexingRequestBody::TYPE_PRODUCT,
			$productIds
		);

		$this->apiPushProductIds($requestBody);

		return true;
	}

	/**
	 * After a category is moved
	 *
	 * Updates products and sub-products. Only update if path is changed. This should be double checked, but path
	 * should not be changed if the category is just reordered.
	 *
	 * @param Varien_Event_Observer $productEvent
	 *
	 * @return bool
	 */
	public function afterMoveUpdateProductCategory(Varien_Event_Observer $productEvent)
	{
		if (!$this->isEnabled()) {
			return true;
		}

		$category = $productEvent->getData('category');
		$updates = $this->varienObjectData->findUpdatedData($category);

		if (!isset($updates['path'])) {
			return true;
		}

		$requestBody = $this->requestBodyFactory->make(
			SearchSpring_Manager_Entity_IndexingRequestBody::TYPE_CATEGORY,
			$category->getAllChildren(true)
		);
		$this->apiPushProductIds($requestBody);

		return true;
	}

	/**
	 * After a category is deleted
	 *
	 * Remove the category from products and sub-products.
	 *
	 * @param Varien_Event_Observer $productEvent
	 * @return bool
	 */
	public function afterDeleteUpdateProductCategory(Varien_Event_Observer $productEvent)
	{
		if (!$this->isEnabled()) {
			return true;
		}

		/** @var Mage_Catalog_Model_Category $category */
		$category = $productEvent->getData('category');
		$requestBody = $this->requestBodyFactory->make(
			SearchSpring_Manager_Entity_IndexingRequestBody::TYPE_PRODUCT,
			$this->getCategoryProductIds($category)
		);
		$this->apiPushProductIds($requestBody);

		return true;
	}

	/**
	 * Get product ids for category and subcategories
	 *
	 * @param Mage_Catalog_Model_Category $category
	 *
	 * @return array
	 */
	private function getCategoryProductIds(Mage_Catalog_Model_Category $category)
	{
		/** @var Mage_Catalog_Model_Resource_Product_Collection $collection */
		$collection = mage::getModel('catalog/product')->getCollection()
			->addAttributeToSelect('*')
			->joinField('category_id', 'catalog/category_product', 'category_id', 'product_id=entity_id', null, 'left')
			->addAttributeToFilter('category_id', array('in' => $category->getAllChildren(true)));
		$collection->getSelect()->group('e.entity_id');
		$productIds = $collection->load()->getAllIds();

		return $productIds;
	}

}
