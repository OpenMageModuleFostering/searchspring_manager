<?php
/**
 * File Result.php
 *
 * @copyright B7 Interactive, LLC. All Rights Reserved.
 */

/**
 * Class SearchSpring_Manager_Entity_SearchResult_Result
 *
 * The class implements product functions for a SearchSpring Result
 *
 * @author James Bathgate <james@b7interactive.com>
 */
class SearchSpring_Manager_Entity_SearchResult_Result
{

	protected $_result;
	/**
	 * Constructor
	 *
	 * @param stdClass $result
	 */
	public function __construct($result)
	{
		$this->_result = $result;
	}

	public function getProductUrl() {
		return $this->_result->url;
	}

	public function getData($field) {
		$data = $this->_result->$field;
		var_dump($data);
	}
}
