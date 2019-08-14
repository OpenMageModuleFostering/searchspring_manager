<?php
/**
 * ProductCollectionProvider.php
 *
 * @copyright B7 Interactive, LLC. All Rights Reserved.
 */

/**
 * Interface SearchSpring_Manager_Provider_ProductCollectionProvider
 *
 * @author Nate Brunette <nate@b7interactive.com>
 */
interface SearchSpring_Manager_Provider_ProductCollectionProvider
{
    /**
     * Gets the ProductCollection
     *
     * @return Mage_Catalog_Model_Resource_Product_Collection
     */
    public function getCollection();

    /**
     * Get the collection count
     *
     * @return int
     */
    public function getCollectionCount();
}
