<?php
/**
 * File PricingFactory.php
 *
 * @copyright B7 Interactive, LLC. All Rights Reserved.
 */

/**
 * Class SearchSpring_Manager_Factory_PricingFactory
 *
 * Creates a pricing strategy.
 *
 * @author Nate Brunette <nate@b7interactive.com>
 */
class SearchSpring_Manager_Factory_PricingFactory
{
	/**
	 * Creates a pricing strategy based on product type
	 *
	 * Throws an exception if it can't properly resolve the type.
	 *
	 * @param Mage_Catalog_Model_Product $product
	 *
	 * @return SearchSpring_Manager_Strategy_Pricing_Strategy
	 * @throws UnexpectedValueException
	 */
	static public function make(Mage_Catalog_Model_Product $product)
	{
		$productType = $product->getTypeId();

		switch($productType) {
			case Mage_Catalog_Model_Product_Type::TYPE_SIMPLE:
				$strategy = new SearchSpring_Manager_Strategy_Pricing_SimpleStrategy($product);
				break;
			case Mage_Catalog_Model_Product_Type::TYPE_GROUPED:
				$strategy = new SearchSpring_Manager_Strategy_Pricing_GroupedStrategy($product);
				break;
			case Mage_Catalog_Model_Product_Type::TYPE_CONFIGURABLE:
				$strategy = new SearchSpring_Manager_Strategy_Pricing_ConfigurableStrategy($product);
				break;
			case Mage_Catalog_Model_Product_Type::TYPE_BUNDLE:
				$strategy = new SearchSpring_Manager_Strategy_Pricing_BundleStrategy($product);
				break;
			default:
				$strategy = new SearchSpring_Manager_Strategy_Pricing_DefaultStrategy($product);
				break;
		}

		return $strategy;
	}
}
