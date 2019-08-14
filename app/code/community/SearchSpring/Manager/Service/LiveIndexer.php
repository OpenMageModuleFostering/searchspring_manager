<?php
/**
 * LiveIndexer.php
 *
 * @copyright B7 Interactive, LLC. All Rights Reserved.
 */

/**
 * Class SearchSpring_Manager_Service_LiveIndexer
 *
 * Pushes product/category live indexing requests to the SearchSpring API. Exposes
 * interface for simple change events, and resolves product dependencies, as
 * well as multiple search spring accounts, and store/account specific configurations.
 *
 * @author Jake Shelby <jake@b7interactive.com>
 */
class SearchSpring_Manager_Service_LiveIndexer
{

	/**
	 * Magento App Container
	 *
	 * @var Mage_Core_Model_App $app
	 */
	protected $app;

	/**
	 * SearchSpring configuration
	 *
	 * @var SearchSpring_Manager_Model_Config
	 */
	protected $config;

	/**
	 * API Adapter Factory
	 *
	 * @var SearchSpring_Manager_Factory_ApiFactory $apiAdapterFactory
	 */
	protected $apiAdapterFactory;

	/**
	 * API Request Factory
	 *
	 * @var SearchSpring_Manager_Factory_IndexingRequestBodyFactory $requestBodyFactory
	 */
	protected $requestBodyFactory;

	/**
	 * API Request Factory
	 *
	 * @var SearchSpring_Manager_Service_SearchSpring_IndexingApiAdapter[] $apiAdapters
	 */
	protected $apiAdapters = array();

	/**
	 * Cache of loaded product instances by id, by store
	 *
	 * @var Mage_Catalog_Model_Product[][] $loadedProducts
	 */
	protected $loadedProducts = array();

	/**
	 * Cache of loaded category instances by id, by store
	 *
	 * @var Mage_Catalog_Model_Category[][] $loadedCategories
	 */
	protected $loadedCategories = array();

	/**
	 * Constructor
	 */
	public function __construct(
		Mage_Core_Model_App $app,
		SearchSpring_Manager_Model_Config $config,
		SearchSpring_Manager_Factory_ApiFactory $apiAdapterFactory,
		SearchSpring_Manager_Factory_IndexingRequestBodyFactory $requestBodyFactory
	) {
		$this->app = $app;
		$this->config = $config;
		$this->apiAdapterFactory = $apiAdapterFactory;
		$this->requestBodyFactory = $requestBodyFactory;
	}

	/**
	 * Cache and return an API adapter for the given store. Throws exception
	 * if the store does not have a SearchSpring account tied to it.
	 *
	 * @param mixed $store Magento store object|id|code
	 * @return SearchSpring_Manager_Service_SearchSpring_IndexingApiAdapter
	 * @throws Exception
	 */
	public function apiAdapter($store)
	{
		$storeId = $this->getStoreId($store);
		if (!isset($this->apiAdapters[$storeId])) {

			$adapter = $this->apiAdapterFactory->makeIndexingAdapter($store);

			$this->apiAdapters[$storeId] = $adapter;
		}
		return $this->apiAdapters[$storeId];
	}

	public function productRequest($ids, $shouldDelete = false)
	{
		return $this->requestBodyFactory->make(
			SearchSpring_Manager_Entity_IndexingRequestBody::TYPE_PRODUCT, $ids, $shouldDelete
		);
	}

	public function categoryRequest($ids, $shouldDelete = false)
	{
		return $this->requestBodyFactory->make(
			SearchSpring_Manager_Entity_IndexingRequestBody::TYPE_CATEGORY, $ids, $shouldDelete
		);
	}

	public function shouldIndexStore($store)
	{
		// Make sure the store is configured for SearchSpring
		if (!$this->config->isStoreConfigured($store)) {
			return false;
		}

		// Make sure the store is enabled with live indexing
		if (!$this->config->isLiveIndexingEnabled($store)) {
			return false;
		}

		return true;
	}

