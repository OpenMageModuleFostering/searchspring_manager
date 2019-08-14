<?php
/**
 * File RecordsCollection.php
 *
 * @copyright B7 Interactive, LLC. All Rights Reserved.
 */

/**
 * Class SearchSpring_Manager_Entity_RecordsCollection
 *
 * Manages a 2d array of field data.  The Iterator methods will reference the first dimension of the array.  The
 * Collection methods will use the current index and act on the second dimension of the array.
 *
 * @author Nate Brunette <nate@b7interactive.com>
 */
class SearchSpring_Manager_Entity_RecordsCollection
	implements SearchSpring_Manager_Collection, SearchSpring_Manager_Iterator
{
	/**
	 * The array of data
	 *
	 * @var array $fields
	 */
	private $fields = array();

	/**
	 * The current record index
	 *
	 * @var int $index
	 */
	private $index = 0;

	/**
	 * {@inheritdoc}
	 */
	public function current()
	{
		if (!isset($this->fields[$this->key()])) {
			throw new OutOfBoundsException('Index not found');
		}

		return $this->fields[$this->key()];
	}

	/**
	 * {@inheritdoc}
	 */
	public function next()
	{
        ++$this->index;

		return null;
	}

	/**
	 * {@inheritdoc}
	 */
	public function key()
	{
		return $this->index;
	}

	/**
	 * {@inheritdoc}
	 */
	public function valid()
	{
		return isset($this->fields[$this->key()]);
	}

	/**
	 * {@inheritdoc}
	 */
	public function rewind()
	{
		$this->index = 0;

		return null;
	}

	/**
	 * {@inheritdoc}
	 */
	public function end()
	{
		end($this->fields);

		return null;
	}

	/**
	 * {@inheritdoc}
	 */
	public function get($key)
	{
		if (!isset($this->fields[$this->key()][$key])) {
			throw new OutOfBoundsException('Key not found');
		}

		return $this->fields[$this->key()][$key];
	}

	/**
	 * {@inheritdoc}
	 */
	public function set($key, $element)
	{
		$this->fields[$this->key()][$key] = $element;

		return $this;
	}

	/**
	 * {@inheritdoc}
	 */
	public function add($key, $element)
	{
		if(isset($this->fields[$this->key()][$key]) && !is_array($this->fields[$this->key()][$key])) {
			$this->fields[$this->key()][$key] = array($this->fields[$this->key()][$key]);
		}

		$this->fields[$this->key()][$key][] = $element;

		return $this;
	}

	/**
	 * {@inheritdoc}
	 */
	public function remove($key)
	{
		if (!isset($this->fields[$this->key()][$key])) {
			throw new OutOfBoundsException('Key not found');
		}

		unset($this->fields[$this->key()][$key]);
	}

	/**
	 * {@inheritdoc}
	 */
	public function toArray()
	{
		return $this->fields;
	}
}
