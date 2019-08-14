<?php
/**
 * File Data.php
 *
 * @copyright B7 Interactive, LLC. All Rights Reserved.
 */

/**
 * Class SearchSpring_Manager_Helper_Data
 *
 * @author Nate Brunette <nate@b7interactive.com>
 * @author Jake Shelby <jake@b7interactive.com>
 */
class SearchSpring_Manager_Helper_Data extends Mage_Core_Helper_Abstract
{

	/**
	 * SearchSpring Config Model
	 *
	 * @var SearchSpring_Manager_Model_Config
	 */
	protected $config;

	public function __construct() {
		// Create an instance of Search Spring config provider
		$this->config = new SearchSpring_Manager_Model_Config( Mage::app() );
	}

	public function getConfig() {
		return $this->config;
	}

	/**
	 * Functions to do ... stuff (lack of better word)
	 */

	public function registerMagentoAPIWithSearchSpring($store) {

		// TODO - Restrict one store being registered with the same feed id as another store

		// First make sure we have the data we need
		$feedId = $this->getApiFeedId($store);
		$creds = $this->getApiCredentials($store);

		if (!$feedId) {
			throw new Exception("Cannot register with SearchSpring, missing feedId.");
		}
		if (!$creds->isPopulated()) {
			throw new Exception("Cannot register with SearchSpring, incomplete API credentials.");
		}

		// Register Auth Method
		$whlp = Mage::helper('searchspring_manager/webservice');
		switch ($this->getAuthenticationMethod($store)) {
			case SearchSpring_Manager_Model_Config::AUTH_METHOD_SIMPLE:
				$whlp->registerMageAPIAuthSimple($feedId, $creds);
				break;
			case SearchSpring_Manager_Model_Config::AUTH_METHOD_OAUTH:
				$whlp->registerMageAPIAuthOAuth($feedId, $creds);
				break;
			default:
				throw new Exception("Cannot register with SearchSpring, missing or unknown authentication method chosen.");
		}

		// Register store specific API URLs that this module provides
		$whlp->registerMageAPIUrls(
			$feedId, $creds,
			$this->getMageUrlGeneratedFeed($store),	// Feed
			$this->getMageAPIUrlGenerate($store),	// Batch
			$this->getMageAPIUrlProduct($store)		// Live
		);

		// Each register call above will throw an exception in the
		// event of an unsuccessful result
	}

	public function verifySetupWithSearchSpring($store) {

		// First make sure we have the data we need
		$feedId = $this->getApiFeedId($store);
		$creds = $this->getApiCredentials($store);

		if (!$feedId) {
			throw new Exception("Cannot verify settings with SearchSpring, missing feedId.");
		}
		if (!$creds->isPopulated()) {
			throw new Exception("Cannot verify settings with SearchSpring, incomplete API credentials.");
		}

		// TODO -- should we cache responses from verify??

		$whlp = Mage::helper('searchspring_manager/webservice');
		switch ($this->getAuthenticationMethod($store)) {
			case SearchSpring_Manager_Model_Config::AUTH_METHOD_SIMPLE:
				return $whlp->verifyMageAPIAuthSimple($feedId, $creds);
			case SearchSpring_Manager_Model_Config::AUTH_METHOD_OAUTH:
				return $whlp->verifyMageAPIAuthOAuth($feedId, $creds);
			default:
				throw new Exception("Cannot register with SearchSpring, missing or unknown authentication method chosen.");
		}

		// Each verify call above will throw an exception in the
		// event of problem connecting to SearchSpring webservice
	}

	public function writeStoreConfig($path, $value, $scope = 'default', $scopeId = 0) {
		Mage::getConfig()->saveConfig($path, $value, $scope, $scopeId)->reinit();
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

	public function ensureModelExists($model) {
		$className = Mage::getConfig()->getModelClassName($model);
		Mage::log('Something here');

		if (!class_exists($className, false)) {
			throw new Exception('The model ' . $model . ' does not exist!');
		}

		return true;
	}

	/**
	 * Forwarded Config Getters
	 */

	public function getVersion() {
		return $this->config->getVersion();
	}

	public function getApiBaseUrl() {
		return $this->config->getApiBaseUrl();
	}

	public function getUUID() {
		return $this->config->getUUID();
	}

	public function getApiSiteId($store) {
		return $this->config->getApiSiteId($store);
	}

	public function getApiSecretKey($store) {
		return $this->config->getApiSecretKey($store);
	}

	public function getApiFeedId($store) {
		return $this->config->getApiFeedId($store);
	}

	public function getAuthenticationMethod($store) {
		return $this->config->getAuthenticationMethod($store);
	}

	public function isLiveIndexingEnabled($store = null) {
		return $this->config->isLiveIndexingEnabled($store);
	}

	public function isZeroPriceIndexingEnabled($store = null) {
		return $this->config->isZeroPriceIndexingEnabled($store);
	}

	public function isOutOfStockIndexingEnabled($store = null) {
		return $this->config->isOutOfStockIndexingEnabled($store);
	}

	public function getSalesRankTimespan($store = null) {
		return $this->config->getSalesRankTimespan($store);
	}

	public function getFeedPath($store = null) {
		return $this->config->getFeedPath($store);
	}

	public function isCacheImagesEnabled($store = null) {
		return $this->config->isCacheImagesEnabled($store);
	}

	public function getImageHeight($store = null) {
		return $this->config->getImageHeight($store);
	}

	public function getImageWidth($store = null) {
		return $this->config->getImageWidth($store);
	}

	public function isSwatchImagesEnabled($store = null) {
		return $this->config->isSwatchImagesEnabled($store);
	}

	public function getSwatchHeight($store = null) {
		return $this->config->getSwatchHeight($store);
	}

	public function getSwatchWidth($store = null) {
		return $this->config->getSwatchWidth($store);
	}


	// This config is not really used yet
	public function isCategorySearchEnabled() {
		return $this->config->isCategorySearchEnabled();
	}


	public function isStoreSetup($store) {
		return $this->config->isStoreSetup($store);
	}

	public function getApiCredentials($store) {
		return $this->config->getApiCredentials($store);
	}

	public function getMageApiCredentials($store) {
		return $this->config->getMageApiCredentials($store);
	}

	public function getMageAPIPathGenerate($store) {
		return $this->config->getMageAPIPathGenerate($store);
	}

	public function getMageAPIPathProduct($store) {
		return $this->config->getMageAPIPathProduct($store);
	}

	public function getMageAPIUrlGenerate($store) {
		return $this->config->getMageAPIUrlGenerate($store);
	}

	public function getMageAPIUrlProduct($store) {
		return $this->config->getMageAPIUrlProduct($store);
	}

	public function getMageUrlGeneratedFeed($store) {
		return $this->config->getMageUrlGeneratedFeed($store);
	}

}
