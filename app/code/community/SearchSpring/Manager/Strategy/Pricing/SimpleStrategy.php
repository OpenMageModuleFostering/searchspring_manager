<?php
/**
 * File SimpleStrategy.php
 *
 * @copyright B7 Interactive, LLC. All Rights Reserved.
 */

/**
 * Class SearchSpring_Manager_Strategy_Pricing_SimpleStrategy
 *
 * Calculate prices for grouped products
 *
 * @author Nate Brunette <nate@b7interactive.com>
 */
class SearchSpring_Manager_Strategy_Pricing_SimpleStrategy extends SearchSpring_Manager_Strategy_Pricing_Strategy
{
	/**
	 * {@inheritdoc}
	 */
	public function calculatePrices()
	{
		$product = $this->getProduct();
		$this->setNormalPrice($product->getPrice());
		$this->setTierPrice($this->getLowestTierPrice($product));
		$this->setSalePrice($product->getFinalPrice());

		return $this;
	}
}
