<?php
/**
 * SetImages.php
 *
 * @copyright B7 Interactive, LLC. All Rights Reserved.
 */

/**
 * Class SearchSpring_Manager_Operation_Product_SetImages
 *
 * Add product images to the feed
 *
 * @author James Bathgate <james@b7interactive.com>
 */
class SearchSpring_Manager_Operation_Product_SetImages extends SearchSpring_Manager_Operation_Product
{
	const DEFAULT_IMAGE_WIDTH = 200;
	const DEFAULT_IMAGE_HEIGHT = 200;

	const FEED_IMAGE_URL = 'image_url';
	const FEED_THUMBNAIL_URL = 'thumbnail_url';
	const FEED_CACHED_THUMBNAIL_URL = 'cached_thumbnail_url';

	/**
	 * Url path for product images
	 */
	const PREFIX_MEDIA_PRODUCT = 'catalog/product';

	protected $_mediaBaseUrl;

	protected $_imageHeight;
	protected $_imageWidth;

	
	public function __construct(
		SearchSpring_Manager_String_Sanitizer $sanitizer,
		SearchSpring_Manager_Entity_RecordsCollection $records,
		array $parameters = array()
	) {
		parent::__construct($sanitizer, $records, $parameters);

		if(Mage::helper('searchspring_manager')->isCacheImagesEnabled()) {
			$this->_imageWidth = Mage::helper('searchspring_manager')->getImageWidth();
			$this->_imageHeight = Mage::helper('searchspring_manager')->getImageHeight();

			if (empty($this->_imageWidth)) {
				if (!empty($this->_imageHeight)) {
					$this->_imageWidth = $this->_imageHeight;
				} else {
					$this->_imageWidth = self::DEFAULT_IMAGE_WIDTH;
					$this->_imageHeight = self::DEFAULT_IMAGE_HEIGHT;
				}
			} else if (empty($this->_imageHeight)) {
				if (!empty($this->_imageWidth)) {
					$this->_imageHeight = $this->_imageWidth;
				} else {
					$this->_imageHeight = self::DEFAULT_IMAGE_HEIGHT;
				}
			}

		}
	}
	
	/**
	 * Set magento product options to the feed
	 *
	 * @param Mage_Catalog_Model_Product $product
	 *
	 * @return $this
	 */
	public function perform(Mage_Catalog_Model_Product $product)
	{
		$mediaBaseUrl = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA);

		$this->getRecords()->set(self::FEED_IMAGE_URL, $mediaBaseUrl . self::PREFIX_MEDIA_PRODUCT . $product->getData('image'));
		$this->getRecords()->set(self::FEED_THUMBNAIL_URL, $mediaBaseUrl . self::PREFIX_MEDIA_PRODUCT . $product->getData('thumbnail'));

		/** @var Mage_Catalog_Helper_Image $imageHelper */
		$imageHelper = Mage::helper('catalog/image');

		if(Mage::helper('searchspring_manager')->isCacheImagesEnabled()) {
			$this->getRecords()->set(
				self::FEED_CACHED_THUMBNAIL_URL,
				(string) $imageHelper->init($product, 'image')->resize($this->_imageWidth, $this->_imageHeight)
			);
		}

		return $this;
	}
}
