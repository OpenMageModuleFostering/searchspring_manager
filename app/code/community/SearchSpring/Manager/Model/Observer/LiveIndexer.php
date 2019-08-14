<?php
/**
 * LiveIndexer.php
 *
 * @copyright B7 Interactive, LLC. All Rights Reserved.
 */

/**
 * Class SearchSpring_Manager_Model_Observer_LiveIndexer
 *
 * @author Nate Brunette <nate@b7interactive.com>
 */
abstract class SearchSpring_Manager_Model_Observer_LiveIndexer
{

	/**
	 * Checks if live indexing is enabled
	 *
	 * @return bool
	 */
	protected function isEnabled()
	{
		return Mage::helper('searchspring_manager')->isLiveIndexingEnabled();
	}

}
