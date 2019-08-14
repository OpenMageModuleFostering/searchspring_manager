<?php
/**
 * File GroupedStrategy.php
 *
 * @copyright B7 Interactive, LLC. All Rights Reserved.
 */

/**
 * Class SearchSpring_Manager_Strategy_Pricing_GroupedStrategy
 *
 * Calculate prices for grouped products
 *
 * @author Nate Brunette <nate@b7interactive.com>
 */
class SearchSpring_Manager_Strategy_Pricing_GroupedStrategy extends SearchSpring_Manager_Strategy_Pricing_Strategy
{
	/**
	 * {@inheritdoc}
	 */
	public function calculatePrices()
	{
		/** @var Mage_Catalog_Model_Product_Type_Grouped $typeInstance */
		$typeInstance = $this->getProduct()->getTypeInstance(true);
		$associated = $typeInstance->getAssociatedProducts($this->getProduct());

		$regularPrices = array();
		$tierPrices = array();
		$salePrices = array();

		/** @var Mage_Catalog_Model_Product $product */
		foreach($associated as $product) {
			$regularPrice = (double)$product->getPrice();
			$tierPrice = (double)$this->getLowestTierPrice($product);
			$salePrice = (double)$product->getFinalPrice();

			$regularPrices[] = $regularPrice;
			$tierPrices[] = $tierPrice;
			$salePrices[] = $salePrice;
		}

		$this->setNormalPrice($this->findMinPrice($regularPrices));
		$this->setTierPrice($this->findMinPrice($tierPrices));
		$this->setSalePrice($this->findMinPrice($salePrices));

		return $this;
	}
}
