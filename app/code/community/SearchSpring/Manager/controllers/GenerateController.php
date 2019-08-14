<?php
/**
 * GenerateController.php
 *
 * @copyright B7 Interactive, LLC. All Rights Reserved.
 */

/**
 * Class SearchSpring_Manager_GenerateController
 *
 * @author Nate Brunette <nate@b7interactive.com>
 */
class SearchSpring_Manager_GenerateController extends Mage_Core_Controller_Front_Action
{
	/**
	 * Default starting point if no parameter is set
	 */
	const OFFSET_DEFAULT = 0;

	/**
	 * Default product limit if no parameter is set
	 */
	const COUNT_DEFAULT = 100;

	/**
	 * Default action for updating based on product/category id
	 *
	 * Parameters:
	 *	 type (required): The type of id passed in. Can be 'product' or 'category'
	 *	 ids (required): An array of ids
	 */
	public function indexAction()
	{
		$request = new SearchSpring_Manager_Request_JSON($this->getRequest());

		$type = $request->getParam('type');
		$ids = $request->getParam('ids');

		if (null === $type) {
			$this->respondWithError('Type must be specified', SearchSpring_ErrorCodes::TYPE_NOT_SET, 400);
		}

		if (null === $ids) {
			$this->respondWithError('Ids must be specified', SearchSpring_ErrorCodes::IDS_NOT_SET, 400);
		}

		$requestParams =  new SearchSpring_Manager_Entity_RequestParams(
			(int)$request->getParam('size', null),
			(int)$request->getParam('start', null),
			Mage::app()->getStore()->getCode()
		);

		$params = array('ids' => $ids);

		try {
			$generatorFactory = new SearchSpring_Manager_Factory_GeneratorFactory();
			$generator = $generatorFactory->make($type, $requestParams, $params);
			$message = $generator->generate();
		} catch (Exception $e) {
			$this->handleException($e);
		}

		$this->setJsonResponse($message);
	}

	/**
	 * Generate an xml feed of all products
	 *
	 * Parameters:
	 *	 filename (required): A unique filename when creating temporary files.
	 *	 start (optional): The starting point for fetching products. Defaults to 0.
	 *	 count (optional): The number of products to fetch. Defaults to 100.
	 */
	public function feedAction()
	{
		$uniqueFilename = $this->getRequest()->getParam('filename');

		if (null === $uniqueFilename) {
			$this->respondWithError('Filename must be specified', SearchSpring_ErrorCodes::FILENAME_NOT_SET, 400);
		}

		$requestParams =  new SearchSpring_Manager_Entity_RequestParams(
			(int)$this->getRequest()->getParam('count', self::COUNT_DEFAULT),
			(int)$this->getRequest()->getParam('start', self::OFFSET_DEFAULT),
			Mage::app()->getStore()->getCode()
		);

		$params = array('filename' => $uniqueFilename);

		try {

			$generatorFactory = new SearchSpring_Manager_Factory_GeneratorFactory();
			$generator = $generatorFactory->make(
				SearchSpring_Manager_Factory_GeneratorFactory::TYPE_FEED,
				$requestParams,
				$params
			);
			$message = $generator->generate();

		} catch (Exception $e) {
			$this->handleException($e);
		}

		$this->setTextResponse($message);
	}

	public function preDispatch()
	{

		if ($this->shouldProfile()) {
			Varien_Profiler::enable();
		}

 		// Do not start standart session
		$this->setFlag('', self::FLAG_NO_START_SESSION, 1);

		parent::preDispatch();

		try {

			// Make sure this access method is enabled
			if (!$this->isEnabled()) {
				$this->_redirect('noroute');
				$this->setFlag('',self::FLAG_NO_DISPATCH,true);
				return $this;
			}

			// Validate Authentication
			$this->_authenticate();

		} catch (Exception $e) {
			$this->handleException($e);
		}

		return $this;
	}

	protected function isEnabled() {
		$hlp = Mage::helper('searchspring_manager');
		// For now, this controller only handles the 'simple' auth method
		$authenticationMethod = $hlp->getAuthenticationMethod( Mage::app()->getStore() );
		return $authenticationMethod == 'simple';
	}

