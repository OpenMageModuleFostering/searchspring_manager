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

            if(in_array($key, $this->_globalReservedFields)) {
                $key = 'ss_mage_attr_' . $key;
            }

			$value = $this->getAttributeValue($product, $attribute);
			if (is_array($value)) {
				foreach($value as $v) {
					$this->getRecords()->add($key, $v);
				}
			} else if ($value !== false) {
				$this->getRecords()->set($key, $value);
			}
        }

        return $this;
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
		$attributeValue = Mage::helper('searchspring_manager/product')->getAttributeText($product, $attribute);

		if (is_array($attributeValue)) {
			$returnValue = null;
			foreach ($attributeValue as $v) {
				$returnValue[] = json_encode($v);
			}
		} else if ($attributeValue !== false && $attributeValue !== null) {
			$returnValue = $this->getSanitizer()->sanitizeForRequest($attributeValue);
		} else {
			// This means there should be no value for this attribute
			$returnValue = false;
		}

		return $returnValue;
	}
}
