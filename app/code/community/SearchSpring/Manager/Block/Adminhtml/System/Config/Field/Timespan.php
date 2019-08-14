<?php

class SearchSpring_Manager_Block_Adminhtml_System_Config_Field_Timespan extends Mage_Adminhtml_Block_System_Config_Form_Field
{

	/**
	 * Get country selector html
	 *
	 * @param Varien_Data_Form_Element_Abstract $element
	 * @return string
	 */
	protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
	{

		// Get the value for the main element
		$values = explode(',', $element->getValue());
		$number = array_shift($values);
		$increment = array_shift($values);

		// Set the Value for the increment dropdown, year, month, day...
		$element->setValue($increment);
		$element->setName( $element->getName() . "[]" );
		$element->setStyle('width: 211px;');

		$box = new Varien_Data_Form_Element_Text(array(
			'name' => $element->getName(),
			'value' => "need to figure this out",
			'no_span' => true,
			'style' => 'width: 60px;',
		));
		$box->setForm($element->getForm());
		// Set the Value for the number
		$box->setValue($number);

		// Put them together
		return $box->getHtml(). $element->getElementHtml();
	}

}