	public function getStore($store)
	{
		return $this->app->getStore($store);
	}

	public function getStoreId($store)
	{
		return $this->getStore($store)->getId();
	}

	public function isProductInStore($product, $store)
	{
		return in_array($this->getStoreId($store), $product->getStoreIds());
	}

	public function getStoreWebsiteIds(array $stores)
	{
		$websites = array();
		foreach($stores as $store) {
			$store = $this->getStore($store);
			if (!is_null($store->getWebsiteId())) {
				$websites[] = $store->getWebsiteId();
			}
		}
		return array_unique($websites);
	}

	public function getProductStores($product)
	{
		$stores = array();
		foreach($product->getStoreIds() as $storeId) {
			if ($this->shouldIndexStore($storeId)) {
				$stores[] = $storeId;
			}
		}
		return $stores;
	}

	public function getCategoryStores($category)
	{
		$stores = array();
		foreach($category->getStoreIds() as $storeId) {
			if ($this->shouldIndexStore($storeId)) {
				$stores[] = $storeId;
			}
		}
		return $stores;
	}

	public function loadProduct($product, $store)
	{
		$newProduct = $this->app->getConfig()->getModelInstance('catalog/product');

		$newProduct->setStoreId( $this->getStoreId($store) );
		$newProduct->load( $this->getModelId($product) );

		return $newProduct;
	}

	public function getProduct($product, $store)
	{
		// Because of wierdness in magento, we have to load a
		// fresh product in the requested store context, even
		// if the passed product is for the correct store. This
		// can happen if in the admin context for a certain
		// store scope, and a product is saved, it's data might
		// have remnants of overriden attribute values, even
		// if the current attribute is set to use the default
		// attribute value.

		$pId = $this->getModelId($product);
		$storeId = $this->getStoreId($store);
		if (!isset($this->loadedProducts[$pId][$storeId])) {

			$this->loadedProducts[$pId][$storeId] = 
				$this->loadProduct($product, $store);

		}

		return $this->loadedProducts[$pId][$storeId];
	}

	public function loadCategory($category, $store)
	{
		$category = $this->app->getConfig()->getModelInstance('catalog/category');

		$category->setStoreId( $this->getStoreId($store) );
		$category->load( $this->getModelId($category) );

		return $category;
	}

	public function getCategory($category, $store)
	{
		$pId = $this->getModelId($category);
		$storeId = $this->getStoreId($store);
		if (!isset($this->loadedCategories[$pId][$storeId])) {

			$this->loadedCategories[$pId][$storeId] = 
				$this->loadCategory($category, $store);

		}

		return $this->loadedCategories[$pId][$storeId];
	}

	public function getModelId($model)
	{
		if ($model instanceof Varien_Object) {
			return $model->getId();
		}
		return $model;
	}

	public function getRelatedProductIds($product)
	{
		$productIds = array();
		// If the product is a simple product we may need to check its parent(s)
		if($product->getTypeId() == "simple"){
			// Check for configurable parent
			$type = $this->app->getConfig()->getModelInstance('catalog/product_type_configurable');
			$productIds = $type->getParentIdsByChild($product->getId());

			// If there are no configurable parents check grouped
			if(empty($productIds)) {
				$type = $this->app->getConfig()->getModelInstance('catalog/product_type_grouped');
				$productIds = $type->getParentIdsByChild($product->getId());
			}
		}

		return $productIds;
	}

