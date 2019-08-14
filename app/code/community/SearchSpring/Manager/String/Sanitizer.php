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
		// Remove all control characters, except new lines
		// Code points 0 - 31, except 10
		// Code points x00 - x1F, except x0A
		$value = preg_replace('/[\x00-\x09\x0B-\x1F]*/','', $value);

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
