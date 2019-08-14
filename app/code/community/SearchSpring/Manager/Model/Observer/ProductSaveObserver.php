<?php
/**
 * File ProductSaveObserver.php
 *
 * @copyright B7 Interactive, LLC. All Rights Reserved.
 */

/**
 * Class SearchSpring_Manager_Model_Observer_ProductSaveObserver
 *
 * Listens for product save events and pushes ids to api
 *
 * @author Nate Brunette <nate@b7interactive.com>
 */
class SearchSpring_Manager_Model_Observer_ProductSaveObserver extends  SearchSpring_Manager_Model_Observer_LiveIndexer
{
	/**
	 * Create an api adapter for SearchSpring
	 *
	 * @var SearchSpring_Manager_Service_SearchSpring_IndexingApiAdapter $api
	 */
	private $api;

	/**
	 * Creates a request body
	 *
	 * @var SearchSpring_Manager_Factory_IndexingRequestBodyFactory
	 */
	private $requestBodyFactory;

	/**
	 * List of parentIds for the updated product.
	 * @var array
	 */
	protected $_productIds = array();

	/**
	 * Constructor
	 *
	 * We need to do some setup in here because there's no way to inject dependencies
	 */
	public function __construct()
	{
		$apiFactory = new SearchSpring_Manager_Factory_ApiFactory();
		$this->api = $apiFactory->make('index');
		$this->requestBodyFactory = new SearchSpring_Manager_Factory_IndexingRequestBodyFactory();
	}

	/**
	 * After a product is saved, push that product to the SearchSpring API
	 *
	 * @param Varien_Event_Observer $productEvent The product event data
	 *
	 * @return bool
	 */
	public function afterSavePushProduct(Varien_Event_Observer $productEvent)
	{
		if (!$this->isEnabled()) {
			return true;
		}

		$product = $productEvent->getData('product');
		$this->_productIds = $this->_getProductIds($product);

		$this->_handleProductChange();
		return true;
	}

	/**
	* Before a product is deleted grab all IDs
	*
	* @param Varien_Event_Observer $productEvent The product event data
	*
	* @return bool
	*/
	public function beforeDeletePushProduct(Varien_Event_Observer $productEvent) {
		$product = $productEvent->getData('product');
		$this->_productIds = $this->_getProductIds($product);
		return true;
	}

	/**
	 * After a product is deleted, push that product delete to the SearchSpring API
	 *
	 * @param Varien_Event_Observer $productEvent The product event data
	 *
	 * @return bool
	 */
	public function afterDeletePushProduct(Varien_Event_Observer $productEvent)
	{
		if (!$this->isEnabled()) {
			return true;
		}

		$this->_handleProductChange();
		return true;
	}

	protected function _handleProductChange() {
		$productIds = $this->_getActions();

		// create the request body with product ids
		if(!empty($productIds['delete'])) {
			$requestBody = $this->requestBodyFactory->make(
				SearchSpring_Manager_Entity_IndexingRequestBody::TYPE_PRODUCT,
				$productIds['delete'],
				true
			);
		}

		// create the request body with product ids
		if(!empty($productIds['update'])) {
			$requestBody = $this->requestBodyFactory->make(
				SearchSpring_Manager_Entity_IndexingRequestBody::TYPE_PRODUCT,
				$productIds['update']
			);
		}

		// send ids to api
		$this->api->pushIds($requestBody);

		return true;
	}

	protected function _getProductIds($product) {
		$productIds = array();
		// If the product is a simple product we may need to check its parent(s)
		if($product->getTypeId() == "simple"){
			// Check for configurable parent
			$productIds = Mage::getModel('catalog/product_type_configurable')->getParentIdsByChild($product->getId());

			// If there are no configurable parents check grouped
			if(empty($productIds)) {
				$productIds = Mage::getModel('catalog/product_type_grouped')->getParentIdsByChild($product->getId());
			}
		}

		$productIds[] = $product->getId();

		return $productIds;
	}


	/**
	 * Get list of update/deletes for product and parents.
	 *
	 * @param Mage_Catalog_Model_Product $product The product being updated
	 *
	 * @return array
	 */
	protected function _getActions() {
		$productActions = array();

		$validator = new SearchSpring_Manager_Validator_ProductValidator();

		// Figure out deletes
		foreach($this->_productIds as $productId) {
			$product = Mage::getModel('catalog/product')->load($productId);

			$isValid = $validator->isValid(
				$product
			);

			if(!$isValid) {
				$productActions['delete'][] = $productId;
			} else {
				$pricingStrategy = SearchSpring_Manager_Factory_PricingFactory::make($product);
				$pricingStrategy->calculatePrices();

				// Only check should delete if the product is otherwise valid
				$shouldDelete = $validator->shouldDelete(
					$product,
					$pricingStrategy
				);

				if($shouldDelete) {
					$productActions['delete'][] = $productId;
				} else {
					$productActions['update'][] = $productId;
				}
			}
		}

		return $productActions;
	}
}