	public function getProductPricingStrategy($product)
	{
		// TODO -- can we move this somewhere else?? should we have to do all this here
		$pricingStrategy = SearchSpring_Manager_Factory_PricingFactory::make($product);

		$newContext = new Varien_Object(array(
			'store_id'  => $product->getStoreId(),
			'website_id'  => $product->getWebsiteId(),
			'customer_group_id' => 0,
		));

		// Get the current values (in case they happen to be set)
		$ruleData = Mage::registry('rule_data');
		$context = Mage::registry('catalog_category_store_context');

		// Remove the current values (in case they happen to be set)
		Mage::unregister('rule_data');
		Mage::unregister('catalog_category_store_context');

		// Need to declare the correct store context for catalog/rule logic
		Mage::register('rule_data', $newContext, true);
		Mage::register('catalog_category_store_context', $newContext, true);

		// Calculate prices for this strategy
		$pricingStrategy->calculatePrices();

		// Remove our rule data
		Mage::unregister('rule_data');
		Mage::unregister('catalog_category_store_context');

		// Put the values back (if they were set)
		if (!is_null($ruleData)) {
			Mage::register('rule_data', $ruleData, true);
		}
		if (!is_null($context)) {
			Mage::register('catalog_category_store_context', $context, true);
		}

		return $pricingStrategy;
	}

	public function shouldProductBeDeleted($product)
	{
		$validator = new SearchSpring_Manager_Validator_ProductValidator($this->config);

		// Is the product valid
		if (!$validator->isValid($product)) {
			return true;
		}

		// Ask validator if we should delete
		$pricingStrategy = $this->getProductPricingStrategy($product);
		if ($validator->shouldDelete($product, $pricingStrategy)) {
			return true;
		}

		return false;
	}

	/**
	 * Service Action Calls
	 */

	/**
	 * Send live indexing request to SearchSpring API, for the store
	 * if it's connected to an account, and enabled with live indexing.
	 *
	 * Throws exception for network adapter/client related issues.
	 *
	 * @param mixed $store Magento store object|id|code
	 * @param SearchSpring_Manager_Entity_IndexingRequestBody $request Live Indexing request
	 * @param bool $force True to force the request to be sent, ignoring the configuration for live indexing being enabled or not
	 * @throws Exception
	 */
	public function sendRequest($store, SearchSpring_Manager_Entity_IndexingRequestBody $request, $force = false)
	{
		// Make sure there are ids in the request
		if (!count($request->getIds())) {
			return;
		}

		// Make sure the store is configured for SearchSpring
		if (!$this->config->isStoreConfigured($store)) {
			return;
		}

		// Make sure the store is enabled with live indexing
		if (!$force && !$this->config->isLiveIndexingEnabled($store)) {
			return;
		}

		// Get the API adapter for this store
		$api = $this->apiAdapter($store);

		// Make actual request
		$api->pushIds($request);
	}

	/**
	 * Send live indexing request to SearchSpring API, for each store that
	 * is connected to an account, and enabled with live indexing.
	 *
	 * Throws exception for network adapter/client related issues.
	 *
	 * @param array $stores List of Magento store object|id|code 's
	 * @param SearchSpring_Manager_Entity_IndexingRequestBody $request Live Indexing request
	 * @throws Exception
	 */
	public function sendRequests($stores, SearchSpring_Manager_Entity_IndexingRequestBody $request)
	{
		foreach($stores as $store) {
			$this->sendRequest($store, $request);
		}
	}

	public function categoryProductsUpdated($category, $products) {

		// Get stores for this category
		$stores = $this->getCategoryStores($category);

		// Make sure we need to do anything
		if (!count($stores)) return;


		$ids = array();
		foreach($products as $product) {

			// TODO - Should we skip products that are not in these stores?

			$ids[] = $this->getModelId($product);
		}

		$request = $this->productRequest($ids);

		$this->sendRequests($stores, $request);
	}

	public function categorySaved($category)
	{
		// Get stores for this category
		$stores = $this->getCategoryStores($category);

		// Make sure we need to do anything
		if (!count($stores)) return;

		$ids = $category->getAllChildren(true);

		$request = $this->categoryRequest($ids);

		$this->sendRequests($stores, $request);
	}