	protected function _authenticate() {

		$hlp = Mage::helper('searchspring_manager/http');
		list($username, $password) = $hlp->isSimpleAuthProvided(true); // true, itemized

		// Make sure auth username was provided
		if(empty($username)) {
			$this->respondWithError(
				'Authentication Failed: Missing username',
				SearchSpring_ErrorCodes::AUTH_CREDENTIALS_MISSING,
				401
			);
		}

		// Make sure auth password was provided
		if(empty($password)) {
			$this->respondWithError(
				'Authentication Failed: Missing password',
				SearchSpring_ErrorCodes::AUTH_CREDENTIALS_MISSING,
				401
			);
		}

		// Make sure auth credentials are valid
		if(!$hlp->isSimpleAuthValid($this->_getMyCredentials())) {
			$this->respondWithError(
				'Authentication Failed: Invalid credentials',
				SearchSpring_ErrorCodes::AUTH_CREDENTIALS_INVALID,
				401
			);
		}

	}

	protected function _getMyCredentials() {
		return Mage::helper('searchspring_manager')->getMageApiCredentials( Mage::app()->getStore() );
	}

	/**
	 * Action to forward/redirect/fork to when an unhandled
	 * exception/error is encountered.
	 *
	 * @return void
	 */
	public function exceptionAction() {

		// Get Message and Status Code from controller flag
		$message = $this->getFlag('', 'unhandled-exception-message');
		$errorCode = $this->getFlag('', 'unhandled-exception-error-code');
		$responseCode = $this->getFlag('', 'unhandled-exception-http-response-code');

		// Default Message and Code
		if (!$message) $message = "Unknown Issue";
		if (!$errorCode) $errorCode = "Unknown Issue";
		if (!$responseCode) $responseCode = 500;

		// Return all issues as json response
		$this->setJsonResponse(
			array(
				'status' => 'error',
				'errorCode' => $errorCode,
				'message' => $message,
			),
			$responseCode
		);
	}

	/**
	 * Respond with error, and optional error code / http status codes
	 *
	 * @param Exception $e Any exception that needs to be handled
	 *
	 * @throws Mage_Core_Controller_Varien_Exception always, to bubble up to parent
	 */
	protected function handleException(Exception $e) {

		// If it's already a controller exception, just bubble it up
		if ($e instanceof Mage_Core_Controller_Varien_Exception) {
			throw $e;
		}

		// Convert to controller exception, as unknown, 500
		$this->respondWithError($e->getMessage());
	}

	/**
	 * Respond with error, and optional error code / http status codes
	 *
	 * @param string $message The message that should be sent back
	 * @param int $errorCode The SpringSpring Error Code
	 * @param int $responseCode The Http response code
	 *
	 * @throws Mage_Core_Controller_Varien_Exception always, to bubble up to parent
	 */
	protected function respondWithError(
		$message,
		$errorCode = SearchSpring_ErrorCodes::UNKNOWN_EXCEPTION,
		$responseCode = 500
	) {
		$controllerException = new Mage_Core_Controller_Varien_Exception;
		$controllerException->prepareFork('exception');
		$controllerException->prepareFlag('exception', 'unhandled-exception-message', $message);
		$controllerException->prepareFlag('exception', 'unhandled-exception-error-code', $errorCode);
		$controllerException->prepareFlag('exception', 'unhandled-exception-http-response-code', $responseCode);
		throw $controllerException;
	}

	/**
	 * Set appropriate response variables for a json response
	 *
	 * @param array $message The message that should be sent back
	 * @param int $responseCode The Http response code
	 */
	private function setJsonResponse(array $message, $responseCode = 200)
	{
		// If we are requiring authentication, set the correct header as well
		if ($responseCode == 401) {
			$this->getResponse()->setHeader('WWW-Authenticate','Basic realm="SearchSpring Manager"');
		}

		$this->getResponse()->setHeader('Content-type', 'application/json');
		$this->getResponse()->setHttpResponseCode($responseCode);

		$responseBody = Zend_Json::encode($message);

		if ($this->shouldProfile()) {
			$responseBody .= "\n\nProfiler Data:\n" . Mage::helper('searchspring_manager/profiler')->fetchHumanReadable();
		}

		$this->getResponse()->setBody($responseBody);
	}

	/**
	 * Set a text based response
	 *
	 * @param string $message
	 * @param int $responseCode
	 */
	private function setTextResponse($message, $responseCode = 200)
	{
		$this->getResponse()->setHeader('Content-type', 'text/plain');
		$this->getResponse()->setHttpResponseCode($responseCode);

		if ($this->shouldProfile()) {
			$message .= "\n\nProfiler Data:\n" . Mage::helper('searchspring_manager/profiler')->fetchHumanReadable();
		}

		$this->getResponse()->setBody($message);
	}

	private function shouldProfile() {
		$param = $this->getRequest()->getParam('profiler');
		return ($param == 'true');
	}
}
