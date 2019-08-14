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

class SearchSpring_Manager_Block_Adminhtml_System_Config_Fieldset_Hint extends Mage_Adminhtml_Block_Abstract implements Varien_Data_Form_Element_Renderer_Interface
{
	protected $_template = 'searchspring/manager/system/config/fieldset/hint.phtml';

	public function __construct() {
		parent::__construct();
		return $this;
	}

	/**
	 * Render fieldset html
	 *
	 * @param Varien_Data_Form_Element_Abstract $element
	 * @return string
	 */
	public function render(Varien_Data_Form_Element_Abstract $element)
	{
		return $this->toHtml();
	}


	public function isSetup() {
		$hlp = Mage::helper('searchspring_manager');
		return $hlp->registerMagentoAPIAuthenticationWithSearchSpring(true);
	}

	public function getVersion() {
		return (string) Mage::helper('searchspring_manager')->getVersion();
	}

	public function getModuleUUID() {
		return Mage::helper('searchspring_manager')->getUUID();
	}
}
