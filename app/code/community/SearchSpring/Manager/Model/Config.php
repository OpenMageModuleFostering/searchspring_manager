<?php
/**
 * File Config.php
 *
 * @copyright B7 Interactive, LLC. All Rights Reserved.
 */

/**
 * Class SearchSpring_Manager_Model_Config
 *
 * You should need to put anything in this class, but Magento needs to to function.
 *
 * @author Jake Shelby <jake@b7interactive.com>
 */
class SearchSpring_Manager_Model_Config
{

	const ENV_VAR_API_BASE_URL					= 'SEARCHSPRING_API_HOST';

	const PATH_API_FEED_ID						= 'ssmanager/ssmanager_api/feed_id';
	const PATH_API_SITE_ID						= 'ssmanager/ssmanager_api/site_id';
	const PATH_API_SECRET_KEY					= 'ssmanager/ssmanager_api/secret_key';

	const PATH_API_AUTHENTICATION_METHOD		= 'ssmanager/ssmanager_api/authentication_method';
	const AUTH_METHOD_SIMPLE					= 'simple';
	const AUTH_METHOD_OAUTH						= 'oauth';

	const PATH_GLOBAL_LIVE_INDEXING_ENABLE_FL	= 'ssmanager/ssmanager_general/live_indexing';
	const PATH_INDEX_ZERO_PRICE					= 'ssmanager/ssmanager_general/index_zero_price';
	const PATH_INDEX_OUT_OF_STOCK				= 'ssmanager/ssmanager_general/index_out_of_stock';

	const PATH_FEED_SETTING_PATH				= 'ssmanager/ssmanager_feed/feed_path';

	const PATH_SALES_RANK_TIMESPAN				= 'ssmanager/ssmanager_sales_rank/timespan';

	const PATH_GLOBAL_CATEGORY_ENABLE_FL		= 'ssmanager/ssmanager_catalog/enable_categories';

	const PATH_GENERATE_CACHE_IMAGES			= 'ssmanager/ssmanager_images/generate_cache_images';
	const PATH_IMAGE_WIDTH						= 'ssmanager/ssmanager_images/image_width';
	const PATH_IMAGE_HEIGHT						= 'ssmanager/ssmanager_images/image_height';

	const PATH_GENERATE_SWATCH_IMAGES			= 'ssmanager/ssmanager_images/generate_swatch_images';
	const PATH_SWATCH_WIDTH						= 'ssmanager/ssmanager_images/swatch_width';
	const PATH_SWATCH_HEIGHT					= 'ssmanager/ssmanager_images/swatch_height';

	const PATH_UUID								= 'ssmanager/ssmanager_track/uuid';

	// TODO -- should these things be a part of the configuration class??
	const MANAGER_API_PATH_PRODUCT_SIMPLE		= 'searchspring/generate/index';
	const MANAGER_API_PATH_GENERATE_SIMPLE		= 'searchspring/generate/feed';
	const MANAGER_API_PATH_PRODUCT_OAUTH		= 'api/rest/searchspring/index';
	const MANAGER_API_PATH_GENERATE_OAUTH		= 'api/rest/searchspring/feed';

	/**
	 * Magento App Container
	 *
	 * @var Mage_Core_Model_App $app
	 */
	protected $app;

	/**
	 * New SearchSpring Config Model
	 *
	 * @var Mage_Core_Model_App $app
	 */
	public function __construct(Mage_Core_Model_App $app)
	{
		$this->app = $app;
	}

	/**
	 * Retrieve config value for store by path
	 *
	 * @param string $path
	 * @param mixed $store
	 * @return mixed
	 */
	public function getStoreConfig($path, $store = null)
	{
		return $this->app->getStore($store)->getConfig($path);
	}

