<?php
/**
 * File Collection.php
 *
 * @copyright B7 Interactive, LLC. All Rights Reserved.
 */

/**
 * Interface SearchSpring_Manager_Collection
 *
 * @author Nate Brunette <nate@b7interactive.com>
 */
interface SearchSpring_Manager_Collection
{
	/**
	 * Get value of key from the current index
	 *
	 * @param string $key
	 *
	 * @return mixed
	 */
	public function get($key);

	/**
	 * Set data to key at current index
	 *
	 * @param string $key
	 * @param mixed $element
	 *
	 * @return mixed
	 */
	public function set($key, $element);

	/**
	 * Add data to key at current index
	 *
	 * Will not replace existing data at key
	 *
	 * @param string $key
	 * @param $element
	 *
	 * @return mixed
	 */
	public function add($key, $element);

	/**
	 * Unset key from current index
	 *
	 * @param string $key
	 * @return mixed
	 */
	public function remove($key);

	/**
	 * Return an array of data
	 *
	 * @return array
	 */
	public function toArray();
}
