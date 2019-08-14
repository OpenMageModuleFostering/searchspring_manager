<?php
class SearchSpring_Manager_Model_Api2_Indexing_Rest_Admin_V1 extends Mage_Api2_Model_Resource
{
	/**
	 * Default starting point if no parameter is set
	 */
	const OFFSET_DEFAULT = 0;

	/**
	 * Default product limit if no parameter is set
	 */
	const COUNT_DEFAULT = 100;

	// Live Indexing
	public function _create()
	{
		$this->fetchById();
	}

	// Data Feed Generation
	public function _retrieve()
	{
		$this->generateFeed();
	}

	public function _update()
	{
		throw new Exception('Not Implemented');
	}

	public function _delete()
	{
		throw new Exception('Not Implemented');
	}

	/**
	 * Default action for updating based on product/category id
	 *
	 * Parameters:
	 *     type (required): The type of id passed in. Can be 'product' or 'category'
	 *     ids (required): An array of ids
	 */
	public function fetchById()
	{
		$request = $this->getRequest()->getBodyParams();

		if(!is_array($request)) {
			$this->setJsonResponse(
				array(
					'status' => 'error',
					'errorCode' => SearchSpring_ErrorCodes::BAD_REQUEST,
					'message' => 'Invalid request format'
				),
				400
			);

			return;
		}

		$type = $request['type'];
		$ids = $request['ids'];

		if (null === $type) {
			$this->setJsonResponse(
				array(
					'status' => 'error',
					'errorCode' => SearchSpring_ErrorCodes::TYPE_NOT_SET,
					'message' => 'Type must be specified'
				),
				400
			);

			return;
		}

		if (null === $ids) {
			$this->setJsonResponse(
				array(
					'status' => 'error',
					'errorCode' => SearchSpring_ErrorCodes::IDS_NOT_SET,
					'message' => 'Ids must be specified'
				),
				400
			);

			return;
		}

		$requestParams =  new SearchSpring_Manager_Entity_RequestParams(
			(int)$request['size'],
			(int)$request['start'],
			$this->_getStore()->getCode()
		);

		$params = array('ids' => $ids);

		$generatorFactory = new SearchSpring_Manager_Factory_GeneratorFactory();
		$generator = $generatorFactory->make($type, $requestParams, $params);
		$message = $generator->generate();

		$this->setJsonResponse($message);
	}

	function generateFeed() {
		// check file is writable first
		if (!is_writable(Mage::getBaseDir())) {
			$this->setJsonResponse(
				array(
					'status' => 'error',
					'errorCode' => SearchSpring_ErrorCodes::DIR_NOT_WRITABLE,
					'message' => 'Magento base directory is not writable'
				),
				500
			);

			return;
		}

		$uniqueFilename = $this->getRequest()->getParam('filename');

		if (null === $uniqueFilename) {
			$this->setJsonResponse(
				array(
					'status' => 'error',
					'errorCode' => SearchSpring_ErrorCodes::FILENAME_NOT_SET,
					'message' => 'Unique filename must be passed in'
				),
				400
			);

			return;
		}

		$requestParams =  new SearchSpring_Manager_Entity_RequestParams(
			(int)$this->getRequest()->getParam('count', self::COUNT_DEFAULT),
			(int)$this->getRequest()->getParam('start', self::OFFSET_DEFAULT),
			$this->_getStore()->getCode()
		);

		$params = array('filename' => $uniqueFilename);

		$generatorFactory = new SearchSpring_Manager_Factory_GeneratorFactory();
		$generator = $generatorFactory->make(
			SearchSpring_Manager_Factory_GeneratorFactory::TYPE_FEED,
			$requestParams,
			$params
		);

		$message = $generator->generate();

		$this->setTextResponse($message);

		return;
	}

	/**
	 * Set appropriate response variables for a json response
	 *
	 * @param array $message The message that should be sent back
	 * @param int $responseCode The Http response code
	 */
	private function setJsonResponse(array $message, $responseCode = 200)
	{
		header('Content-type: application/json', true, $responseCode);
		echo json_encode($message);
		exit();
	}

	/**
	 * Set a text based response
	 *
	 * @param string $message
	 * @param int $responseCode
	 */
	private function setTextResponse($message, $responseCode = 200)
	{
		header('Content-type: text/plain', true, $responseCode);
		echo $message;
		exit();

	}
}
?>