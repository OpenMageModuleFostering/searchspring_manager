<?php
/**
 * Sanitizer.php
 *
 * @copyright B7 Interactive, LLC. All Rights Reserved.
 */

/**
 * Class SearchSpring_Manager_String_Sanitizer
 *
 * Service to sanitize string data
 *
 * @author Nate Brunette <nate@b7interactive.com>
 */
class SearchSpring_Manager_String_Sanitizer
{
	/**
	 * Helper method to remove unwanted characters
	 *
	 * @param string $value
	 * @return string|null
	 */
	public function sanitizeForRequest($value)
	{
		$value = preg_replace('/[^(\x20-\x7F)\x0A]*/','', $value);

		return $value;
	}

    /**
     * Strip newline and tab characters
     *
     * @param string $value
     *
     * @return string|null
     */
    public function removeNewlinesAndStripTags($value)
    {
        $value = strip_tags($value);
        $value = str_replace("\n", "", $value);
        $value = str_replace("\r", "", $value);
        $value = str_replace("\t", "", $value);

        return $value;
    }
}
