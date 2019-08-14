<?php
/**
 * File ProductDeletionToken.php
 *
 * @copyright B7 Interactive, LLC. All Rights Reserved.
 */

/**
 * Class SearchSpring_Manager_Entity_ProductDeletionToken
 *
 * Token that represents the prepped data needed for the deletion of a product
 *
 * @author Jake Shelby <jake@b7interactive.com>
 */
class SearchSpring_Manager_Entity_ProductDeletionToken
{

    /**
     * Product ID to be deleted
     *
     * @var int $product
     */
    protected $productId;

    /**
     * Stores affected
     *
     * @var array $stores
     */
    protected $stores;

    /**
     * Product IDs related, affected by deletion
     *
     * @var int $product
     */
    protected $relatedProductIds;

    /**
     * Constructor
     *
     * @param int $productId
     * @param array $stores
     * @param array $relatedProductIds optional
     */
    public function __construct($productId, array $stores, array $relatedProductIds = array())
    {
		$this->productId = $productId;
		$this->stores = $stores;
		$this->relatedProductIds = $relatedProductIds;
    }

	public function getProductId()
	{
		return $this->productId;
	}

	public function getStores()
	{
		return $this->stores;
	}

	public function getRelatedProductIds()
	{
		return $this->relatedProductIds;
	}

}
