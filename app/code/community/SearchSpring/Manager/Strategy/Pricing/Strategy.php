<?php
/**
 * File Strategy.php
 *
 * @copyright B7 Interactive, LLC. All Rights Reserved.
 */

/**
 * Class SearchSpring_Manager_Strategy_Pricing_Strategy
 *
 * Abstract class for pricing strategies.  Holds getters for price data.
 *
 * @todo This should probably be a separate class that just holds the data rather than an abstract with subclasses.
 *
 * @author Nate Brunette <nate@b7interactive.com>
 */
abstract class SearchSpring_Manager_Strategy_Pricing_Strategy implements SearchSpring_Manager_Strategy_PricingStrategy
{
	/**
	 * Magento product
	 *
	 * @var Mage_Catalog_Model_Product $product
	 */
	private $product;

	/**
	 * The regular price of the product
	 *
	 * @var double $regularPrice
	 */
	private $regularPrice;

	/**
	 * The price of the product including tier prices
	 *
	 * @var double $tierPrice
	 */
	private $tierPrice;

	/**
	 * The minimal price of product from all types
	 *
	 * @var double $salePrice
	 */
	private $salePrice;


	/**
	 * The price of the product from getFinalPrice()
	 *
	 * @var double $salePrice
	 */
	private $finalPrice;

	/**
	 * {@inheritdoc}
	 */
	public function __construct(Mage_Catalog_Model_Product $product)
	{
		$this->product = $product;
	}

	/**
	 * Get the product
	 *
	 * @return Mage_Catalog_Model_Product
	 */
	protected function getProduct()
	{
		return $this->product;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getNormalPrice()
	{
		$price = $this->formatPrice($this->regularPrice);

		return $price;
	}

	/**
	 * Set the regular price
	 *
	 * @param double $price
	 */
	protected function setNormalPrice($price)
	{
		$this->regularPrice = $price;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getTierPrice()
	{
		$minPrice = min($this->regularPrice, $this->tierPrice);
		$price = $this->formatPrice($minPrice);

		return $price;
	}

	/**
	 * Set the tier price
	 *
	 * @param double $price
	 */
	protected function setTierPrice($price)
	{
		$this->tierPrice = $price;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getSalePrice()
	{
		$minPrice = min($this->regularPrice, $this->tierPrice, $this->salePrice);
		$price = $this->formatPrice($minPrice);

		return $price;
	}

	/**
	 * Set the sale price
	 *
	 * @param double $price
	 */
	protected function setSalePrice($price)
	{
		$this->salePrice = $price;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getFinalPrice()
	{
		return $this->formatPrice($this->salePrice);
	}

	/**
	 * Find the minimum price in array
	 *
	 * Returns 0 if array if empty
	 *
	 * @param array $prices
	 *
	 * @return int
	 */
	protected function findMinPrice(array $prices)
	{
		if (empty($prices)) {
			return 0;
		}

		return min($prices);
	}

	/**
	 * Find the lowest tier price for product
	 *
	 * @param Mage_Catalog_Model_Product $product
	 *
	 * @return double
	 */
	protected function getLowestTierPrice(Mage_Catalog_Model_Product $product)
	{
		/** @var array $tierPrices */
		$tierPrices = $product->getTierPrice();

		if (empty($tierPrices)) {
			$price = (double)$product->getPrice();

			return $price;
		}

		$prices = array();
		foreach ($tierPrices as $tierPrice) {
			if ($tierPrice == 0) {
				continue;
			}

			$prices[] = (double)$tierPrice['price'];
		}

		$lowestPrice = min($prices);

		return $lowestPrice;
	}

	/**
	 * Format number to two decimals
	 *
	 * @param double $price
	 * @return double
	 */
	private function formatPrice($price)
	{
		$price = (double)$price;

		return $price;
	}
}
