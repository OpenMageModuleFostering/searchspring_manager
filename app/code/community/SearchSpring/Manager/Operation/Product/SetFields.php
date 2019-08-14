<?php
/**
 * SetFields.php
 *
 * @copyright B7 Interactive, LLC. All Rights Reserved.
 */

/**
 * Class SearchSpring_Manager_Operation_Product_SetFields
 *
 * Set product attributes to the field
 *
 * @author Nate Brunette <nate@b7interactive.com>
 */
class SearchSpring_Manager_Operation_Product_SetFields extends SearchSpring_Manager_Operation_Product
{
    /**
     * Add magento product attributes to the feed
     *
     * @param Mage_Catalog_Model_Product $product
     * @return $this
     */
    public function perform(Mage_Catalog_Model_Product $product)
    {
        /** @var Mage_Catalog_Model_Resource_Eav_Attribute $attribute */
        foreach($product->getAttributes() as $key => $attribute) {
			$value = $this->getAttributeValue($product, $attribute);
			if (!is_array($value)) {
				$this->getRecords()->add($key, $value);
			} else {
				foreach($value as $v) {
					$this->getRecords()->add($key, $v);
				}
			}
        }

        return $this;
    }

    /**
     * If product is not enabled or visible, set invalid
     *
     * @param Mage_Catalog_Model_Product $product
     *
     * @return bool
     */
    public function isValid(Mage_Catalog_Model_Product $product)
    {
        // product must be enabled
        if ((int)$product->getData('status') !== Mage_Catalog_Model_Product_Status::STATUS_ENABLED) {
            return false;
        }

        // product must be visible in catalog and search
        if ((int)$product->getData('visibility') !== Mage_Catalog_Model_Product_Visibility::VISIBILITY_BOTH) {
            return false;
        }

        return true;
    }

    /**
     * Get the attribute value from the attribute object
     *
     * @param Mage_Catalog_Model_Product $product
     * @param Mage_Eav_Model_Entity_Attribute $attribute
     *
     * @return array|string|null
     */
	private function getAttributeValue(Mage_Catalog_Model_Product $product, Mage_Eav_Model_Entity_Attribute $attribute)
	{
		$attributeValue = $attribute->getFrontend()->getValue($product);
		$returnValue = null;

		if (is_array($attributeValue)) {
			foreach ($attributeValue as $v) {
				$returnValue[] = json_encode($v);
			}
		} else {
			$returnValue = $this->getSanitizer()->sanitizeForRequest($attributeValue);
		}

		return $returnValue;
	}
}
