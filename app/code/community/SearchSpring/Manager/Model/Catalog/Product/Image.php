<?php
/**
 * Image.php
 *
 * @copyright B7 Interactive, LLC. All Rights Reserved.
 */

/**
 * Class SearchSpring_Manager_Model_Catalog_Product_Image
 *
 * Extension of core Catalog Product Image model, to help optimize for speed
 *
 * @author Jake Shelby <jake@b7interactive.com>
 */
class SearchSpring_Manager_Model_Catalog_Product_Image extends Mage_Catalog_Model_Product_Image
{

	protected $_skipMemoryCheck = false;

	protected function _checkMemory($file = null)
	{
		if ($this->_skipMemoryCheck) {
			return true;
		}
		return parent::_checkMemory($file);
	}

	public function skipMemoryCheck($flag = true)
	{
		$this->_skipMemoryCheck = $flag;
		return $this;
	}

	public function getSkipMemoryCheck()
	{
		return $this->_skipMemoryCheck;
	}

}
