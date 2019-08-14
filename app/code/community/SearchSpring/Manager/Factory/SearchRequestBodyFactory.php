<?php
/**
 * IndexingRequestBodyFactory.php
 *
 * @copyright B7 Interactive, LLC. All Rights Reserved.
 */

/**
 * Class SearchSpring_Manager_Factory_SearchRequestBodyFactory
 *
 * Create a request body
 *
 * @author James Bathgate <james@b7interactive.com>
 */
class SearchSpring_Manager_Factory_SearchRequestBodyFactory
{

    public function make()
    {
		// Build using the site Id of the current magento store
		$siteId = Mage::helper('searchspring_manager')->getApiSiteId( Mage::app()->getStore() );
		if (null === $siteId) {
			throw new UnexpectedValueException('SearchSpring: Site ID must be set to create a search request, none found for store: ' . Mage::app()->getStore()->getCode());
		}

        $requestBody = new SearchSpring_Manager_Entity_SearchRequestBody($siteId);
        return $requestBody;
    }
}
