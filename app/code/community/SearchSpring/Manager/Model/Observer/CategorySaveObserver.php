<?php
/**
 * CategorySaveObserver.php
 *
 * @copyright B7 Interactive, LLC. All Rights Reserved.
 */

/**
 * Class SearchSpring_Manager_Model_Observer_CategorySaveObserver
 *
 * Listens for category change events and forwards to the Live Indexing Service
 *
 * @author Nate Brunette <nate@b7interactive.com>
 * @author Jake Shelby <jake@b7interactive.com>
 */
class SearchSpring_Manager_Model_Observer_CategorySaveObserver extends SearchSpring_Manager_Model_Observer
{

	/**
	 * Live Indexer Service
	 *
	 * @var SearchSpring_Manager_Service_LiveIndexer $liveIndexer
	 */
	private $liveIndexer;

	/**
	 * Performs operations on a Varien_Object
	 *
	 * @var SearchSpring_Manager_VarienObject_Data $varienObjectData
	 */
	private $varienObjectData;

	/**
	 * Constructor
	 *
	 * We need to do some setup in here because there's no way to inject dependencies
	 */
	public function __construct()
	{
		$factory = new SearchSpring_Manager_Factory_LiveIndexerFactory;
		$this->liveIndexer = $factory->make();

		$this->varienObjectData = new SearchSpring_Manager_VarienObject_Data();
	}

	/**
	 * After a category is saved
	 *
	 * We only push out an update to products and sub-products if the category name or status has changed
	 *
	 * @todo implement status change
	 *
	 * @event catalog_category_save_commit_after
	 *
	 * @param Varien_Event_Observer $productEvent
	 * @return void
	 */
	public function afterSaveUpdateProductCategory(Varien_Event_Observer $productEvent)
	{
		try {

			$category = $productEvent->getCategory();

			// if this is a new category return because we already sent the ids
			if (true === $this->varienObjectData->isNew($category)) {
				return true;
			}

			$updates = $this->varienObjectData->findUpdatedData($category);

			// If category name is not changed, this will not affect the product categories
			if (!isset($updates['name']) && !isset($updates['is_active'])) {
				return true;
			}

			$this->liveIndexer->categorySaved($category);

		} catch (Exception $e) {
			$this->handleException($e);
		}
	}

	/**
	 * After a category is moved
	 *
	 * Updates products and sub-products. Only update if path is changed. This should be double checked, but path
	 * should not be changed if the category is just reordered.
	 *
	 * @event category_move
	 *
	 * @param Varien_Event_Observer $productEvent
	 * @return void
	 */
	public function afterMoveUpdateProductCategory(Varien_Event_Observer $productEvent)
	{
		try {

			$category = $productEvent->getCategory();
			$updates = $this->varienObjectData->findUpdatedData($category);

			if (!isset($updates['path'])) {
				return;
			}

			$this->liveIndexer->categorySaved($category);

		} catch (Exception $e) {
			$this->handleException($e);
		}
	}

	/**
	 * Right before a category is deleted. Remove the category from products and sub-products.
	 *
	 * @event catalog_controller_category_delete
	 *
	 * @param Varien_Event_Observer $productEvent
	 * @return void
	 */
	public function beforeDeleteUpdateProductCategory(Varien_Event_Observer $productEvent)
	{
		try {

			$category = $productEvent->getCategory();

			// TODO -- ?? should we add the token dance here as well ?? might have a problem with the race condition here
			$this->liveIndexer->categoryDeleted($category);

		} catch (Exception $e) {
			$this->handleException($e);
		}
	}

	/**
	 * After category products have changed
	 *
	 * This is triggered when products are checked or unchecked for a category.  Only affects products of category.
	 *
	 * @event catalog_category_change_products
	 *
	 * @param Varien_Event_Observer $productEvent
	 * @return void
	 */
	public function afterProductChangeUpdateProductCategory(Varien_Event_Observer $productEvent)
	{
		try {

			$category = $productEvent->getCategory();
			$productIds = $productEvent->getProductIds();

			$this->liveIndexer->categoryProductsUpdated($category, $productIds);

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
