<?php
/**
 * ConfigObserver.php
 *
 * @copyright B7 Interactive, LLC. All Rights Reserved.
 */

/**
 * Class SearchSpring_Manager_Model_Observer_ConfigObserver
 *
 * On a category change, trigger one of these methods
 *
 */
class SearchSpring_Manager_Model_Observer_ConfigObserver extends SearchSpring_Manager_Model_Observer
{

	const NEW_CONNECTION_SETUP_PARAM = 'searchspring_new_connection_fl';

	public function hasNewConnectionBeenSetup() {
		$param = Mage::app()->getRequest()->getParam(self::NEW_CONNECTION_SETUP_PARAM);
		return (bool) $param;
	}

	public function getStore() {
		// Get the param for store code, we'll always be in the context of the config section
		if ($store = Mage::app()->getRequest()->getParam('store')) {
			return Mage::app()->getStore($store);
		}
		return null;
	}

	public function afterSystemConfigSectionChanged(Varien_Event_Observer $event) {

		$hlp = Mage::helper('core');

		// Make sure we are in the admin panel
		if (!Mage::app()->getStore()->isAdmin()) {
			return;
		}

		// Get the store context configuration being edited
		$store = $this->getStore();

		if ($this->hasNewConnectionBeenSetup() && $store) {

			// TODO - should we require admin permissions here

			try {

				// Initialize Resources needed for auth method
				$this->_initializeAuthMethod($store);

				// Register store/authType specific urls with Search Spring
				$this->_registerWithSearchSpring($store);

			} catch (Exception $e) {

				Mage::logException($e);
				$this->notifyAdminUser('There was a problem while attempting to setup your SearchSpring account [E938]');

			}

		}

	}

	protected function _initializeAuthMethod($store) {
		$hlp = Mage::helper('searchspring_manager');

		switch ($hlp->getAuthenticationMethod($store)) {

			case SearchSpring_Manager_Model_Config::AUTH_METHOD_SIMPLE:
				// Nothing to initialize for this auth method
				break;

			case SearchSpring_Manager_Model_Config::AUTH_METHOD_OAUTH:
				// Initialize oAuth Resources for API access
				$this->_initializeOAuthResources();
				break;
		}
	}

	protected function _initializeOAuthResources() {
		$oahlp = Mage::helper('searchspring_manager/oauth');
		$oahlp->ensureOAuthResourcesInitialized();
	}	

	protected function _registerWithSearchSpring($store) {

		$hlp = Mage::helper('searchspring_manager');

		// Register Store configuration With Search Spring
		$hlp->registerMagentoAPIWithSearchSpring($store);

	}

}
