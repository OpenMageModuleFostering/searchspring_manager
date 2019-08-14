<?php
/**
 * File ProductValidator.php
 *
 * @copyright B7 Interactive, LLC. All Rights Reserved.
 */

/**
 * Class SearchSpring_Manager_Validator_ProductValidator
 *
 * @author Nate Brunette <nate@b7interactive.com>
 */
class SearchSpring_Manager_Validator_ProductValidator implements Zend_Validate_Interface
{
	/**
	 * SearchSpring configuration
	 *
	 * @var SearchSpring_Manager_Model_Config
	 */
	protected $config;

	/**
	 * An array of messages that details why the validator failed
	 *
	 * @var array $messages
	 */
	private $messages = array();

	/**
	 * Constructor
	 */
	public function __construct(SearchSpring_Manager_Model_Config $config)
	{
		$this->config = $config;
	}

	/**
	 * Determines if we should delete this product from the SearchSpring index
	 *
	 * @param Mage_Catalog_Model_Product $product
	 * @param SearchSpring_Manager_Strategy_PricingStrategy $pricing
	 *
	 * @return bool
	 * @throws UnexpectedValueException
	 */
	public function shouldDelete(
		Mage_Catalog_Model_Product $product,
		SearchSpring_Manager_Strategy_PricingStrategy $pricing
	) {
		$status = (int)$product->getStatus();
		$origStatus = (int)$product->getOrigData('status');
		$visibility = (int)$product->getData('visibility');
		$origVisibility = (int)$product->getOrigData('visibility');

		// if product became disabled
		if (
			$status === Mage_Catalog_Model_Product_Status::STATUS_DISABLED
			&& $origStatus === Mage_Catalog_Model_Product_Status::STATUS_ENABLED
		) {
			return true;
		}

		// if product became not visible
		if (
			$visibility === Mage_Catalog_Model_Product_Visibility::VISIBILITY_NOT_VISIBLE
			&& $origVisibility !== Mage_Catalog_Model_Product_Visibility::VISIBILITY_NOT_VISIBLE
		) {
			return true;
		}

		// if product is out of stock
		$displayOos = $this->config->isOutOfStockIndexingEnabled($product->getStoreId());
		$stockItem = $product->getStockItem();

		if (!$stockItem->getIsInStock() && !$displayOos) {
			return true;
		}

		if (null === $pricing->getNormalPrice() || null === $pricing->getTierPrice() || null === $pricing->getSalePrice()) {
			throw new UnexpectedValueException('Pricing calculations must be run before we can check if product should be deleted');
		}

		// if product has a zero price
		$displayZeroPrice = $this->config->isZeroPriceIndexingEnabled($product->getStoreId());
		$productHaZeroPrice = 0 == $pricing->getNormalPrice() && 0 == $pricing->getTierPrice() && 0 == $pricing->getSalePrice();
		if ($productHaZeroPrice && !$displayZeroPrice) {
			return true;
		}

		return false;
	}

	/**
	 * {@inheritdoc}
	 */
	public function isValid($product)
	{
		$this->messages = array();

		/** @var Mage_Catalog_Model_Product $product */
		if (!$product instanceof Mage_Catalog_Model_Product) {
			$this->messages[] = 'Validator expected a Mage_Catalog_Model_Product.';

			return false;
		}

		// product must be enabled
		if ((int)$product->getData('status') !== Mage_Catalog_Model_Product_Status::STATUS_ENABLED) {
			$this->messages[] = 'Product is not enabled';
		}

		// product must be visible in either catalog and search
		if (!$product->isVisibleInSiteVisibility()) {
			$this->messages[] = 'Product must be visible in either catalog and search';
		}

		// if we have errors, return false
		if (!empty($this->messages)) {
			return false;
		}

		return true;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getMessages()
	{
		return $this->messages;
	}
}
