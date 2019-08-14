<?php
/**
 * File IndexingRequestBody.php
 *
 * @copyright B7 Interactive, LLC. All Rights Reserved.
 */

/**
 * Class SearchSpring_Manager_Entity_SearchRequestBody
 *
 * The class models a SearchSpring Search API request body
 *
 * @author James Bathgate <james@b7interactive.com>
 */
class SearchSpring_Manager_Entity_SearchRequestBody extends SearchSpring_Manager_Entity_RequestBody
{

	protected $_siteId;

	protected $_filters = array();
	protected $_backgroundFilters = array();

	protected $_filterOnly = array();

	protected $_page = 1;
	protected $_resultsPerPage = 30;

	protected $_sortField;
	protected $_sortDir = 'asc';

    /**
     * Constructor
     */
    public function __construct($siteId)
    {
		$this->_siteId = $siteId;
    }

	public function addFilter($field, $value, $append = false) {
		if(!isset($this->_filters[$field])) {
			$this->_filters[$field] = $value;
		} else {
			if(!is_array($this->_filters[$field])) {
				$this->_filters[$field] = array($this->_filters[$field]);
			}

			$this->_filters[$field][] = $value;
		}
	}

	public function addBackgroundFilter($field, $value, $append = false) {
		if(!isset($this->_backgroundFilters[$field])) {
			$this->_backgroundFilters[$field] = $value;
		} else {
			if(!is_array($this->_backgroundFilters[$field])) {
				$this->_backgroundFilters[$field] = array($this->_backgroundFilters[$field]);
			}

			$this->_backgroundFilters[$field][] = $value;
		}
	}

	public function addFilterOnly($field, $value) {
		if(!isset($this->_filterOnly[$field])) {
			$this->_filterOnly[$field] = $value;
		} else {
			if(!is_array($this->_filterOnly[$field])) {
				$this->_filterOnly[$field] = array($this->_filterOnly[$field]);
			}

			$this->_filterOnly[$field][] = $value;
		}
	}

	public function setPage($page) {
		$this->_page = $page;
	}

	public function getPage() {
		return $this->_page;
	}

	public function setResultsPerPage($rpp) {
		$this->_resultsPerPage = $rpp;
	}

	public function getResultsPerPage() {
		return $this->_resultsPerPage;
	}

	public function setSort($field, $dir) {
		$this->_sortField = $field;
		$this->_sortDir = $dir;
	}

	public function __toString() {
		$parameters = array(
			'siteId' => $this->_siteId,
			'page' => $this->_page,
			'resultsPerPage' => $this->_resultsPerPage
		);

		foreach($this->_filters as $field => $value) {
			$parameters['filter.' . $field] = $value;
		}

		foreach($this->_backgroundFilters as $field => $value) {
			$parameters['bgfilter.' . $field] = $value;
		}

		foreach($this->_filterOnly as $field => $value) {
			$parameters['filter.' . $field . '.only'] = $value;
		}

		if(!is_null($this->_sortField)) {
			$parameters['sort.' . $this->_sortField] = $this->_sortDir;
		}

		$url = http_build_query($parameters);

		// remove [] from multi-valued filters
		$url = preg_replace('/\%5B\d+\%5D/', '', $url);

		return $url;
	}

}
