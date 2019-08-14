<?php
/**
 * File Data.php
 *
 * @copyright B7 Interactive, LLC. All Rights Reserved.
 */

/**
 * Class SearchSpring_Manager_Helper_Http
 *
 * Helper for all things HTTP
 *
 * @author Jake Shelby <jake@searchspring.com>
 */
class SearchSpring_Manager_Helper_Http extends Mage_Core_Helper_Http
{

	/**
	 * Validate Simple Authentication, just return wether or
	 * not the basic auth credentials were provided
	 *
	 * @return mixed bool|array
	 */
	public function isSimpleAuthProvided($itemized = false) {

		list($username, $password) = $this->getBasicAuthCredentials();

		if ($itemized) {
			return array(!empty($username), !empty($password));
		} else {
			return !(empty($username) || empty($password));
		}

	}

	/**
	 * Validate Simple Authentication, just return wether or
	 * not the basic auth credentials match for Manager's settings
	 *
	 * @return bool
	 */
	public function isSimpleAuthValid(SearchSpring_Manager_Entity_Credentials $creds) {

		// Make sure auth was provided for this request at all
		if (!$this->isSimpleAuthProvided()) {
			return false;
		}

		// Validate against credentials
		list($username, $password) = $this->getBasicAuthCredentials();
		return (
			$username == $creds->getUsername() &&
			$password == $creds->getPassword()
		);
	}

	/**
	 * Get Basic Auth Credentials
	 *
	 * @param array $headers
	 * @return array
	 */
	public function getBasicAuthCredentials($headers = null)
	{
		$creds = $this->authValidate($headers);
		return $creds;
	}

	public function authFailed()
	{
		// Overriding this to not do anything
		// Original function sends 'require auth' headers and exits
	}

}
