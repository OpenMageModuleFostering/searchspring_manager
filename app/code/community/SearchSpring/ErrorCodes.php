<?php
/**
 * ErrorCodes.php
 *
 * @copyright B7 Interactive, LLC. All Rights Reserved.
 */

/**
 * Class SearchSpring_ErrorCodes
 *
 * Hold a list of all the error codes that can be returned from the API
 *
 * @author Nate Brunette <nate@b7interactive.com>
 */
class SearchSpring_ErrorCodes
{
	/**
	 * If we can not resolve the product type for a product
	 */
	const PRODUCT_TYPE_NOT_FOUND = 1;

	/**
	 * Could not write to the directory
	 */
	const DIR_NOT_WRITABLE = 2;

	/**
	 * Could not create feed for unkown reason
	 */
	const FEED_WRITE_ERROR = 3;

	/**
	 * Filename is not passed into feed generation
	 */
	const FILENAME_NOT_SET = 4;

    /**
     * Type parameter not found
     */
    const TYPE_NOT_SET = 5;

    /**
     * Ids parameter not found
     */
    const IDS_NOT_SET = 6;

	/**
	 * BAD REQUEST FORMAT
	 */
	const BAD_REQUEST = 7;

	/**
	 * Unknown Error
	 */
	const UNKNOWN_EXCEPTION = 8;

	/**
	 * Authentication Credentials - Invalid
	 */
	const AUTH_CREDENTIALS_INVALID = 9;

	/**
	 * Authentication Credentials - Missing
	 */
	const AUTH_CREDENTIALS_MISSING = 10;

}
