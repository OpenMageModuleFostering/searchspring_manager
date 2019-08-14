<?php
/**
 * SetCoreFields.php
 *
 * @copyright B7 Interactive, LLC. All Rights Reserved.
 */

/**
 * Class SearchSpring_Manager_Operation_Product_SetCoreFields
 *
 * Set SearchSpring core fields to the feed
 *
 * @author Nate Brunette <nate@b7interactive.com>
 */
class SearchSpring_Manager_Operation_Product_SetCoreFields extends SearchSpring_Manager_Operation_Product
{
    /**#@+
     * Feed constants
     */
    const FEED_SKU = 'sku';
    const FEED_PRODUCT_TYPE = 'product_type';
    const FEED_DESCRIPTION = 'description';
    const FEED_SHORT_DESCRIPTION = 'short_description';
    const FEED_QUANTITY = 'quantity';
    const FEED_IN_STOCK = 'in_stock';
    const FEED_WEIGHT = 'weight';
    const FEED_MANUFACTURER = 'manufacturer';
    const FEED_URL = 'url';
    const FEED_NAME = 'name';
    const FEED_CHILD_QUANTITY = 'child_quantity';
    const FEED_CHILD_SKU = 'child_sku';
    const FEED_CHILD_NAME = 'child_name';
    /**#@-*/



    /**
     * Add SearchSpring core fields to the feed
     *     - sku
     *     - product_type
     *     - quantity
     *     - in_stock
     *     - weight
     *     - manufacturer
     *     - url
     *     - url
     *     - image_url
     *     - thumbnail_url
     *     - cached_thumbnail_url
     *     - name
     *     - description
     *     - short_description
     *     - child_quantity
     *     - child_name
     *     - child_sku
     *
     * @param Mage_Catalog_Model_Product $product
     *
     * @return $this
     */
    public function perform(Mage_Catalog_Model_Product $product)
    {
        $webBaseUrl = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB);

        $this->getRecords()->set(self::FEED_SKU, $product->getSku());
        $this->getRecords()->set(self::FEED_PRODUCT_TYPE, $product->getData('type_id'));
        $this->getRecords()->set(self::FEED_QUANTITY, number_format($this->getQuantity($product)));
        $this->getRecords()->set(self::FEED_IN_STOCK, $product->isInStock());
        $this->getRecords()->set(self::FEED_WEIGHT, number_format((double)$product->getWeight(), 2));
        $this->getRecords()->set(self::FEED_MANUFACTURER, $product->getAttributeText('manufacturer'));
        $this->getRecords()->set(self::FEED_URL, $webBaseUrl . $product->getUrlPath());

        $productName = $this->getSanitizer()->removeNewlinesAndStripTags($product->getName());
        $description = $this->getSanitizer()->sanitizeForRequest($product->getData('description'));
        $shortDescription = $this->getSanitizer()->sanitizeForRequest($product->getData('short_description'));
        $this->getRecords()->set(self::FEED_NAME, $productName);
        $this->getRecords()->set(self::FEED_DESCRIPTION, $description);
        $this->getRecords()->set(self::FEED_SHORT_DESCRIPTION, $shortDescription);

        $this->setChildQuantity($product);

        return $this;
    }

    /**
     * Helper method to get the quantity from a product
     *
     * @param Mage_Catalog_Model_Product $product
     * @return int
     */
    private function getQuantity(Mage_Catalog_Model_Product $product)
    {
        $quantity = $product->getData('stock_item')->getData('qty');

        return (int)$quantity;
    }

    /**
     * If the product is composite, find the child quantities and set that to the field
     */
    private function setChildQuantity(Mage_Catalog_Model_Product $product)
    {
        // default to normal quantity
        $childQuantity = $this->getRecords()->get(self::FEED_QUANTITY);

        // find the child quantity if it exists
        switch ($product->getTypeId()) {
            case Mage_Catalog_Model_Product_Type::TYPE_GROUPED:
                /** @var Mage_Catalog_Model_Product_Type_Grouped $typeInstance */
                $typeInstance = $product->getTypeInstance(true);
                $associated = $typeInstance->getAssociatedProducts($product);
                $childQuantity = $this->getQuantityForChildren($associated);

                break;
            case Mage_Catalog_Model_Product_Type::TYPE_CONFIGURABLE:
                /** @var Mage_Catalog_Model_Product_Type_Configurable $typeInstance */
                $typeInstance = $product->getTypeInstance(true);

                /** @var Mage_Catalog_Model_Product_Type_Configurable_Attribute $attribute */
                foreach ($typeInstance->getConfigurableAttributesAsArray($product) as $attribute) {
                    foreach ($attribute['values'] as $value) {
                        $this->getRecords()->add($attribute['attribute_code'], $value['label']);
                    }
                }

                $children = $typeInstance->getUsedProducts(null, $product);
                $childQuantity = $this->getQuantityForChildren($children);

                break;
            case Mage_Catalog_Model_Product_Type::TYPE_BUNDLE:
                /** @var Mage_Bundle_Model_Product_Type $typeInstance */
                $typeInstance = $product->getTypeInstance(true);
                $optionsIds = $typeInstance->getOptionsIds($product);
                $selections = $typeInstance->getSelectionsCollection($optionsIds, $product);
                $bundleOptions = $typeInstance->getOptionsByIds($optionsIds, $product);
                $bundleOptions->appendSelections($selections);

                /** @var Mage_Bundle_Model_Option $bundleOption */
                foreach ($bundleOptions as $bundleOption) {
                    $childQuantity += $this->getQuantityForChildren($bundleOption->getData('selections'));
                }

                break;
        }

        $this->getRecords()->set(self::FEED_CHILD_QUANTITY, $childQuantity);
    }

    /**
     * Loop over an array of products to set field data and calculate total child quantity
     *
     * @param array $products
     * @return int
     */
    private function getQuantityForChildren(array $products)
    {
        $quantity = 0;

        /** @var Mage_Catalog_Model_Product $product */
        foreach ($products as $product) {
            $this->getRecords()->add(self::FEED_CHILD_SKU, $product->getSku());
            $this->getRecords()->add(self::FEED_CHILD_NAME, $product->getName());

            $quantity += $this->getQuantity($product);
        }

        return $quantity;
    }
}
