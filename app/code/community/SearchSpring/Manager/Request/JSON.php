<?php
/**
 * JSON.php
 *
 * @copyright B7 Interactive, LLC. All Rights Reserved.
 */

/**
 * Class SearchSpring_Manager_Request_JSON
 *
 * Wrapper for Request object that allows you to easily access JSON POST Data.
 *
 * @author James Bathgate <james@b7interactive.com>
 */
class SearchSpring_Manager_Request_JSON
{

    /**
     * Request parameters parsed from JSON
     *
     * @var object $request
     */
    private $params;

    /**
     * Constructor
     *
     * @param Mage_Core_Controller_Request_Http $request
     */
    public function __construct(Mage_Core_Controller_Request_Http $request) {
        $rawBody = $request->getRawBody();

        if(!empty($rawBody)) {
            $this->params = Zend_Json::decode($rawBody);

            if(null === $this->params) {
                throw new Exception('Invalid JSON in Request');
            }
        } else {
            $this->params = array();
        }


    }

    /**
     * Returns parameter from JSON request
     *
     * @param string $parameter
     *
     * @return mixed
     */
    public function getParam($parameter, $default=NULL) {
        return isset($this->params[$parameter])?$this->params[$parameter]:$default;
    }
}
