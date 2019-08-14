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
 * @author Jake Shelby <jake@b7interactive.com>
 */

class SearchSpring_Manager_Block_Adminhtml_System_Config_Fieldset_Setup extends Mage_Adminhtml_Block_Abstract implements Varien_Data_Form_Element_Renderer_Interface
{
	const TEMPLATE_SETUP		= 'searchspring/manager/system/config/fieldset/setup.phtml';
	const TEMPLATE_CHOOSE_STORE	= 'searchspring/manager/system/config/fieldset/setup_scope.phtml';

	protected $_configModel;

	protected function _construct()
	{
		parent::_construct();

		$this->_initConfigModel();

		if ($this->isInStoreScope()) {
			$this->setTemplate(self::TEMPLATE_SETUP);
		} else {
			$this->setTemplate(self::TEMPLATE_CHOOSE_STORE);
		}
	}

	protected function _initConfigModel()
	{
		// In newer versions, this data is available in the adminhtml/config_data singleton ... however older versions make use pull the params directly from the request
		$section = $this->getRequest()->getParam('section');
		$website = $this->getRequest()->getParam('website');
		$store   = $this->getRequest()->getParam('store');

		$this->_configModel = Mage::getModel('adminhtml/config_data')
			->setSection($section)
			->setWebsite($website)
			->setStore($store)
		;
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

	public function isAnyStoreSetup() {
		$hlp = Mage::helper('searchspring_manager');
		foreach($this->getAllStores() as $store) {
			if ($hlp->isStoreSetup($store)) {
				return true;
			}
		}
		return false;
	}

	public function isModuleEnabled($moduleName) {
		$hlp = Mage::helper('searchspring_manager');
		return $hlp->isModuleEnabled($moduleName);
	}

	public function isSetup($store = null) {
		$hlp = Mage::helper('searchspring_manager');
		$isSetup = false;

		try {
			// If no store was passed
			if (!$store) {
				if (!$this->isInStoreScope()) {
					// If we're not in the store scope
					return $this->isAnyStoreSetup();
				}
				$store = $this->_configModel->getStore();
			}

			$isSetup = $hlp->isStoreSetup($store) && $hlp->verifySetupWithSearchSpring($store);
		} catch (Exception $e) {
			// TODO: figure out better way to report this to the user
		}

		return $isSetup;
	}

	public function isInDefaultScope() {
		return (
			$this->_configModel->getWebsite() == null &&
			$this->_configModel->getStore() == null
		);
	}

	public function isInWebsiteScope() {
		return (
			$this->_configModel->getWebsite() != null &&
			$this->_configModel->getStore() == null
		);
	}

	public function isInStoreScope() {
		return ($this->_configModel->getStore() != null);
	}

	public function getWebsites() {
		$websites = array();
		$storeModel = Mage::getSingleton('adminhtml/system_store');
		foreach($storeModel->getWebsiteCollection() as $website) {
			$websites[$website->getId()] = $website;
		}
		return $websites;
	}

	public function getGroups($website = null) {
		// If no website was passed, and we're in the context
		// of a website, only return stores under that website
		if (!$website && $this->isInWebsiteScope()) {
			$code = $this->_configModel->getWebsite();
			$websiteId = Mage::app()->getWebsite($code)->getId();
		} else if (!$website) {
			$websiteId = null;
		} else {
			$websiteId = $website->getId();
		}

		$groups = array();
		$storeModel = Mage::getSingleton('adminhtml/system_store');
		foreach($storeModel->getGroupCollection() as $group) {
			if (!$websiteId || $group->getWebsiteId() == $websiteId) {
				$groups[$group->getId()] = $group;
			}
		}
		return $groups;
	}

	public function getStores($group) {
		$stores = array();
		$storeModel = Mage::getSingleton('adminhtml/system_store');
		foreach($storeModel->getStoreCollection() as $store) {
			if ($store->getGroupId() == $group->getId()) {
				$stores[$store->getId()] = $store;
			}
		}

		return $stores;
	}

	public function getAllStores() {
		$stores = array();
		$storeModel = Mage::getSingleton('adminhtml/system_store');
		foreach($storeModel->getStoreCollection() as $store) {
			$stores[$store->getId()] = $store;
		}
		return $stores;
	}

	public function getWebsiteLabel($website) {
		return $website->getName();
	}

	public function getGroupLabel($group) {
		return $group->getName();
	}

	public function getStoreLabel($store) {
		return $store->getName();
	}

	public function getStoreUrl($store) {
		// There is no central function for
		// doing this, so we just build it
		// ourselves with these three params
		$url = Mage::getModel('adminhtml/url');
		$website = $store->getWebsite();
		$section = $this->_configModel->getSection();
		return $url->getUrl('*/*/*', array(
			'section' => $section,
			'website' => $website->getCode(),
			'store' => $store->getCode())
		);
	}

	public function getAuthMethods() {
		$oahlp = Mage::helper('searchspring_manager/oauth');
		$methods = array(
			array(
				'value' => 'simple',
				'label' => 'Simple Authentication'
			)
		);

		if ($oahlp->isSupported()) {
			$methods[] = array(
				'value' => 'oauth',
				'label' => 'OAuth API'
			);
		}

		return $methods;
	}

}
