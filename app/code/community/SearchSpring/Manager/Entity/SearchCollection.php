<?php
/**
 * File SearchRequest.php
 *
 * @copyright B7 Interactive, LLC. All Rights Reserved.
 */

/**
 * Class SearchSpring_Manager_Entity_SearchCollection
 *
 * The class models a SearchSpring SearchCollection
 *
 * @author James Bathgate <james@b7interactive.com>
 */
class SearchSpring_Manager_Entity_SearchCollection extends Mage_Catalog_Model_Resource_Collection_Abstract
{
	protected $_api;
	protected $_requestBody;

	protected $_results;

	protected $_loaded = false;

	const SORT_ORDER_ASC = 'asc';
	const SORT_ORDER_DESC = 'desc';

	/**
	 * @param SearchSpring_Manager_Service_SearchSpring_SearchApiAdapter $api
	 * @param SearchSpring_Manager_Entity_SearchRequestBody $requestBody
	 */

	public function __construct(
		SearchSpring_Manager_Service_SearchSpring_SearchApiAdapter $api,
		SearchSpring_Manager_Entity_SearchRequestBody $requestBody
	) {
		$this->_api = $api;
		$this->_requestBody = $requestBody;
	}

	/*
	 * $type will be ignored since it's handled by the SMC settings
	 */
	public function addFilter($field, $value, $type='and') {
		$this->_requestBody->addFilter($field, $value);
		return $this;
	}

	public function addBackgroundFilter($field, $value) {
		$this->_requestBody->addBackgroundFilter($field, $value);
		return $this;
	}

	public function addFilterOnly($field, $values) {
		foreach($values as $value) {
			$this->_requestBody->addFilterOnly($field, $value);
		}
	}

	public function setCurPage($page) {
		$this->_requestBody->setPage($page);
		return $this;
	}

	public function getCurPage($offset=0) {
		return $this->_requestBody->getPage()+$offset;
	}

	public function setPageSize($size) {
		$this->_requestBody->setResultsPerPage($size);
		return $this;
	}

	public function getPageSize() {
		return $this->_requestBody->getResultsPerPage();
	}

	public function setOrder($field, $dir = self::SORT_ORDER_ASC) {
		$this->_requestBody->setSort($field, $dir);
		return $this;
	}

	public function load($printQuery = false, $logQuery = false) {
		if(!$this->_loaded) {
			$this->_results = new SearchSpring_Manager_Entity_SearchResult(
				$this->_api->search($this->_requestBody)
			);

			$this->_loaded = true;
		}

		return $this;
	}

	public function count() {
		return $this->_results->getNumResults();
	}

	public function getSize() {
		return $this->_results->getNumResults();
	}

	public function getLastPageNumber() {
		return ceil($this->_results->getNumResults() / $this->_results->getPerPage());
	}

	public function getResults() {
		if(!$this->_loaded) {
			$this->load();
		}

		return $this->_results;
	}

	public function getMerchandisingContent() {
		return $this->_results->getMerchandising();
	}

	public function getSorting() {
		return $this->_results->getSorting();
	}

	public function getFilterSummary() {
		return $this->_results->getFilterSummary();
	}


	/**
	 * SearchSpring automatically includes counts, so we do nothing.
	 *
	 * @param Mage_Eav_Model_Entity_Collection_Abstract $categoryCollection
	 * @return Mage_Catalog_Model_Resource_Product_Collection
	 */
	public function addCountToCategories($categoryCollection)
	{
		return $this;
	}

}
