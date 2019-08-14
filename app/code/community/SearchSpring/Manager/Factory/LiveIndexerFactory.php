<?php
/**
 * LiveIndexerFactory.php
 *
 * @copyright B7 Interactive, LLC. All Rights Reserved.
 */

/**
 * Class SearchSpring_Manager_Factory_LiveIndexerFactory
 *
 * Create a SearchSpring Live Indexer Service
 *
 * @author Jake Shelby <jake@b7interactive.com>
 */
class SearchSpring_Manager_Factory_LiveIndexerFactory
{

	/**
	 * Make Live Indexer Service
	 *
	 * @return SearchSpring_Manager_Service_LiveIndexer
	 */
	public function make()
	{
		$hlp = Mage::helper('searchspring_manager');

		$service = new SearchSpring_Manager_Service_LiveIndexer(
			Mage::app(),
			$hlp->getConfig(),
			new SearchSpring_Manager_Factory_ApiFactory,
			new SearchSpring_Manager_Factory_IndexingRequestBodyFactory
		);

		return $service;
	}

}
