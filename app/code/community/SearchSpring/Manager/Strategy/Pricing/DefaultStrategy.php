<?php
/**
 * File DefaultStrategy.php
 *
 * @copyright B7 Interactive, LLC. All Rights Reserved.
 */

/**
 * Class SearchSpring_Manager_Strategy_Pricing_DefaultStrategy
 *
 * Calculate prices for products
 *
 * @author James <james@b7interactive.com>
 */
class SearchSpring_Manager_Strategy_Pricing_DefaultStrategy extends SearchSpring_Manager_Strategy_Pricing_Strategy
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
