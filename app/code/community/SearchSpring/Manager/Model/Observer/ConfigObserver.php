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
class SearchSpring_Manager_Model_Observer_ConfigObserver {

	const NEW_CONNECTION_SETUP_PARAM = 'searchspring_new_connection_fl';

	public function afterSystemConfigSectionChanged(Varien_Event_Observer $event) {

		$hlp = Mage::helper('core');

		// Make sure we are in the admin panel
		if (!Mage::app()->getStore()->isAdmin()) {
			return;
		}

		if ($this->hasNewConnectionBeenSetup()) {

			// TODO - should we require admin permissions here

			try {

				// Initialize Resources needed for auth method
				$this->_initializeAuthMethod();

				// Register Auth Method With Search Spring API
				$this->_registerAuthMethodWithSearchSpring();

			} catch (Exception $e) {

				Mage::logException($e);

				$message = $hlp->__('There was a problem while attempting to setup your SearchSpring account [E938]');
				$session = Mage::getSingleton('adminhtml/session');
				$session->addWarning($message);

			}

		}

	}

	public function hasNewConnectionBeenSetup() {
		$param = Mage::app()->getRequest()->getParam(self::NEW_CONNECTION_SETUP_PARAM);
		return (bool) $param;
	}

	protected function _initializeAuthMethod() {

		$hlp = Mage::helper('searchspring_manager');
		switch ($hlp->getAuthenticationMethod()) {

			case SearchSpring_Manager_Helper_Data::AUTH_METHOD_SIMPLE:
				// Nothing to initialize for this auth method
				break;

			case SearchSpring_Manager_Helper_Data::AUTH_METHOD_OAUTH:
				// Initialize oAuth Resources for API access
				$this->_initializeOAuthResources();
				break;

		}

	}

	protected function _initializeOAuthResources() {
		$oahlp = Mage::helper('searchspring_manager/oauth');
		$oahlp->ensureOAuthResourcesInitialized();
	}	

	protected function _registerAuthMethodWithSearchSpring() {

		$success = Mage::helper('searchspring_manager')
			->registerMagentoAPIAuthenticationWithSearchSpring();

		if (!$success) {
			$hlp = Mage::helper('core');
			$message = $hlp->__('There was a problem while attempting to setup your SearchSpring account [E939]');
			$session = Mage::getSingleton('adminhtml/session');
			$session->addWarning($message);
		}

	}

}
