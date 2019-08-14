<?php
/**
 * ApiFactory.php
 *
 * @copyright B7 Interactive, LLC. All Rights Reserved.
 */

/**
 * Class SearchSpring_Manager_Factory_ApiFactory
 *
 * Create a SearchSpring api request object
 *
 * @author Nate Brunette <nate@b7interactive.com>
 */
class SearchSpring_Manager_Factory_ApiFactory
{
	/**
	 * Make the object
	 *
	 * Pulls in config values from the Magento configuration. Uses the Varien curl adapter.
	 *
	 * @param $type string The type of API to build (index or search)
	 *
	 * @return SearchSpring_Manager_Service_SearchSpring_IndexingApiAdapter
	 *
	 * @throws UnexpectedValueException
	 */
	public function make($type)
	{

		$baseUrl = Mage::helper('searchspring_manager')->getApiBaseUrl();
		$siteId = Mage::helper('searchspring_manager')->getApiSiteId();
		$secretKey = Mage::helper('searchspring_manager')->getApiSecretKey();

		if (empty($baseUrl)) {
			throw new UnexpectedValueException('SearchSpring: Base URL must be set');
		}

		if (empty($siteId)) {
			throw new UnexpectedValueException('SearchSpring: Site ID must be set');
		}

		if (empty($secretKey)) {
			throw new UnexpectedValueException('SearchSpring: Secret key must be set');
		}

		$apiErrorHandler = new SearchSpring_Manager_Handler_ApiErrorHandler();

		$client = new Zend_Http_Client();

		$client->setConfig(array(
			'maxredirects' => 0,
			'timeout' => 15,
			'keepalive' => true
		));

		if($type == 'index') {
			$client->setAuth($siteId, $secretKey);

			$api = new SearchSpring_Manager_Service_SearchSpring_IndexingApiAdapter(
				$apiErrorHandler,
				$client,
				$baseUrl
			);
		} else {
			$api = new SearchSpring_Manager_Service_SearchSpring_SearchApiAdapter(
				$apiErrorHandler,
				$client,
				$baseUrl
			);
		}

		return $api;
	}
}
