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
	 * Default store if no parameter is set
	 */
	const STORE_DEFAULT = 'default';

    /**
     * Default action for updating based on product/category id
     *
     * Parameters:
     *     type (required): The type of id passed in. Can be 'product' or 'category'
     *     ids (required): An array of ids
     */
    public function indexAction()
    {
		$request = new SearchSpring_Manager_Request_JSON($this->getRequest());

		$type = $request->getParam('type');
		$ids = $request->getParam('ids');

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
            (int)$request->getParam('size', null),
            (int)$request->getParam('start', null),
            $request->getParam('store', self::STORE_DEFAULT)
        );

        $params = array('ids' => $ids);

        $generatorFactory = new SearchSpring_Manager_Factory_GeneratorFactory();
        $generator = $generatorFactory->make($type, $requestParams, $params);
        $message = $generator->generate();

        $this->setJsonResponse($message);
    }


	/**
	 * Generate an xml feed of all products
	 *
	 * Parameters:
	 *     filename (required): A unique filename when creating temporary files.
	 *     start (optional): The starting point for fetching products. Defaults to 0.
	 *     count (optional): The number of products to fetch. Defaults to 100.
	 *     store (optional): The store name as a string. Defaults to 'default'
	 */
	public function feedAction()
	{
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
            $this->getRequest()->getParam('store', self::STORE_DEFAULT)
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
		$this->getResponse()->setHeader('Content-type', 'application/json');
		$this->getResponse()->setHttpResponseCode($responseCode);

		$responseBody = json_encode($message);
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
		$this->getResponse()->setBody($message);
	}
}
