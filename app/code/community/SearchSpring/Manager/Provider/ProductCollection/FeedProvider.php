<?php
/**
 * FeedProvider.php
 *
 * @copyright B7 Interactive, LLC. All Rights Reserved.
 */

/**
 * Class SearchSpring_Manager_Provider_ProductCollection_FeedProvider
 *
 * Provides a ProductCollection for feeds
 *
 * @author Nate Brunette <nate@b7interactive.com>
 */
class SearchSpring_Manager_Provider_ProductCollection_FeedProvider
    implements SearchSpring_Manager_Provider_ProductCollectionProvider
{
    /**
     * A ProductCollection
     *
     * @var Mage_Catalog_Model_Resource_Product_Collection $collection
     */
    private $collection;

    /**
     * The size of the collection
     *
     * @var int $collectionCount
     */
    private $collectionCount;

    /**
     * Request parameters
     *
     * Used to get the store for filter
     *
     * @var SearchSpring_Manager_Entity_RequestParams
     */
    private $requestParams;

    /**
     * Constructor
     *
     * @param SearchSpring_Manager_Entity_RequestParams $requestParams
     */
    public function __construct(SearchSpring_Manager_Entity_RequestParams $requestParams)
    {
        $this->requestParams = $requestParams;
    }

    /**
     * {@inheritdoc}
     */
    public function getCollection()
    {
        if (null !== $this->collection) {
            return $this->collection;
        }

		$store = Mage::app()->getStore($this->requestParams->getStore());

        /** @var Mage_Catalog_Model_Resource_Product_Collection $collection */
        $collection = Mage::getModel('catalog/product')->getCollection();
        $collection->addAttributeToFilter('status', Mage_Catalog_Model_Product_Status::STATUS_ENABLED);

		// Set store, and filter by it
        $collection->setStoreId($store->getId());
        $collection->addStoreFilter();

		// Filter out products not visible on the site
		$collection->addAttributeToFilter('visibility', array('neq' => Mage_Catalog_Model_Product_Visibility::VISIBILITY_NOT_VISIBLE));

        if (!Mage::helper('searchspring_manager')->isOutOfStockIndexingEnabled()) {
            Mage::getSingleton('cataloginventory/stock')->addInStockFilterToCollection($collection);
        }

        $collection->addAttributeToSort('entity_id');

        $collection->getSelect()->limit($this->requestParams->getCount(), $this->requestParams->getOffset());
        $this->collection = $collection;

        return $this->collection;
    }

    /**
     * {@inheritdoc}
     */
    public function getCollectionCount()
    {
        if (null !== $this->collectionCount) {
            return $this->collectionCount;
        }

        // if collection isn't set, get it first
        if (null === $this->collection) {
            $this->getCollection();
        }

        // set collection count before limit is applied
        $this->collectionCount = $this->collection->getSize();

        return $this->collectionCount;
    }
}
