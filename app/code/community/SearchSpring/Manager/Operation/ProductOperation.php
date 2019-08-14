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
     * Perform an operation
     *
     * @param Mage_Catalog_Model_Product $product
     *
     * @return self
     */
    public function perform(Mage_Catalog_Model_Product $product);

    /**
     * Checks validity of operation
     *
     * @param Mage_Catalog_Model_Product $product
     *
     * @return mixed
     */
    public function isValid(Mage_Catalog_Model_Product $product);

    /**
     * Set records collection to operation
     *
     * @param SearchSpring_Manager_Entity_RecordsCollection $records
     *
     * @return mixed
     */
    public function setRecords(SearchSpring_Manager_Entity_RecordsCollection $records);
}
