<?php
/**
 * Observer.php
 *
 * @copyright B7 Interactive, LLC. All Rights Reserved.
 */

/**
 * Class SearchSpring_Manager_Model_Observer
 *
 * @author Jake Shelby <jake@b7interactive.com>
 */
abstract class SearchSpring_Manager_Model_Observer
{

    protected function notifyAdminUser($message)
	{
		// Only if we're in the admin panel
		if (Mage::app()->getStore()->isAdmin()) {
			$session = Mage::getSingleton('adminhtml/session');
			$session->addWarning($message);
		}
		return $this;
	}

}
