<?php
/**
 * ApiFactory.php
 *
 * @copyright B7 Interactive, LLC. All Rights Reserved.
 */

/**
 * Class SearchSpring_Manager_Factory_ApiFactory
 *
 * Create a SearchSpring api request objects
 *
 * @author Nate Brunette <nate@b7interactive.com>
 * @author Jake Shelby <jake@b7interactive.com>
 */
class SearchSpring_Manager_Factory_ApiFactory
{

	/**
	 * Make indexing adapter for a specific store
	 *
	 * Pulls in config values from the Magento configuration, based
	 * on specified store. Uses the Zend Http Client adapter.
	 *
	 * @param $store mixed Mage store id|code|object 
	 *
	 * @return SearchSpring_Manager_Service_SearchSpring_IndexingApiAdapter
	 *
	 * @throws Exception
	 */
	public function makeIndexingAdapter($store)
	{
		$hlp = Mage::helper('searchspring_manager');

		$feedId = $hlp->getApiFeedId($store);
		$creds = $hlp->getApiCredentials($store);

		if (!$feedId) {
			$code = Mage::app()->getStore($store)->getCode();
			throw new Exception("Cannot create API adapter for store: $code; feed id is not configured.");
		}
		if (!$creds->isPopulated()) {
			$code = Mage::app()->getStore($store)->getCode();
			throw new Exception("Cannot create API adapter for store: $code; incomplete API credentials.");
		}

		$apiErrorHandler = new SearchSpring_Manager_Handler_ApiErrorHandler();

		$client = new Zend_Http_Client();

		$client->setConfig(array(
			'maxredirects' => 0,
			'timeout' => 15,
			'keepalive' => true
		));

		$client->setAuth($creds->getUsername(), $creds->getPassword());

		$api = new SearchSpring_Manager_Service_SearchSpring_IndexingApiAdapter(
			$apiErrorHandler,
			$client,
			$this->getApiBaseUrl(),
			$feedId
		);

		return $api;
	}

	/**
	 * Make search adapter
	 *
	 * Creates an instance of a search adapter. Uses the Zend Http Client adapter
	 *
	 * @return SearchSpring_Manager_Service_SearchSpring_SearchApiAdapter
	 */
	public function makeSearchAdapter()
	{
		$apiErrorHandler = new SearchSpring_Manager_Handler_ApiErrorHandler();

		$client = new Zend_Http_Client();
		$client->setConfig(array(
			'maxredirects' => 0,
			'timeout' => 15,
			'keepalive' => true
		));

		$api = new SearchSpring_Manager_Service_SearchSpring_SearchApiAdapter(
			$apiErrorHandler,
			$client,
			$this->getApiBaseUrl()
		);

		return $api;
	}

	public function getApiBaseUrl()
	{
		return Mage::helper('searchspring_manager')->getApiBaseUrl();
	}

}
