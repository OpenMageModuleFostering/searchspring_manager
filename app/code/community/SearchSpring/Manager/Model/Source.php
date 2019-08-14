<?php

class SearchSpring_Manager_Model_Source extends SearchSpring_Manager_Model_Source_Abstract
{

	public function toOptionHash($selector=false)
	{
		$hlp = Mage::helper('searchspring_manager');

		$options = array();

		switch ($this->getPath()) {

			case SearchSpring_Manager_Model_Config::PATH_SALES_RANK_TIMESPAN:

				$options = array(
					'day'	=> $hlp->__('Day(s)'),
					'week'	=> $hlp->__('Week(s)'),
					'month'	=> $hlp->__('Month(s)'),
					'year'	=> $hlp->__('Year(s)'),
				);

				break;

			default:
				Mage::throwException($hlp->__('Invalid request for source options: '.$this->getPath()));

		}

		if ($selector) {
			$options = array(''=>$hlp->__('* Please select')) + $options;
		}

		return $options;
	}

}

