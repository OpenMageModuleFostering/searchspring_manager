<?php
/**
 * LabelsOperation.php
 *
 * @copyright B7 Interactive, LLC. All Rights Reserved.
 */

/**
 * Class SearchSpring_Manager_ThirdParty_Amasty_LabelsOperation
 *
 * Set Amasty labels HTML to the feed
 *
 * @author Will Wesley <will@b7interactive.com>
 */
class SearchSpring_Manager_ThirdParty_Amasty_LabelsOperation extends SearchSpring_Manager_Operation_Product
{
	const FEED_LABEL = 'amasty_label_product_labels_html';

	/**
	 * Add label HTML
	 *
	 * @param Mage_Catalog_Model_Product $product
	 * @return SearchSpring_Manager_Operation_ProductOperation|void
	 */
	public function perform(Mage_Catalog_Model_Product $product)
	{
		$this->getRecords()->add(self::FEED_LABEL, Mage::helper('amlabel')->getLabels($product));
		return $this;
	}
}
