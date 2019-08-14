<?php
/**
 * File IndexingApiAdapter.php
 *
 * @copyright B7 Interactive, LLC. All Rights Reserved.
 */

/**
 * Class SearchSpring_Manager_Service_SearchSpring_IndexingApiAdapter
 *
 * Adapter for the SearchSpring Indexing API
 *
 * @author Nate Brunette <nate@b7interactive.com>
 */
class SearchSpring_Manager_Service_SearchSpring_IndexingApiAdapter extends SearchSpring_Manager_Service_SearchSpring_ApiAdapter
{
	const API_INDEX = 'api/index/magento/generate';

    /**
     * Push ids to api
     *
     * @param SearchSpring_Manager_Entity_IndexingRequestBody $requestBody
     */
    public function pushIds(SearchSpring_Manager_Entity_IndexingRequestBody $requestBody)
    {
        $this->call(self::API_INDEX, $requestBody);
    }

	/**
	 * Generic method that accepts any api endpoint path
	 *
	 * @param string $method
	 * @param SearchSpring_Manager_Entity_IndexingRequestBody $requestBody
	 */
	public function call($method, SearchSpring_Manager_Entity_IndexingRequestBody $requestBody)
	{
		$url = $this->buildUrl($method);
		$this->request(Zend_Http_Client::POST, $url, json_encode($requestBody->jsonSerialize()));
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
	private function request($httpMethod, $url, $body)
	{
		$this->client->setUri($url);
		$this->client->setRawData($body, 'application/json');
		$response = $this->client->request($httpMethod);

		while($this->errorHandler->shouldRetry($response)) {
			$response = $this->client->request($httpMethod);
		}

		$responseCode = $response->getStatus();

		return $responseCode;
	}

}
