<?php
/**
 * File Product.php
 *
 * @copyright B7 Interactive, LLC. All Rights Reserved.
 */

/**
 * Class SearchSpring_Manager_Helper_Product
 *
 * Some helpful utilities related to products
 *
 * @author Jake Shelby <jake@searchspring.com>
 */
class SearchSpring_Manager_Helper_Product extends Mage_Core_Helper_Abstract
{

	public function getAttributeText($product, $attributeCode) {

		if ($attributeCode instanceof Mage_Eav_Model_Entity_Attribute) {
			$attribute = $attributeCode;
			$attributeCode = $attribute->getAttributeCode();
		} else {
			$attribute = $product->getResource()->getAttribute($attributeCode);
		}

		if (!is_object($attribute)) {
			throw new Exception("Unknown attribute: $attributeCode");
		}

		if ($attribute->getFrontendInput() === 'boolean' ||
			$attribute->getFrontendInput() === 'select'
		) {
			// Magento's Attribute Frontend getValue() function
			// returns 'No' for optional attributes with nothing
			// set, specifically for these types...
			// So we'll just try and resolve from the attribute
			// options map, or empty if the value isn't set.
			return $product->getAttributeText($attributeCode);

		} else if ($attribute->getFrontendInput() === 'date') {
			// dates are the special
			return $product->getData($attributeCode);

		} else {

			return $attribute->getFrontend()->getValue($product);

		}

	}

}
