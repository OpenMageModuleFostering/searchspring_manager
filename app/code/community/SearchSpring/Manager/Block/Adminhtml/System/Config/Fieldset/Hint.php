<?php
/**
 * Hint.php
 *
 * @copyright B7 Interactive, LLC. All Rights Reserved.
 */

/**
 * Class SearchSpring_Manager_Block_Adminhtml_System_Config_Fieldset_Hint
 *
 * Display hint above SearchSpring Manager Settings
 *
 * @author James Bathgate <james@b7interactive.com>
 */

class SearchSpring_Manager_Block_Adminhtml_System_Config_Fieldset_Hint extends SearchSpring_Manager_Block_Adminhtml_System_Config_Fieldset_Setup
{
	const TEMPLATE_HINT = 'searchspring/manager/system/config/fieldset/hint.phtml';

	public function __construct() {
		parent::__construct();

		$this->setTemplate(self::TEMPLATE_HINT);

		return $this;
	}


	public function getVersion() {
		return (string) Mage::helper('searchspring_manager')->getVersion();
	}

	public function getModuleUUID() {
		return Mage::helper('searchspring_manager')->getUUID();
	}
}
