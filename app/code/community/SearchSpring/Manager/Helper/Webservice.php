<?php
/**
 * File Webservice.php
 *
 * @copyright B7 Interactive, LLC. All Rights Reserved.
 */

/**
 * Class SearchSpring_Manager_Helper_Webservice
 *
 * @author Jake Shelby <jake@searchspring.com>
 */
class SearchSpring_Manager_Helper_Webservice extends Mage_Core_Helper_Abstract
{

	const PATH_FEED_AUTH_METHOD_SET		= "/api/manage/feeds/auth-method/%s/set.json";
	const PATH_FEED_AUTH_METHOD_VERIFY	= "/api/manage/feeds/auth-method/%s/verify.json";

	const PATH_FEED_SETTINGS_URL_SET	= "/api/manage/feeds/settings/indexing-urls";

	// SearchSpring Specific Authentication Codes
	const AUTH_METHOD_SIMPLE	= 'simple';
	const AUTH_METHOD_OAUTH		= 'o-auth';

	protected $_apiBaseUrl;

	/**
	 * Convenience Functions for calling the webservice
	 */

	public function verifyMageAPIAuthSimple($feedId, $creds) {
		$path = sprintf(self::PATH_FEED_AUTH_METHOD_VERIFY, self::AUTH_METHOD_SIMPLE);
		$response = $this->callSearchSpringWebservice($path, $this->getAuthMethodParamsForSimple($feedId), $creds);
		return $this->isResponseStatusSuccess($response);
	}

	public function registerMageAPIAuthSimple($feedId, $creds) {
		$path = sprintf(self::PATH_FEED_AUTH_METHOD_SET, self::AUTH_METHOD_SIMPLE);
		$response = $this->callSearchSpringWebservice($path, $this->getAuthMethodParamsForSimple($feedId), $creds);
		$this->ensureResponseSuccess($response, __FUNCTION__);
	}

	public function verifyMageAPIAuthOAuth($feedId, $creds) {
		$path = sprintf(self::PATH_FEED_AUTH_METHOD_VERIFY, self::AUTH_METHOD_OAUTH);
		$response = $this->callSearchSpringWebservice($path, $this->getAuthMethodParamsForOAuth($feedId), $creds);
		return $this->isResponseStatusSuccess($response);
	}

	public function registerMageAPIAuthOAuth($feedId, $creds) {
		$path = sprintf(self::PATH_FEED_AUTH_METHOD_SET, self::AUTH_METHOD_OAUTH);
		$response = $this->callSearchSpringWebservice($path, $this->getAuthMethodParamsForOAuth($feedId), $creds);
		$this->ensureResponseSuccess($response, __FUNCTION__);
	}

	public function registerMageAPIUrls($feedId, $creds, $feedUrl, $batchUrl, $liveUrl) {
		$params = array(
			'feedId'	=> $feedId,
			'feedUrl'	=> $feedUrl,
			'batchUrl'	=> $batchUrl,
			'liveUrl'	=> $liveUrl,
		);
		$response = $this->callSearchSpringWebservice(
			self::PATH_FEED_SETTINGS_URL_SET,
			$params, $creds
		);
		$this->ensureResponseSuccess($response, __FUNCTION__);
	}

	/**
	 * Easy generic way to make a request to the SearchSpring API
	 */
	public function callSearchSpringWebservice($path, $params = array(), SearchSpring_Manager_Entity_Credentials $creds = null) {

		$apiHost = $this->getApiBaseUrl();

		$url = $apiHost . $path;

		$client = new Zend_Http_Client($url, array(
			'maxredirects' => 0,
			'timeout'=>30
		));

		// If credentials were provided
		if ($creds) {
			// ... add them as basic auth
			$client->setAuth( $creds->getUsername(), $creds->getPassword() );
		}

		$client->setParameterGet($params);

		$response = $client->request();

		return $response;
	}

	public function isResponseSuccess($response) {
		if (!$response) {
			return false;
		}
		return $response->isSuccessful();
	}

	public function isResponseStatusSuccess($response) {
		if (!$this->isResponseSuccess($response)) return false;
		$responseData = Zend_Json::decode($response->getBody(), Zend_Json::TYPE_OBJECT);
		if (!is_object($responseData)) return false;
		if ($responseData->status !== 'success') return false;
		return true;
	}

	protected function ensureResponseSuccess($response, $context = null) {
		if (!$this->isResponseSuccess($response)) {
			$message = '';
			if ($context) {
				$message .= $context . ': ';
			}
			$message .= 'SearchSpring webservice response returned non-success response';
			if (is_object($response)) {
				$message .= ': ' . $response->getBody();
			}
			throw new Exception($message);
		}
	}

	protected function getAuthMethodParamsForSimple($feedId) {
		// Nothing else needed
		return array(
			'feedId' => $feedId,
		);
	}

	protected function getAuthMethodParamsForOAuth($feedId) {

		$oahlp = Mage::helper('searchspring_manager/oauth');
		if (!($consumer = $oahlp->getConsumer())) {
			// Can't do much without these
			return array();
		}
		$cKey = $consumer->getKey();
		$cSecret = $consumer->getSecret();

		return array(
			'feedId' => $feedId,
			'consumerKey' => $cKey,
			'consumerSecret' => $cSecret,
			'type' => 'magento_indexing',
		);
	}

	/**
	 * To allow caller to specify a different API base URL
	 */

	public function getApiBaseUrl() {
		if (empty($this->_apiBaseUrl)) {
			return Mage::helper('searchspring_manager')->getApiBaseUrl();
		}
		return $this->_apiBaseUrl;
	}

	public function setApiBaseUrl($url) {
		$this->_apiBaseUrl = $url;
		return $this;
	}

}