	/**
	 * Retrieve config flag for store by path
	 *
	 * @param string $path
	 * @param mixed $store
	 * @return bool
	 */
	public function getStoreConfigFlag($path, $store = null)
	{
		$flag = strtolower($this->getStoreConfig($path, $store));
		if (!empty($flag) && 'false' !== $flag) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Protected Configuration
	 */
	public function getVersion() {
		return $this->app->getConfig()->getNode('modules/SearchSpring_Manager/version');
	}

	public function getApiBaseUrl()
	{
		if($env = getenv(self::ENV_VAR_API_BASE_URL)) {
			return $env;
		} else {
			return $this->app->getConfig()->getNode('global/searchspring/api_host');
		}
	}

	public function getUUID() {
		return $this->getStoreConfig(self::PATH_UUID);
	}

	/**
	 * Store Level Only Configuration
	 */

	public function getApiSiteId($store)
	{
		return $this->getStoreConfig(self::PATH_API_SITE_ID, $store);
	}

	public function getApiSecretKey($store)
	{
		return $this->getStoreConfig(self::PATH_API_SECRET_KEY, $store);
	}

	public function getApiFeedId($store)
	{
		return $this->getStoreConfig(self::PATH_API_FEED_ID, $store);
	}

	public function getAuthenticationMethod($store)
	{
		return $this->getStoreConfig(self::PATH_API_AUTHENTICATION_METHOD, $store);
	}

	/**
	 * General Configuration Getters
	 */

	public function isLiveIndexingEnabled($store = null)
	{
		return $this->getStoreConfigFlag(self::PATH_GLOBAL_LIVE_INDEXING_ENABLE_FL, $store);
	}

	public function isZeroPriceIndexingEnabled($store = null)
	{
		return $this->getStoreConfigFlag(self::PATH_INDEX_ZERO_PRICE, $store);
	}

	public function isOutOfStockIndexingEnabled($store = null)
	{
		return $this->getStoreConfigFlag(self::PATH_INDEX_OUT_OF_STOCK, $store);
	}

	public function getSalesRankTimespan($store = null)
	{
		return $this->getStoreConfig(self::PATH_SALES_RANK_TIMESPAN, $store);
	}

	public function getFeedPath($store = null)
	{
		return $this->getStoreConfig(self::PATH_FEED_SETTING_PATH, $store);
	}

	public function isCacheImagesEnabled($store = null)
	{
		return $this->getStoreConfig(self::PATH_GENERATE_CACHE_IMAGES, $store);
	}

	public function getImageHeight($store = null)
	{
		return $this->getStoreConfig(self::PATH_IMAGE_HEIGHT, $store);
	}

	public function getImageWidth($store = null)
	{
		return $this->getStoreConfig(self::PATH_IMAGE_WIDTH, $store);
	}

	public function isSwatchImagesEnabled($store = null)
	{
		return $this->getStoreConfig(self::PATH_GENERATE_SWATCH_IMAGES, $store);
	}

	public function getSwatchHeight($store = null)
	{
		return $this->getStoreConfig(self::PATH_SWATCH_HEIGHT, $store);
	}

	public function getSwatchWidth($store = null)
	{
		return $this->getStoreConfig(self::PATH_SWATCH_WIDTH, $store);
	}


	// This config is not really used yet
	public function isCategorySearchEnabled()
	{
		return $this->getStoreConfigFlag(self::PATH_GLOBAL_CATEGORY_ENABLE_FL);
	}

	/**
	 * Dynamic / Combined Configuration Getters
	 */

	public function isStoreSetup($store) {
		// Make sure these things are configured for the store
		return (
			$this->getApiSiteId($store) &&
			$this->getApiSecretKey($store) &&
			$this->getApiFeedId($store) &&
			$this->getAuthenticationMethod($store)
		);
	}

	// Alias for isStoreSetup
	public function isStoreConfigured($store) {
		return $this->isStoreSetup($store);
	}

	public function getApiCredentials($store) {
		return new SearchSpring_Manager_Entity_RequestCredentials(
			$this->getApiSiteId($store),
			$this->getApiSecretKey($store)
		);
	}

	public function getMageApiCredentials($store) {
		return new SearchSpring_Manager_Entity_RequestCredentials(
			$this->getApiFeedId($store),
			$this->getApiSecretKey($store)
		);
	}

	public function getMageAPIPathGenerate($store) {
		switch ($this->getAuthenticationMethod($store)) {
			case self::AUTH_METHOD_SIMPLE:
				return self::MANAGER_API_PATH_GENERATE_SIMPLE;
			case self::AUTH_METHOD_OAUTH:
				$params = '?store=' . $this->app->getStore($store)->getCode();
				return self::MANAGER_API_PATH_GENERATE_OAUTH . $params;
		}
		return false;
	}

	public function getMageAPIPathProduct($store) {
		switch ($this->getAuthenticationMethod($store)) {
			case self::AUTH_METHOD_SIMPLE:
				return self::MANAGER_API_PATH_PRODUCT_SIMPLE;
			case self::AUTH_METHOD_OAUTH:
				$params = '?store=' . $this->app->getStore($store)->getCode();
				return self::MANAGER_API_PATH_PRODUCT_OAUTH . $params;
		}
		return false;
	}

	public function getMageAPIUrlGenerate($store) {
		return $this->app->getStore($store)
			->getBaseUrl(Mage_Core_Model_Store::URL_TYPE_LINK, true) .
				$this->getMageAPIPathGenerate($store);
	}

	public function getMageAPIUrlProduct($store) {
		return $this->app->getStore($store)
			->getBaseUrl(Mage_Core_Model_Store::URL_TYPE_LINK, true) .
				$this->getMageAPIPathProduct($store);
	}

	public function getMageUrlGeneratedFeed($store) {
		$filename = SearchSpring_Manager_Writer_Product_Params_FileWriterParams::getBaseFilename($store);
		$feedFilePath = rtrim($this->getFeedPath($store), '/') . '/' . $filename;

		return $this->app->getStore($store)
			->getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB, false) . $feedFilePath;
	}

}
