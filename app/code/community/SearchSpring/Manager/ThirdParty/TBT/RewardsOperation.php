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

		$value = $product->getEarnablePoints();

		if (is_array($value)) {
			$value = current($value);
		}

		$this->getRecords()->add('TBT_Rewards_earnable_points', $value);

		return $this;
	}

}
