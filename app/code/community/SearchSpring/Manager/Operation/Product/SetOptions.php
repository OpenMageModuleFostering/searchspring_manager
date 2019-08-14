<?php
/**
 * SetOptions.php
 *
 * @copyright B7 Interactive, LLC. All Rights Reserved.
 */

/**
 * Class SearchSpring_Manager_Operation_Product_SetOptions
 *
 * Set product options to the feed
 *
 * @author Nate Brunette <nate@b7interactive.com>
 */
class SearchSpring_Manager_Operation_Product_SetOptions extends SearchSpring_Manager_Operation_Product
{
    /**
     * Set magento product options to the feed
     *
     * @param Mage_Catalog_Model_Product $product
     *
     * @return $this
     */
    public function perform(Mage_Catalog_Model_Product $product)
    {
        /** @var Mage_Catalog_Model_Product_Option $option */
        foreach ($product->getOptions() as $option) {
            $optionType = $option->getType();

            if ($optionType !== Mage_Catalog_Model_Product_Option::OPTION_TYPE_DROP_DOWN) {
                continue;
            }

            $title = strtolower(trim($option->getData('title')));
            $title = preg_replace('/_+/', '_', preg_replace('/[^a-z0-9_]+/i', '_', $title));
            $key = 'option_' . $title;

            /** @var Mage_Catalog_Model_Product_Option_Value $optionValue */
            foreach ($option->getValues() as $optionValue) {
                $value = $this->getSanitizer()->sanitizeForRequest($optionValue->getData('title'));
                $this->getRecords()->add($key, $value);
            }
        }

        return $this;
    }
}
