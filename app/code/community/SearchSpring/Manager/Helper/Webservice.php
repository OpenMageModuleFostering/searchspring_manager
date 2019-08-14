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

	// SearchSpring Specific Authentication Codes
	const AUTH_METHOD_SIMPLE	= 'simple';
	const AUTH_METHOD_OAUTH		= 'o-auth';

	public function verifyMageAPIAuthSimple() {
		$path = sprintf(self::PATH_FEED_AUTH_METHOD_VERIFY, self::AUTH_METHOD_SIMPLE);
		$response = $this->callSearchSpringWebservice($path, $this->getAuthMethodParamsForSimple());
		return $this->isResponseStatusSuccess($response);
	}

	public function registerMageAPIAuthSimple() {
		$path = sprintf(self::PATH_FEED_AUTH_METHOD_SET, self::AUTH_METHOD_SIMPLE);
		$response = $this->callSearchSpringWebservice($path, $this->getAuthMethodParamsForSimple());
		return $this->isResponseSuccess($response);
	}

	public function verifyMageAPIAuthOAuth() {
		$path = sprintf(self::PATH_FEED_AUTH_METHOD_VERIFY, self::AUTH_METHOD_OAUTH);
		$response = $this->callSearchSpringWebservice($path, $this->getAuthMethodParamsForOAuth());
		return $this->isResponseStatusSuccess($response);
	}

	public function registerMageAPIAuthOAuth() {
		$path = sprintf(self::PATH_FEED_AUTH_METHOD_SET, self::AUTH_METHOD_OAUTH);
		$response = $this->callSearchSpringWebservice($path, $this->getAuthMethodParamsForOAuth());
		return $this->isResponseSuccess($response);
	}

	public function callSearchSpringWebservice($path, $params = array()) {

		$hlp = Mage::helper('searchspring_manager');

		$apiHost = $hlp->getApiBaseUrl();
		$siteId  = $hlp->getApiSiteId();
		$secret  = $hlp->getApiSecretKey();
		$feedId  = $hlp->getApiFeedId();

		if (empty($apiHost) || empty($siteId) || empty($secret) || empty($feedId)) {
			// Can't do much without these first
			return false;
		}

		$url = $apiHost . $path;

		$client = new Zend_Http_Client($url, array(
			'maxredirects' => 0,
			'timeout'=>30
		));
		$client->setAuth($siteId, $secret);

		$params = array_merge(array('feedId' => $feedId), $params);

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

	protected function getAuthMethodParamsForSimple() {
		// Nothing needed
		return array();
	}

	protected function getAuthMethodParamsForOAuth() {

		$oahlp = Mage::helper('searchspring_manager/oauth');
		if (!($consumer = $oahlp->getConsumer())) {
			// Can't do much without these
			return array();
		}
		$cKey = $consumer->getKey();
		$cSecret = $consumer->getSecret();

		return array(
			'consumerKey' => $cKey,
			'consumerSecret' => $cSecret,
			'type' => 'magento_indexing',
		);
	}

}
