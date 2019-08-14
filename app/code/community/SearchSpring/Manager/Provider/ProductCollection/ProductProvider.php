<?php
/**
 * ProductProvider.php
 *
 * @copyright B7 Interactive, LLC. All Rights Reserved.
 */

/**
 * Class SearchSpring_Manager_Provider_ProductCollection_ProductProvider
 *
 * Provides a ProductCollection for product types
 *
 * @author Nate Brunette <nate@b7interactive.com>
 */
class SearchSpring_Manager_Provider_ProductCollection_ProductProvider
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
     * An array of product ids
     *
     * @var array $ids
     */
    private $ids;

    /**
     * Request parameters
     *
     * Needed to get current count/offset values
     *
     * @var SearchSpring_Manager_Entity_RequestParams
     */
    private $requestParams;

    /**
     * Constructor
     *
     * @param array $ids
     * @param SearchSpring_Manager_Entity_RequestParams $requestParams
     */
    public function __construct(array $ids, SearchSpring_Manager_Entity_RequestParams $requestParams = null)
    {
        $this->ids = $ids;
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
        $collection = Mage::getModel('catalog/product')
            ->getCollection()
			->addAttributeToFilter('entity_id', array('in' => $this->ids))
		;

		// Set store, and filter by it
        $collection->setStoreId($store->getId());
        $collection->addStoreFilter();

		// Filter out products not visible on the site
		$collection->addAttributeToFilter('visibility', array('neq' => Mage_Catalog_Model_Product_Visibility::VISIBILITY_NOT_VISIBLE));

        if (null !== $this->requestParams) {
            $collection->addAttributeToSort('entity_id');
            $collection->getSelect()->limit($this->requestParams->getCount(), $this->requestParams->getOffset());
        }

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

        if (null === $this->collection) {
            $this->getCollection();
        }

        $this->collectionCount = $this->collection->getSize();

        return $this->collectionCount;
    }
}
