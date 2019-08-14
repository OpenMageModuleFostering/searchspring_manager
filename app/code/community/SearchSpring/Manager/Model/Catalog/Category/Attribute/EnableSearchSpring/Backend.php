<?php

class SearchSpring_Manager_Model_Catalog_Category_Attribute_EnableSearchSpring_Backend extends Mage_Eav_Model_Entity_Attribute_Backend_Abstract
{

	public function afterLoad($object)
	{
		parent::afterLoad($object);
		$attrCode = $this->getAttribute()->getAttributeCode();
		$rawValue = $object->getData($attrCode);
		switch ($rawValue) {
			case SearchSpring_Manager_Model_Catalog_Category_Attribute_EnableSearchSpring_Source::VALUE_ENABLED:
				$boolValue = true;
				break;
			case SearchSpring_Manager_Model_Catalog_Category_Attribute_EnableSearchSpring_Source::VALUE_DISABLED:
				$boolValue = false;
				break;
			default:
				$boolValue = Mage::helper('searchspring_manager')->isCategorySearchEnabled();
				break;
		}
		$object->setData('is_search_spring_enabled', $boolValue);
		return $this;
	}

}
