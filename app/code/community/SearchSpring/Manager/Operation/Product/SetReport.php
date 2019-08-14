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

	protected $_localReservedFields = array(
		self::FEED_CART_COUNT,
		self::FEED_ORDERS_COUNT,
		self::FEED_ORDERS_QTY
	);

	/**
	 * Feed constants
	 */
	const FEED_CART_COUNT	= 'report_cart_count';
	const FEED_ORDERS_COUNT	= 'report_orders_count';
	const FEED_ORDERS_QTY	= 'report_orders_qty';

	public function prepare($productCollection) {

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
		if (is_null($this->_reportData)) {
			// If we don't have data, then we'll fetch it with the requested product
			// NOTE: This should only happen if the person using the class didn't request
			// preparation with a product collection
			$this->fetchReportData(array($product->getId()), $product->getStoreId());

			// If we still don't have the data, then we can't support this feature
			if (is_null($this->_reportData)) {
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

		// Skip Urigy Dropship Multi Load if they have it installed
		$reportCollection->setFlag('skip_udmulti_load', true);

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

	public function createReportCollection($productIds, $store) {

		// Make sure we have a valid parameter
		if (!$this->isTimespanValid()) {
			return;
		}

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

	public function getParamReportStartDate() {

		$fromTime = strtotime('-' . $this->getTimespan());

		// If we can't convert the timespan to a time, we can't do anything with it
		if ($fromTime === false) {
			// TODO -- log when we have a debug logger
			// Mage::helper('searchspring_manager/debug')->log("Can't convert timespan to time: " . $this->getTimespan());
			return '';
		}

		return date('Y-m-d H:i:s', $fromTime);
	}

	public function getParamReportEndDate() {

		// Up till now
		return date('Y-m-d H:i:s');
	}

	public function getTimespan() {
		$timespan = $this->getParameter('timespan');
		return implode(' ', explode(',', $timespan) );
	}

	public function getTimespanNumber() {
		$timespan = $this->getParameter('timespan');
		$elements = explode(',', $timespan);
		$number = count($elements) > 1 ? reset($elements) : null;
		return trim($number);
	}

	public function isTimespanValid() {
		$timespanNumber = $this->getTimespanNumber();

		// Shouldn't be empty (0, null, '')
		if (empty($timespanNumber)) {
			return false;
		}

		// Should be numeric
		if (!is_numeric($timespanNumber)) {
			return false;
		}

		return true;
	}

}
