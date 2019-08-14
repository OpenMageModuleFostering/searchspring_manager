<?php

// If a SearchSpring account has been linked to the default level configs ...
$feedId = getConfigValue($this, SearchSpring_Manager_Model_Config::PATH_API_FEED_ID);
$siteId = getConfigValue($this, SearchSpring_Manager_Model_Config::PATH_API_SITE_ID);
$secret = getConfigValue($this, SearchSpring_Manager_Model_Config::PATH_API_SECRET_KEY);
if ($feedId && $siteId && $secret) {
	// ...then we need to move them down to a store specific config
	// We'll assume that the default store is the intended store to link
	$storeId = Mage::app()->getAnyStoreView()->getId();

	$this->setConfigData(   SearchSpring_Manager_Model_Config::PATH_API_FEED_ID, $feedId, 'stores', $storeId);
	$this->setConfigData(   SearchSpring_Manager_Model_Config::PATH_API_SITE_ID, $siteId, 'stores', $storeId);
	$this->setConfigData(SearchSpring_Manager_Model_Config::PATH_API_SECRET_KEY, $secret, 'stores', $storeId);

	// Just in case it's different, pull and persist the auth method config for the specific store
	$method = Mage::getStoreConfig(SearchSpring_Manager_Model_Config::PATH_API_AUTHENTICATION_METHOD, $storeId);
	$this->setConfigData(SearchSpring_Manager_Model_Config::PATH_API_AUTHENTICATION_METHOD, $method, 'stores', $storeId);

}

// Then remove the API store specific configs from the other scopes (other than stores)
$this->deleteConfigData(SearchSpring_Manager_Model_Config::PATH_API_FEED_ID, 'default');
$this->deleteConfigData(SearchSpring_Manager_Model_Config::PATH_API_FEED_ID, 'websites');
$this->deleteConfigData(SearchSpring_Manager_Model_Config::PATH_API_SITE_ID, 'default');
$this->deleteConfigData(SearchSpring_Manager_Model_Config::PATH_API_SITE_ID, 'websites');
$this->deleteConfigData(SearchSpring_Manager_Model_Config::PATH_API_SECRET_KEY, 'default');
$this->deleteConfigData(SearchSpring_Manager_Model_Config::PATH_API_SECRET_KEY, 'websites');
$this->deleteConfigData(SearchSpring_Manager_Model_Config::PATH_API_AUTHENTICATION_METHOD, 'default');
$this->deleteConfigData(SearchSpring_Manager_Model_Config::PATH_API_AUTHENTICATION_METHOD, 'websites');


// There was a bug earlier that would post extra uneeded configs, remove all of them...
$this->deleteConfigData('ssmanager/ssmanager_setup/username');
$this->deleteConfigData('ssmanager/ssmanager_setup/password');
$this->deleteConfigData('ssmanager/ssmanager_setup/authentication_method');
$this->deleteConfigData('ssmanager/ssmanager_setup/website');
$this->deleteConfigData('ssmanager/ssmanager_setup/feed');
$this->deleteConfigData('ssmanager/ssmanager_api/base_url');


// Reinitialize cache for the configuration
Mage::getConfig()->reinit();


// If data was migrated to a specific store...
if (isset($storeId)) {
	// Register just the live indexing URL for this SearchSpring account
	$hlp = Mage::helper('searchspring_manager');
	$whlp = Mage::helper('searchspring_manager/webservice');
	$feedId = $hlp->getApiFeedId($storeId);
	$creds = $hlp->getApiCredentials($storeId);
	$whlp->registerMageAPIUrls(
		$feedId, $creds,
		null,                                 // Feed
		null,                                 // Batch
		$hlp->getMageAPIUrlProduct($storeId)  // Live
	);
}


// Unfortunately, there's no way to get a config value directly from the scope you're looking for. So we need to query the data ourself.
function getConfigValue($installer, $path, $scope = 'default', $scopeId = 0)
{
	$adapter = $installer->getConnection();
	$select = $adapter->select();
	$select->from($installer->getTable('core/config_data'));
	$select->where('path = ?', $path);
	$select->where('scope = ?', $scope);
	$select->where('scope_id = ?', $scopeId);

	$data = $adapter->fetchRow($select);

	if (empty($data) || !is_array($data)) {
		return false;
	}

	if (!isset($data['value'])) {
		return false;
	}

	return $data['value'];
}


