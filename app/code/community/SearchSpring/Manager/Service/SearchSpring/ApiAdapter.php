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
	 * The http client
	 *
	 * @var Zend_Http_Client
	 */
	protected $client;

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
	 * @param Zend_Http_Client $client
	 * @param string $baseUrl
	 */
	public function __construct(
		SearchSpring_Manager_Handler_ApiErrorHandler $errorHandler,
		Zend_Http_Client $client,
		$baseUrl
	) {
		$this->errorHandler = $errorHandler;
		$this->client = $client;
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
