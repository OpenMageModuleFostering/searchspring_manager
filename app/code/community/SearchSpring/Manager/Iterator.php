<?php
/**
 * File Iterator.php
 *
 * @copyright B7 Interactive, LLC. All Rights Reserved.
 */

/**
 * Interface SearchSpring_Manager_Iterator
 *
 * @author Nate Brunette <nate@b7interactive.com>
 */
interface SearchSpring_Manager_Iterator extends Iterator
{
	/**
	 * Move the index to the last element
	 *
	 * @return null
	 */
	public function end();
}
