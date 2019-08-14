<?php
/**
 * LiveIndexer.php
 *
 * @copyright B7 Interactive, LLC. All Rights Reserved.
 */

/**
 * Class SearchSpring_Manager_Model_Observer_LiveIndexer
 *
 * @author Nate Brunette <nate@b7interactive.com>
 */
abstract class SearchSpring_Manager_Model_Observer_LiveIndexer
{

	/**
	 * Api adapter for connecting to SearchSpring api
	 *
	 * @var SearchSpring_Manager_Service_SearchSpring_IndexingApiAdapter $api
	 */
	protected $api;

	public function api()
	{
		if (is_null($this->api)) {
			$apiFactory = new SearchSpring_Manager_Factory_ApiFactory();
			$this->api = $apiFactory->make('index');
		}
		return $this->api;
	}

	/**
	 * Checks if live indexing is enabled
	 *
	 * @return bool
	 */
	protected function isEnabled()
	{
		return Mage::helper('searchspring_manager')->isLiveIndexingEnabled();
	}

	protected function apiPushProductIds($request)
	{
		try {
			$this->api()->pushIds($request);
		} catch (UnexpectedValueException $e) {
			$this->notifyAdminUser($e->getMessage());
		} catch (Exception $e) {
			$this->notifyAdminUser("SearchSpring: Unknown issue occurred while attempting live indexing operation.");
			Mage::logException($e);
		}
	}

    protected function notifyAdminUser($message)
	{
		// Only if we're in the admin panel
		if (Mage::app()->getStore()->isAdmin()) {
			$session = Mage::getSingleton('adminhtml/session');
			$session->addWarning($message);
		}
		return $this;
	}

}
