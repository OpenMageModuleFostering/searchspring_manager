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

		if($this->getProduct()->getPriceType() == Mage_Bundle_Model_Product_Price::PRICE_TYPE_FIXED) {
			$product = $this->getProduct();
			$this->setNormalPrice($product->getPrice());
			$this->setTierPrice($this->getLowestTierPrice($product));
			$this->setSalePrice($product->getFinalPrice());
		} else {
			/** @var Mage_Bundle_Model_Product_Type $typeInstance */
			$typeInstance = $this->getProduct()->getTypeInstance(true);

			$totalRegularPrice = 0;
			$totalTierPrice = 0;
			$totalSalePrice = 0;

			$priceModel = $this->getProduct()->getPriceModel();

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

						// // The lowest price may also be in the "bundled" price so we'll add it to the prices
						// $bundlePrice = $priceModel->getSelectionPreFinalPrice($this->getProduct(), $product, $product->getSelectionQty());
						// $regularPrices[] = $bundlePrice;
						// $tierPrices[] = $bundlePrice;
						// $salePrices[] = $bundlePrice;
					}
				}

				$totalRegularPrice += $this->findMinPrice($regularPrices);
				$totalTierPrice += $this->findMinPrice($tierPrices);
				$totalSalePrice += $this->findMinPrice($salePrices);
			}

			$this->setNormalPrice($totalRegularPrice);
			$this->setTierPrice($totalTierPrice);
			$this->setSalePrice($totalSalePrice);
		}

		return $this;
	}

}
