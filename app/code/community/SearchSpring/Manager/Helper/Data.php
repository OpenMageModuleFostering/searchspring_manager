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
	const XML_PATH_GLOBAL_CATEGORY_ENABLE_FL		= 'ssmanager/ssmanager_catalog/enable_categories';

	const XML_PATH_INDEX_ZERO_PRICE					= 'ssmanager/ssmanager_general/index_zero_price';

	const XML_PATH_API_FEED_ID						= 'ssmanager/ssmanager_api/feed_id';
	const XML_PATH_API_SITE_ID						= 'ssmanager/ssmanager_api/site_id';
	const XML_PATH_API_BASE_URL						= 'ssmanager/ssmanager_api/base_url';
	const XML_PATH_API_SECRET_KEY					= 'ssmanager/ssmanager_api/secret_key';

	const XML_PATH_GENERATE_CACHE_IMAGES			= 'ssmanager/ssmanager_images/generate_cache_images';
	const XML_PATH_IMAGE_WIDTH						= 'ssmanager/ssmanager_images/image_width';
	const XML_PATH_IMAGE_HEIGHT						= 'ssmanager/ssmanager_images/image_height';

	const XML_PATH_GENERATE_SWATCH_IMAGES			= 'ssmanager/ssmanager_images/generate_swatch_images';
	const XML_PATH_SWATCH_WIDTH						= 'ssmanager/ssmanager_images/swatch_width';
	const XML_PATH_SWATCH_HEIGHT					= 'ssmanager/ssmanager_images/swatch_height';

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
		return Mage::getStoreConfig(self::XML_PATH_API_BASE_URL);
	}

	public function getApiSecretKey()
	{
		return Mage::getStoreConfig(self::XML_PATH_API_SECRET_KEY);
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


}
