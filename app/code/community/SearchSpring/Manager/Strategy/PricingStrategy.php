<?php
/**
 * File PricingStrategy.php
 *
 * @copyright B7 Interactive, LLC. All Rights Reserved.
 */

/**
 * Interface SearchSpring_Manager_Strategy_PricingStrategy
 *
 * @author Nate Brunette <nate@b7interactive.com>
 */
interface SearchSpring_Manager_Strategy_PricingStrategy
{
	/**
	 * Constructor
	 *
	 * @param Mage_Catalog_Model_Product $product
	 */
	public function __construct(Mage_Catalog_Model_Product $product);

	/**
	 * Calculate lowest prices
	 *
	 * @return self
	 */
	public function calculatePrices();

	/**
	 * Return the regular price
	 *
	 * @return double
	 */
	public function getNormalPrice();

	/**
	 * Return the lowest between regular price and tier price
	 *
	 * @return double
	 */
	public function getTierPrice();

	/**
	 * Return the lowest price between all kinds of prices
	 *
	 * @return double
	 */
	public function getSalePrice();

	/**
	 * Return the lowest price non-tier price
	 *
	 * @return double
	 */
	public function getFinalPrice();
}
