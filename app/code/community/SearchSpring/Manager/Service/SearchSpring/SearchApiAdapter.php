<?php
/**
 * File SearchApiAdapter.php
 *
 * @copyright B7 Interactive, LLC. All Rights Reserved.
 */

/**
 * Class SearchSpring_Manager_Service_SearchSpring_IndexingApiAdapter
 *
 * Adapter for the SearchSpring Indexing API
 *
 * @author James Bathgate <james@b7interactive.com>
 */
class SearchSpring_Manager_Service_SearchSpring_SearchApiAdapter extends SearchSpring_Manager_Service_SearchSpring_ApiAdapter
{

	const API_SEARCH = 'api/search/search.json';

	/**
	 * Push ids to api
	 *
	 * @param SearchSpring_Manager_Entity_SearchRequestBody $requestBody
	 */
	public function search(SearchSpring_Manager_Entity_SearchRequestBody $requestBody)
	{
		$response = $this->call(self::API_SEARCH, $requestBody);

		if(null === ($result = json_decode($response))) {
			throw new Exception('Unable to parse SearchSpring search response JSON');
		}

		return $result;
	}

	/**
	 * Generic method that accepts any api endpoint path
	 *
	 * @param string $method
	 * @param SearchSpring_Manager_Entity_SearchRequestBody $requestBody
	 */
	public function call($method, SearchSpring_Manager_Entity_SearchRequestBody $requestBody)
	{
		$url = $this->buildUrl($method);
		return $this->request(Zend_Http_Client::GET, $url, (string)$requestBody);
	}

	/**
	 * Helper method to make an api request
	 *
	 * @param string $httpMethod
	 * @param string $url
	 * @param string $body
	 *
	 * @return int
	 */
	private function request($httpMethod, $url, $parameters)
	{

		// TODO Change this to Zend_Http_Client, parameters shouldn't be a string but an array
		$this->curl->write($httpMethod, $url . '?' . $parameters, Zend_Http_Client::HTTP_1, array());
		$response = $this->curl->read();

		$responseCode = Zend_Http_Response::extractCode($response);
		$responseBody = Zend_Http_Response::extractBody($response);

		if ($this->errorHandler->shouldRetry($response)) {
			return $this->request($httpMethod, $url, $parameters);
		}

		return $responseBody;

	}

}
