<?php
/**
 * SetReport.php
 *
 * @copyright B7 Interactive, LLC. All Rights Reserved.
 */

/**
 * Class SearchSpring_Manager_Operation_Product_SetReport
 *
 * Set Sales/Customer/Reporting related product data
 *
 * Available reporting functions (from CE version 1.3)
 *   public function addCartsCount()
 *   public function addOrdersCount($from = '', $to = '')
 *   public function addOrderedQty($from = '', $to = '')
 *   public function addViewsCount($from = '', $to = '')
 *
 * @author Jake Shelby <jake@b7interactive.com>
 */
class SearchSpring_Manager_Operation_Product_SetReport extends SearchSpring_Manager_Operation_Product
{

	protected $_enabled = true;
	protected $_reportData;

	/**
	 * Feed constants
	 */
	const FEED_CART_COUNT	= 'report_cart_count';
	const FEED_ORDERS_COUNT	= 'report_orders_count';
	const FEED_ORDERS_QTY	= 'report_orders_qty';

	// Default timespan, 1 year, so we don't accidentally overload their database
	const DEFAULT_REPORT_TIMESPAN = '1 year';

	public function prepare(Mage_Catalog_Model_Resource_Product_Collection $productCollection) {

		$this->fetchReportData(
			$productCollection->getAllIds(),
			$productCollection->getStoreId()
		);

		return $this;
	}

	public function perform(Mage_Catalog_Model_Product $product)
	{

		if ($this->_enabled) {

			$this->setOrderedQty($product);

			// We're not using these just yet
			// $this->setCartCount($product);
			// $this->setOrdersCount($product);

		}

		return $this;
	}

	public function setCartCount(Mage_Catalog_Model_Product $product) {
		if ($productReport = $this->getProductReport($product)) {
			$this->getRecords()->set(self::FEED_CART_COUNT, $productReport->getCounts());
		}
	}

	public function setOrdersCount(Mage_Catalog_Model_Product $product) {
		if ($productReport = $this->getProductReport($product)) {
			$this->getRecords()->set(self::FEED_ORDERS_COUNT, $productReport->getOrders());
		}
	}

	public function setOrderedQty(Mage_Catalog_Model_Product $product) {
		if ($productReport = $this->getProductReport($product)) {
			$this->getRecords()->set(self::FEED_ORDERS_QTY, $productReport->getOrderedQty());
		}
	}

	public function getProductReport(Mage_Catalog_Model_Product $product) {

		// Make sure we have report data
		if (!$this->_reportData) {
			// If we don't have data, then we'll fetch it with the requested product
			// NOTE: This should only happen if the person using the class didn't request
			// preparation with a product collection
			$this->fetchReportData(array($product->getId()), $product->getStoreId());

			// If we still don't have the data, then we can't support this feature
			if (!$this->_reportData) {
				return false;
			}
		}

		// Make sure we have data for this product
		if (!isset($this->_reportData[$product->getId()])) {
			return false;
		}

		// Return as an object
		return new Varien_Object($this->_reportData[$product->getId()]);
	}

	protected function fetchReportData($productIds, $store) {

		// Start by using the reports collection
		$reportCollection = $this->createReportCollection($productIds, $store);

		// If we don't have a collection now, we won't ever have one, disable this operation
		if (!$reportCollection) {
			$this->_enabled = false;
			$this->_reportData = null;
			return;
		}

		// Get From and To Dates
		$from = $this->getParamReportStartDate();
		$to = $this->getParamReportEndDate();

		// Start our resulting data
		$reportData = array();

		// Ordered Qty
		$reportCollection->addOrderedQty($from,$to);
		foreach($reportCollection as $productReport) {
			$reportData[$productReport->getId()] = array(
				'ordered_qty'	=> $productReport->getOrderedQty(),
			);
		}

/*		// Order Counts - Add this in when needed
		$reportCollection = $this->createReportCollection($productIds, $store);
		$reportCollection->addOrdersCount($from,$to); // TODO Figure out how to add this without breaking the query
		foreach($reportCollection as $productReport) {
			$reportData[$productReport->getId()] = array(
				'orders'		=> $productReport->getOrders(),
			);
		} */

/*		// Cart Counts - Add this in when needed
		$reportCollection = $this->createReportCollection($productIds, $store);
		$reportCollection->addCartsCount(); // TODO Figure out how to add this without breaking the query
		foreach($reportCollection as $productReport) {
			$reportData[$productReport->getId()] = array(
				'counts'		=> $productReport->getCounts(),
			);
		} */

		$this->_reportData = $reportData;
	}

	protected function createReportCollection($productIds, $store) {

		// Start by using the reports collection
		$reportCollection = Mage::getResourceModel('reports/product_collection');

		// Make sure this magento installation has this report collection
		if (!is_object($reportCollection)) {
			return;
		}

		// Filter By Store ID
		$reportCollection->setStoreId($store)->addStoreFilter($store);

		// Filter to just certain products
		$reportCollection->addAttributeToFilter('entity_id', array('in' => $productIds));

		return $reportCollection;
	}

	// TODO -- Figure out how to add from and to dates as parameters, might need to add them as configuration items as well
	public function getParamReportStartDate() {
		return date('Y-m-d H:i:s', strtotime('-' . self::DEFAULT_REPORT_TIMESPAN));
	}

	public function getParamReportEndDate() {
		// Up till now
		return date('Y-m-d H:i:s');
	}

}
