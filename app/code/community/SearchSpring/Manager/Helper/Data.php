<?php
/**
 * File Data.php
 *
 * @copyright B7 Interactive, LLC. All Rights Reserved.
 */

/**
 * Class SearchSpring_Manager_Helper_Data
 *
 * You should need to put anything in this class, but Magento needs to to function.
 *
 * @author Nate Brunette <nate@b7interactive.com>
 */
class SearchSpring_Manager_Helper_Data extends Mage_Core_Helper_Abstract
{

	const XML_PATH_GLOBAL_LIVE_INDEXING_ENABLE_FL	= 'ssmanager/ssmanager_general/live_indexing';
	const XML_PATH_INDEX_ZERO_PRICE					= 'ssmanager/ssmanager_general/index_zero_price';
	const XML_PATH_INDEX_OUT_OF_STOCK				= 'ssmanager/ssmanager_general/index_out_of_stock';

	const XML_PATH_FEED_SETTING_PATH				= 'ssmanager/ssmanager_feed/feed_path';

	const XML_PATH_SALES_RANK_TIMESPAN				= 'ssmanager/ssmanager_sales_rank/timespan';

	const XML_PATH_GLOBAL_CATEGORY_ENABLE_FL		= 'ssmanager/ssmanager_catalog/enable_categories';

	const XML_PATH_API_BASE_URL						= 'ssmanager/ssmanager_api/base_url';
	const ENV_VAR_API_BASE_URL                      = 'SEARCHSPRING_API_HOST';

	const XML_PATH_API_FEED_ID						= 'ssmanager/ssmanager_api/feed_id';
	const XML_PATH_API_SITE_ID						= 'ssmanager/ssmanager_api/site_id';
	const XML_PATH_API_SECRET_KEY					= 'ssmanager/ssmanager_api/secret_key';

	const XML_PATH_API_AUTHENTICATION_METHOD		= 'ssmanager/ssmanager_api/authentication_method';
	const AUTH_METHOD_SIMPLE						= 'simple';
	const AUTH_METHOD_OAUTH							= 'oauth';

	const XML_PATH_GENERATE_CACHE_IMAGES			= 'ssmanager/ssmanager_images/generate_cache_images';
	const XML_PATH_IMAGE_WIDTH						= 'ssmanager/ssmanager_images/image_width';
	const XML_PATH_IMAGE_HEIGHT						= 'ssmanager/ssmanager_images/image_height';

	const XML_PATH_GENERATE_SWATCH_IMAGES			= 'ssmanager/ssmanager_images/generate_swatch_images';
	const XML_PATH_SWATCH_WIDTH						= 'ssmanager/ssmanager_images/swatch_width';
	const XML_PATH_SWATCH_HEIGHT					= 'ssmanager/ssmanager_images/swatch_height';

	const XML_PATH_UUID								= 'ssmanager/ssmanager_track/uuid';

	const MANAGER_API_PATH_PRODUCT_SIMPLE			= 'searchspring/generate/index';
	const MANAGER_API_PATH_GENERATE_SIMPLE			= 'searchspring/generate/feed';
	const MANAGER_API_PATH_PRODUCT_OAUTH			= 'api/rest/searchspring/index';
	const MANAGER_API_PATH_GENERATE_OAUTH			= 'api/rest/searchspring/feed';

	public function getVersion() {
		return Mage::getConfig()->getNode('modules/SearchSpring_Manager/version');
	}

	public function isLiveIndexingEnabled()
	{
		return Mage::getStoreConfigFlag(self::XML_PATH_GLOBAL_LIVE_INDEXING_ENABLE_FL);
	}

	public function isCategorySearchEnabled()
	{
		return Mage::getStoreConfigFlag(self::XML_PATH_GLOBAL_CATEGORY_ENABLE_FL);
	}

	public function isZeroPriceIndexingEnabled()
	{
		return Mage::getStoreConfigFlag(self::XML_PATH_INDEX_ZERO_PRICE);
	}

	public function isOutOfStockIndexingEnabled()
	{
		return Mage::getStoreConfigFlag(self::XML_PATH_INDEX_OUT_OF_STOCK);
	}

	public function getSalesRankTimespan()
	{
		return Mage::getStoreConfig(self::XML_PATH_SALES_RANK_TIMESPAN);
	}

	public function getFeedPath()
	{
		return Mage::getStoreConfig(self::XML_PATH_FEED_SETTING_PATH);
	}

	public function getApiFeedId()
	{
		return Mage::getStoreConfig(self::XML_PATH_API_FEED_ID);
	}

	public function getApiSiteId()
	{
		return Mage::getStoreConfig(self::XML_PATH_API_SITE_ID);
	}