	/**
	 * Call for a category that is going to be deleted. All assocated/affected products will be requested for live indexing.
	 */
	public function categoryDeleted($category)
	{
		// Get stores for this category
		$stores = $this->getCategoryStores($category);

		// Make sure we need to do anything
		if (!count($stores)) return;

		// Get the website ids for these stores
		$websiteIds = $this->getStoreWebsiteIds($stores);

		$collection = mage::getModel('catalog/product')->getCollection()
			->addAttributeToSelect('*')
			->joinField('category_id', 'catalog/category_product', 'category_id', 'product_id=entity_id', null, 'left')
			->addAttributeToFilter('category_id', array('in' => $category->getAllChildren(true)));
		$collection->addWebsiteFilter($websiteIds);
		$collection->getSelect()->group('e.entity_id');
		$productIds = $collection->load(false, true)->getAllIds();

		$request = $this->productRequest($productIds);

		$this->sendRequests($stores, $request);
	}

	/**
	 * Register a Live Indexing request for a saved product.
	 * Find all related parent products, and include them
	 * in the request. Foreach product determine wether they
	 * should be deleted or just updated.
	 *
	 * The requests will only be sent to accounts that the
	 * main product is registered with - not the related
	 * parent product stores.
	 *
	 * @param Mage_Catalog_Model_Product $savedProduct
	 * @return void
	 */
	public function productSaved($savedProduct)
	{
		// Get stores for this product
		$stores = $this->getProductStores($savedProduct);

		// Make sure we need to do anything
		if (!count($stores)) return;

		$affectedProducts = $this->getRelatedProductIds($savedProduct);
		$affectedProducts[] = $savedProduct;

		foreach($stores as $store) {

			$pIdsRemove = array();
			$pIdsUpdate = array();
			foreach($affectedProducts as $affectedProduct) {

				// Get loaded product for this store
				$product = $this->getProduct($affectedProduct, $store);

				// Make sure this product is part of this store
				if (!$this->isProductInStore($product, $store)) {
					continue;
				}

				// Find out if it should be deleted or just updated
				if ($this->shouldProductBeDeleted($product)) {
					$pIdsRemove[] = $product->getId();
				} else {
					$pIdsUpdate[] = $product->getId();
				}
			}

			// Request update for removes
			$request = $this->productRequest($pIdsRemove, true);
			$this->sendRequest($store, $request);

			// Request update for changes
			$request = $this->productRequest($pIdsUpdate);
			$this->sendRequest($store, $request);
		}
	}

	/**
	 * Before a product is going to be deleted, call this to get a token
	 * that can be used to call us back when the product has been deleted.
	 *
	 * This step is required to obtain data related to the product, because
	 * after the product is deleted, there's no way to obtain the needed
	 * data.
	 *
	 * @param mixed $product	The magento product, or product id that will
	 * 							be potentially deleted
	 *
	 * @return SearchSpring_Manager_Entity_ProductDeletionToken
	 */
	public function obtainProductDeletionToken($product)
	{
		// Get stores for this product
		$stores = $this->getProductStores($product);

		// Make sure we need to do anything
		if (!count($stores)) {
			return new SearchSpring_Manager_Entity_ProductDeletionToken(
				$this->getModelId($product), array()
			);
		}

		$related = $this->getRelatedProductIds($product);

		return new SearchSpring_Manager_Entity_ProductDeletionToken(
			$this->getModelId($product), $stores, $related
		);
	}

	/**
	 * After a product has been deleted, pass the token to finalize/commit
	 * the live indexing request. Use the obtainProductDeletionToken before
	 * the product is deleted in order to have a token after the product has
	 * been deleted.
	 *
	 * @param SearchSpring_Manager_Entity_ProductDeletionToken $token
	 */
	public function productDeleted(SearchSpring_Manager_Entity_ProductDeletionToken $token)
	{
		// Make sure we need to do anything
		if (!count($token->getStores())) return;

		$stores = $token->getStores();

		// Send request to delete product
		$request = $this->productRequest(array($token->getProductId()), true);
		$this->sendRequests($stores, $request);

		// Send request to update related products
		$request = $this->productRequest($token->getRelatedProductIds());
		$this->sendRequests($stores, $request);
	}

}
