<?php
/**
 * 0-Legged oAuth Authentication adapter, oAuth direct consumer - provider, minus the user
 *
 */
class SearchSpring_Manager_Model_Api2_Auth_Adapter extends Mage_Api2_Model_Auth_Adapter_Abstract
{
	/**
	 * Process request and figure out an API user type and its identifier
	 *
	 * Returns stdClass object with two properties: type and id
	 *
	 * @param Mage_Api2_Model_Request $request
	 * @return stdClass
	 */
	public function getUserParams(Mage_Api2_Model_Request $request)
	{
		// Get our oauth server, with 0-Legged Support
		$oauthServer = Mage::getModel('searchspring_manager/oauth_server', $request);
		$hlp = Mage::helper('searchspring_manager');
		$oahlp = Mage::helper('searchspring_manager/oauth');
		$storeCode = $request->getParam('store');

		try {
			// Make sure this Authentication Method is enabled
			if ($hlp->getAuthenticationMethod($storeCode) != 'oauth') {
				throw new Mage_Api2_Exception('Not authorized', Mage_Api2_Model_Server::HTTP_UNAUTHORIZED);
			}

			// Authenticate Consumer
			$consumer = $oauthServer->checkDirectConsumerRequest();

			// Validate Consumer Identity, with the one configured to use this API
			if ($consumer->getId() != $oahlp->getConsumerId()) {
				throw new Mage_Api2_Exception('Not authorized', Mage_Api2_Model_Server::HTTP_UNAUTHORIZED);
			}

			// Return user configured to use this API
			$adminId = $oahlp->getAdminUserId();
			$userParamsObj = (object) array('type' => Mage_Oauth_Model_Token::USER_TYPE_ADMIN, 'id' => $adminId);

		} catch (Exception $e) {
			throw new Mage_Api2_Exception($oauthServer->reportProblem($e), Mage_Api2_Model_Server::HTTP_UNAUTHORIZED);
		}

		 return $userParamsObj;
	}

	/**
	 * Check if request contains authentication info for adapter
	 *
	 * @param Mage_Api2_Model_Request $request
	 * @return boolean
	 */
	public function isApplicableToRequest(Mage_Api2_Model_Request $request)
	{
		// Check if request is for searchspring apis, and the request is for oauth
		$uri = $request->getRequestUri();
		if (preg_match('!/api/rest/searchspring/.*!i', $uri)) {
			return true;
		}

		return false;
	}

}
