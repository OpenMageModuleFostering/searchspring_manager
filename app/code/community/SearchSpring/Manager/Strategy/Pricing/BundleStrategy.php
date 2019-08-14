<?php
/**
 * File BundleStrategy.php
 *
 * @copyright B7 Interactive, LLC. All Rights Reserved.
 */

/**
 * Class SearchSpring_Manager_Strategy_Pricing_BundleStrategy
 *
 * Calculate prices for bundled products
 *
 * @author Nate Brunette <nate@b7interactive.com>
 */
class SearchSpring_Manager_Strategy_Pricing_BundleStrategy extends SearchSpring_Manager_Strategy_Pricing_Strategy
{
	/**
	 * {@inheritdoc}
	 */
	public function calculatePrices()
	{
		/** @var Mage_Bundle_Model_Product_Type $typeInstance */
		$typeInstance = $this->getProduct()->getTypeInstance(true);

		$totalRegularPrice = 0;
		$totalTierPrice = 0;
		$totalSalePrice = 0;

		// set up bundle options
		$optionsIds = $typeInstance->getOptionsIds($this->getProduct());
		$selections = $typeInstance->getSelectionsCollection($optionsIds, $this->getProduct());
		$bundleOptions = $typeInstance->getOptionsByIds($optionsIds, $this->getProduct());
		$bundleOptions->appendSelections($selections);

		/** @var Mage_Bundle_Model_Option $bundleOption */
		foreach ($bundleOptions as $bundleOption) {
			// if it's not required, it doesn't count as part of the minimal price
			if (!$bundleOption->getRequired()) {
				continue;
			}

			$regularPrices = array();
			$tierPrices = array();
			$salePrices = array();

			$bundleOptionSelections = $bundleOption->getData('selections');
			if(
				is_array($bundleOptionSelections) ||
				$bundleOptionSelections instanceof Traversable
			) {
				foreach ($bundleOptionSelections as $product) {
					$regularPrice = (double)$product->getPrice();
					$tierPrice = (double)$this->getLowestTierPrice($product);
					$salePrice = (double)$product->getFinalPrice();

					$regularPrices[] = $regularPrice;
					$tierPrices[] = $tierPrice;
					$salePrices[] = $salePrice;
				}
			}

			$totalRegularPrice += $this->findMinPrice($regularPrices);
			$totalTierPrice += $this->findMinPrice($tierPrices);
			$totalSalePrice += $this->findMinPrice($salePrices);
		}

		$this->setNormalPrice($totalRegularPrice);
		$this->setTierPrice($totalTierPrice);
		$this->setSalePrice($totalSalePrice);

		return $this;
	}

}
