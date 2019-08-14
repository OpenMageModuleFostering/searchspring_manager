<?php
/**
 * File ProductSaveObserver.php
 *
 * @copyright B7 Interactive, LLC. All Rights Reserved.
 */

/**
 * Class SearchSpring_Manager_Model_Observer_ProductSaveObserver
 *
 * Listens for product change events and forwards to the Live Indexing Service
 *
 * @author Nate Brunette <nate@b7interactive.com>
 * @author Jake Shelby <jake@b7interactive.com>
 */
class SearchSpring_Manager_Model_Observer_ProductSaveObserver extends SearchSpring_Manager_Model_Observer
{

	/**
	 * Live Indexer Service
	 *
	 * @var SearchSpring_Manager_Service_LiveIndexer $liveIndexer
	 */
	private $liveIndexer;

	/**
	 * Deletion Tokens queue
	 *
	 * @var SearchSpring_Manager_Entity_ProductDeletionToken[] $deletionTokens
	 */
	protected $deletionTokens = array();

	/**
	 * Constructor
	 *
	 * We need to do some setup in here because there's no way to inject dependencies
	 */
	public function __construct()
	{
		$factory = new SearchSpring_Manager_Factory_LiveIndexerFactory;
		$this->liveIndexer = $factory->make();
	}

	/**
	* Before a product is deleted obtain a token for deletion
	*
	* @param Varien_Event_Observer $productEvent The product event data
	* @return void
	*/
	public function beforeDeletePushProduct(Varien_Event_Observer $productEvent)
	{
		try {

			$product = $productEvent->getProduct();

			// If we haven't already obtained a token...
			if (!isset($this->deletionTokens[$product->getId()])) {

				// Queue up a token, for after the product has been deleted
				$this->deletionTokens[$product->getId()] =
					$this->liveIndexer->obtainProductDeletionToken($product);

			}

		} catch (Exception $e) {
			$this->handleException($e);
		}
	}

	/**
	 * After a product is deleted, push that product deletion token to the indexer service
	 *
	 * @param Varien_Event_Observer $productEvent The product event data
	 * @return void
	 */
	public function afterDeletePushProduct(Varien_Event_Observer $productEvent)
	{
		try {

			$product = $productEvent->getProduct();

			// If we haven't already obtained a token...
			if (isset($this->deletionTokens[$product->getId()])) {

				$token = $this->deletionTokens[$product->getId()];
				$this->liveIndexer->productDeleted($token);

			}

		} catch (Exception $e) {
			$this->handleException($e);
		}
	}

	/**
	 * After a product is saved, push that product to the SearchSpring API
	 *
	 * @param Varien_Event_Observer $productEvent The product event data
	 * @return void
	 */
	public function afterSavePushProduct(Varien_Event_Observer $productEvent)
	{
		try {

			$product = $productEvent->getProduct();

			// TODO ? should there be more logic to verify that an actual change to data was made ?

			$this->liveIndexer->productSaved($product);

		} catch (Exception $e) {
			$this->handleException($e);
		}
	}

	protected function handleException(Exception $e)
	{
		// TODO - is there a mage exception we can catch to send a better message to admin??

		// Get some kind of context info

		// log what happened
		Mage::logException($e);

		// Get the best message for the admin user, and notify
		$this->notifyAdminUser("SearchSpring: There was a live indexing issue, please contact SearchSpring support for assistance.");
	}

}
