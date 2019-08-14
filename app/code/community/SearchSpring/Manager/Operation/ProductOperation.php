<?php
/**
 * Operation.php
 *
 * @copyright B7 Interactive, LLC. All Rights Reserved.
 */

/**
 * Interface SearchSpring_Manager_Visitor_Product_Operation
 *
 * @author Nate Brunette <nate@b7interactive.com>
 */
interface SearchSpring_Manager_Operation_ProductOperation
{

    /**
	 * Prepare the collection, before it's loaded.
	 * This allows for the operation to add filters,
	 * joins, extra data to the collection before
	 * it's loaded.
     *
     * @param Mage_Catalog_Model_Resource_Product_Collection $productCollection
     *
     * @return self
     */
	public function prepareCollection($productCollection);

    /**
	 * Prepare the operation after the collection has
	 * been loaded, but before isValid and perform have
	 * been called. This allows for pulling in data in
	 * mass, since you have been called ahead of time
	 * with the products that you will be performing
	 * operations on.
     *
     * @param Mage_Catalog_Model_Resource_Product_Collection $productCollection
     *
     * @return self
     */
	public function prepare($productCollection);

    /**
     * Checks validity of operation
     *
     * @param Mage_Catalog_Model_Product $product
     *
     * @return mixed
     */
    public function isValid(Mage_Catalog_Model_Product $product);

    /**
     * Perform an operation
     *
     * @param Mage_Catalog_Model_Product $product
     *
     * @return self
     */
    public function perform(Mage_Catalog_Model_Product $product);

    /**
	 * Set records collection to operation. This is the
	 * collection that output data should be written to.
     *
     * @param SearchSpring_Manager_Entity_RecordsCollection $records
     *
     * @return mixed
     */
    public function setRecords(SearchSpring_Manager_Entity_RecordsCollection $records);
}
