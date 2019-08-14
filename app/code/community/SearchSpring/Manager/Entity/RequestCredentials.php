<?php
/**
 * File RequestCredentials.php
 *
 * @copyright B7 Interactive, LLC. All Rights Reserved.
 */

/**
 * Class SearchSpring_Manager_Entity_RequestCredentials
 *
 * The class models a SearchSpring API request's credentials parameters
 *
 * @author Jake Shelby <jake@b7interactive.com>
 */
class SearchSpring_Manager_Entity_RequestCredentials implements SearchSpring_Manager_Entity_Credentials
{

    /**
     * The SearchSpring website account Site ID, also used as a sort of username
     *
     * @var string $
     */
    protected $siteId;

    /**
     * The SearchSpring website account Secret Key, also used a password of sorts
     *
     * @var string $type
     */
    protected $secretKey;

    /**
     * Constructor
     *
     * @param string $siteId
     * @param string $secretKey
     */
    public function __construct($siteId, $secretKey)
    {
		$this->siteId = $siteId;
		$this->secretKey = $secretKey;
    }

	public function isPopulated() {
		if (empty($this->siteId) ||
			empty($this->secretKey))
		{
			return false;
		}
		return true;
	}

	public function getSecretKey() {
		return $this->secretKey;
	}

	public function getSecret() {
		return $this->getSecretKey();
	}

	public function getSiteId() {
		return $this->siteId;
	}

	public function getUsername() {
		return $this->getSiteId();
	}

	public function getPassword() {
		return $this->getSecretKey();
	}

}
