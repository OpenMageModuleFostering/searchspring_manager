<?php
/**
 * File ApiAdapter.php
 *
 * @copyright B7 Interactive, LLC. All Rights Reserved.
 */

/**
 * Class SearchSpring_Manager_Service_SearchSpring_ApiAdapter
 *
 * Adapter for the SearchSpring API
 *
 * @author James Bathgate <james@b7interactive.com>
 */
class SearchSpring_Manager_Service_SearchSpring_ApiAdapter
{

	/**
	 * Format for create the url endpoint
	 *
	 * [baseurl]/[method]
	 */
	const URL_FORMAT = '%s/%s';

	/**
	 * The curl adapter
	 *
	 * @var Varien_Http_Adapter_Curl $curl
	 */
	protected $curl;

	/**
	 * An API error handler if a request fails
	 *
	 * @var SearchSpring_Manager_Handler_ApiErrorHandler
	 */
	protected $errorHandler;

	/**
	 * The base url for the endpoint
	 *
	 * @var string $baseUrl
	 */
	protected $baseUrl;

	/**
	 * Constructor
	 *
	 * @param SearchSpring_Manager_Handler_ApiErrorHandler $errorHandler
	 * @param Varien_Http_Adapter_Curl $curl
	 * @param string $baseUrl
	 */
	public function __construct(
		SearchSpring_Manager_Handler_ApiErrorHandler $errorHandler,
		Varien_Http_Adapter_Curl $curl,
		$baseUrl
	) {
		$this->errorHandler = $errorHandler;
		$this->curl = $curl;
		$this->baseUrl = $baseUrl;
	}


	/**
	 * Build the url based on expected format
	 *
	 * @param string $method
	 *
	 * @return string
	 */
	protected function buildUrl($method)
	{
		$url = sprintf(self::URL_FORMAT, $this->baseUrl, $method);

		return $url;
	}
}
