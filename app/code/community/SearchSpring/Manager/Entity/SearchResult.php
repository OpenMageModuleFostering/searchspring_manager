<?php
/**
 * File SearchResult.php
 *
 * @copyright B7 Interactive, LLC. All Rights Reserved.
 */

/**
 * Class SearchSpring_Manager_Entity_SearchResult
 *
 * The class models a SearchSpring Search API result body
 *
 * @author James Bathgate <james@b7interactive.com>
 */
class SearchSpring_Manager_Entity_SearchResult
{

	protected $_results = array();

	protected $_pagination;

	/**
	 * Constructor
	 *
	 * @param mixed JSON decoded SearchSpring search result
	 */
	public function __construct($results)
	{
//		print "<pre>"; var_dump($results);print "</pre>";exit;
		$this->_pagination = $results->pagination;
		$this->_results = $results->results;
		$this->_facets = $results->facets;
		$this->_merchandising = $results->merchandising;
		$this->_sorting = $results->sorting;
		$this->_filterSummary = $results->filterSummary;
	}

	public function getResults() {
		return $this->_results;
	}

	public function getFacets() {
		return $this->_facets;
	}

	public function getNumResults() {
		return $this->_pagination->totalResults;
	}

	public function getPerPage() {
		return $this->_pagination->perPage;
	}

	public function getMerchandisingContent() {
		if(isset($this->_merchandising->content)) {
			return $this->_merchandising->content;
		} else {
			return new stdClass();
		}
	}

	public function getSorting() {
		return $this->_sorting->options;
	}

	public function getFilterSummary() {
		return $this->_filterSummary;
	}
}
