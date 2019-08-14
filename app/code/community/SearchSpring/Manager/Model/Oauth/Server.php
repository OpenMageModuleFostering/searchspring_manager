<?php

/**
 * oAuth Server With 0-Legged Support
 *
 */
class SearchSpring_Manager_Model_Oauth_Server extends Mage_Oauth_Model_Server
{

	/**
	 * Validate request with consumer token, and empty access token
	 */
	public function checkDirectConsumerRequest()
	{
		// get parameters from request
		$this->_fetchParams();

		// make generic validation of request parameters
		$this->_validateProtocolParams();

		// initialize consumer
		$this->_initConsumer();

		// generate empty token (to validate against)
		$this->_token = new Varien_Object(array('secret' => ''));

		// validate signature
		$this->_validateSignature();

		return $this->_consumer;
	}

}