	public function getApiBaseUrl()
	{
		if($env = getenv(self::ENV_VAR_API_BASE_URL)) {
			return $env;
		} else {
			return Mage::getStoreConfig(self::XML_PATH_API_BASE_URL);
		}
	}

	public function getApiSecretKey()
	{
		return Mage::getStoreConfig(self::XML_PATH_API_SECRET_KEY);
	}

	public function getAuthenticationMethod() {
		return Mage::getStoreConfig(self::XML_PATH_API_AUTHENTICATION_METHOD);
	}

	public function isCacheImagesEnabled()
	{
		return Mage::getStoreConfig(self::XML_PATH_GENERATE_CACHE_IMAGES);
	}

	public function getImageHeight()
	{
		return Mage::getStoreConfig(self::XML_PATH_IMAGE_HEIGHT);
	}

	public function getImageWidth()
	{
		return Mage::getStoreConfig(self::XML_PATH_IMAGE_WIDTH);
	}

	public function isSwatchImagesEnabled()
	{
		return Mage::getStoreConfig(self::XML_PATH_GENERATE_SWATCH_IMAGES);
	}

	public function getSwatchHeight()
	{
		return Mage::getStoreConfig(self::XML_PATH_SWATCH_HEIGHT);
	}

	public function getSwatchWidth()
	{
		return Mage::getStoreConfig(self::XML_PATH_SWATCH_WIDTH);
	}

	public function getUUID() {
		return Mage::getStoreConfig(self::XML_PATH_UUID);
	}

	public function getMageAPIPathGenerate() {
		switch ($this->getAuthenticationMethod()) {
			case self::AUTH_METHOD_SIMPLE:
				return self::MANAGER_API_PATH_GENERATE_SIMPLE;
			case self::AUTH_METHOD_OAUTH:
				return self::MANAGER_API_PATH_GENERATE_OAUTH;
		}
		return false;
	}

	public function getMageAPIPathProduct() {
		switch ($this->getAuthenticationMethod()) {
			case self::AUTH_METHOD_SIMPLE:
				return self::MANAGER_API_PATH_PRODUCT_SIMPLE;
			case self::AUTH_METHOD_OAUTH:
				return self::MANAGER_API_PATH_PRODUCT_OAUTH;
		}
		return false;
	}

	public function getMageAPIUrlGenerate() {
		return Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB, true) . $this->getMageAPIPathGenerate();
	}

	public function getMageAPIUrlProduct() {
		return Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB, true) . $this->getMageAPIPathProduct();
	}

	public function writeStoreConfig($path, $value, $scope = 'default', $scopeId = 0) {
		Mage::getConfig()->saveConfig($path, $value, $scope, $scopeId)->reinit();
	}

	public function registerMagentoAPIAuthenticationWithSearchSpring($verify = false) {

		$whlp = Mage::helper('searchspring_manager/webservice');

		// TODO -- should we cache responses from verify??

		try {

			switch ($this->getAuthenticationMethod()) {
				case self::AUTH_METHOD_SIMPLE:
					if ($verify)
						$response = $whlp->verifyMageAPIAuthSimple();
					else
						$response = $whlp->registerMageAPIAuthSimple();
					break;
				case self::AUTH_METHOD_OAUTH:
					if ($verify)
						$response = $whlp->verifyMageAPIAuthOAuth();
					else
						$response = $whlp->registerMageAPIAuthOAuth();
					break;
				default:
					// TODO - Should we be returning false, this means the setting hasn't been initialized?
					return false;
			}

		} catch (Exception $e) {
			Mage::log(__METHOD__.": Problem while attempting to access the SearchSpring service: " . $e->getMessage());
			return false;
		}

		return $response;
	}

	/**
	 * Intended for layout action parameter helper calls
	 *
	 * If the current layer category is enabled, return
	 * new template; if not return the blocks existing template
	 */
	public function getBlockTemplateIfCategoryEnabled($block, $newTemplate)
	{
		$layer = Mage::getSingleton('searchspring_manager/layer');
		if ($layer->isSearchSpringEnabled()) {
			return $newTemplate;
		}
		return Mage::app()->getLayout()->getBlock($block)->getTemplate();
	}

	/**
	 * Check if module exists and is enabled in global config.
	 *
	 * NOTE: This function most likely exists in the parent, but
	 * may not depending on the version of magento installed.
	 *
	 * @param string $moduleName the full module name, example Mage_Core
	 * @return boolean
	 */
	public function isModuleEnabled($moduleName = null)
	{
		if (!Mage::getConfig()->getNode('modules/' . $moduleName)) {
			return false;
		}

		$isActive = Mage::getConfig()->getNode('modules/' . $moduleName . '/active');
		if (!$isActive || !in_array((string)$isActive, array('true', '1'))) {
			return false;
		}
		return true;
	}

}
