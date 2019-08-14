<?php
/**
 * Data.php
 *
 * @copyright B7 Interactive, LLC. All Rights Reserved.
 */

/**
 * Class SearchSpring_Manager_VarienObject_Data
 *
 * Performs operations on Varien_Objects
 *
 * @author Nate Brunette <nate@b7interactive.com>
 */
class SearchSpring_Manager_VarienObject_Data
{
    /**
     * Finds updated data on object
     *
     * @param Varien_Object $object
     *
     * @return array
     */
    public function findUpdatedData(Varien_Object $object)
	{
		$originalData = $object->getOrigData();
		$data = $object->getData();

		$updated = $this->arrayDiffAssocRecursive($data, $originalData);

		return $updated;
	}

    /**
     * Determines if the object is new
     *
     * @param Varien_Object $object
     *
     * @return bool
     */
    public function isNew(Varien_Object $object)
    {
        $isNew = null === $object->getOrigData();

        return $isNew;
    }

	/**
	 * Finds array_diff_assoc recursively
	 *
	 * Will serialize each array field before performing array_diff_assoc().  This will resolve any errors that occur
	 * when running array_diff_assoc() and values that are not strings.  It will then unserialize each field in the
	 * difference array returned from array_diff_assoc().
	 *
	 * @param array $changed
	 * @param array $original
	 * @return array
	 */
	private function arrayDiffAssocRecursive(array $changed, array $original)
	{
		$changed = $this->arrayValuesToString($changed);
		$original = $this->arrayValuesToString($original);

		$difference = array_map(
			'unserialize',
			array_diff_assoc(
				array_map('serialize', $changed),
				array_map('serialize', $original)
			)
		);

		return $difference;
	}

    /**
     * Casts scalar array values to strings
     *
     * @param array $array
     *
     * @return array
     */
    private function arrayValuesToString(array $array)
	{
		foreach ($array as $key => $value) {
			if (!is_scalar($value)) {
				continue;
			}

			$array[$key] = (string) $value;
		}

		return $array;
	}
}
