<?php
/**
 * Setup.php
 *
 * @copyright B7 Interactive, LLC. All Rights Reserved.
 */

/**
 * Class SearchSpring_Manager_Block_Adminhtml_System_Config_Fieldset_Setup
 *
 * Display connection setup helper
 *
 * @author James Bathgate <james@b7interactive.com>
 */

class SearchSpring_Manager_Block_Adminhtml_System_Config_Fieldset_Setup extends Mage_Adminhtml_Block_Abstract implements Varien_Data_Form_Element_Renderer_Interface
{
	protected $_template = 'searchspring/manager/system/config/fieldset/setup.phtml';

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

	public function _prepareLayout() {
		$head = $this->getLayout()->getBlock('head');
		$head->addItem('js', 'searchspring/jquery-1.11.1.min.js');
		$head->addItem('js', 'searchspring/setup.js');

		parent::_prepareLayout();
		return $this;
	}

	public function isSetup() {
		$hlp = Mage::helper('searchspring_manager');
		return $hlp->registerMagentoAPIAuthenticationWithSearchSpring(true);
	}

}
