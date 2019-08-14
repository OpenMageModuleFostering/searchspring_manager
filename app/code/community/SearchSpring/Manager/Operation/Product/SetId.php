<?php
/**
 * SetId.php
 *
 * @copyright B7 Interactive, LLC. All Rights Reserved.
 */

/**
 * Class SearchSpring_Manager_Operation_Product_SetId
 *
 * Set the product id to the feed
 *
 * @author Nate Brunette <nate@b7interactive.com>
 */
class SearchSpring_Manager_Operation_Product_SetId extends SearchSpring_Manager_Operation_Product
{
    /**
     * Set product id to the feed
     *
     * @param Mage_Catalog_Model_Product $product
     *
     * @return $this
     */
    public function perform(Mage_Catalog_Model_Product $product)
    {
        $this->getRecords()->set($product->getIdFieldName(), $product->getId());

        return $this;
    }
}
