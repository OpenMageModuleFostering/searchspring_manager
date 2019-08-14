<?php
class SearchSpring_Manager_Model_Catalog_Category_Attribute_EnableSearchSpring_Source extends Mage_Eav_Model_Entity_Attribute_Source_Abstract {

	const VALUE_CONFIG = 0;
	const VALUE_DISABLED = 1;
	const VALUE_ENABLED = 2;

	public function getAllOptions($withEmpty = true)
	{
		$options = array(
			array('label' => 'Use Store SearchSpring Config', 'value' => self::VALUE_CONFIG),
			array('label' => 'Disabled', 'value' => self::VALUE_DISABLED),
			array('label' => 'Enabled', 'value' => self::VALUE_ENABLED)
		);

		return $options;
	}
}
