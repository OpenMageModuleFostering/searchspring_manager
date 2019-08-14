<?php

class SearchSpring_Manager_Model_Layer_Filter_SearchSpringCategoryItem extends SearchSpring_Manager_Model_Layer_Filter_SearchSpringItem
{

	/**
	 * Get filter item url
	 *
	 * @return string
	 */
	public function getUrl()
	{

		$query = array(
			Mage::getBlockSingleton('page/html_pager')->getPageVarName() => null // exclude current page from urls
		);

		$url = Mage::getUrl('',
			array(
				'_current' => true,
				'_direct' => Mage::getModel('core/url_rewrite')->loadByIdPath('category/' . $this->getValue())->getRequestPath(),
				'_query' => $query
			)
		);

		return $url;
	}

}
