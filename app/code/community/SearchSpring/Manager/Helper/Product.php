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

		$attribute = $product->getResource()->getAttribute($attributeCode);

		// If the attribute type uses a set number of options, we need to resolve the id
		if ($attribute->getFrontendInput() === 'select' ||
			$attribute->getFrontendInput() === 'multiselect'
		) {
			return $product->getAttributeText($attributeCode);
		} else {
			return $product->getData($attributeCode);
		}

	}

}
