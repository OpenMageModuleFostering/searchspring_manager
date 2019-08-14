<?php
/**
 * RewardsOperation.php
 *
 * @copyright B7 Interactive, LLC. All Rights Reserved.
 */

/**
 * Class SearchSpring_Manager_ThirdParty_TBT_RewardsOperation
 *
 * Set Rewards Related Data
 *
 * @author Jake Shelby <jake@b7interactive.com>
 */
class SearchSpring_Manager_ThirdParty_TBT_RewardsOperation extends SearchSpring_Manager_Operation_Product
{

	public function perform(Mage_Catalog_Model_Product $product)
	{
		// This might look a little weird, but bear with me...

		// Make sure the base TBT product class exists, you never know...
		if (!class_exists('TBT_Rewards_Model_Catalog_Product')) {
			return $this;
		}

		// Create new product model, with Magento service locator, in case someone is overwriting
		$rewardsProduct = Mage::getModel('rewards/catalog_product', $product->getData());

		// Now we have a rewards product model, without having to touch the database again
		$points = $rewardsProduct->getEarnablePoints();

		// It should be returned as an array...
		if (is_array($points)) {
			$points = current($points);
		}

		$this->getRecords()->add('tbt_rewards_earnable_points', $points);

		return $this;
	}

}
