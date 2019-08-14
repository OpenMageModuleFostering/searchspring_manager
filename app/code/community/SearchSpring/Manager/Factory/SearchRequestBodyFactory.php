<?php
/**
 * IndexingRequestBodyFactory.php
 *
 * @copyright B7 Interactive, LLC. All Rights Reserved.
 */

/**
 * Class SearchSpring_Manager_Factory_SearchRequestBodyFactory
 *
 * Create a request body
 *
 * @author James Bathgate <james@b7interactive.com>
 */
class SearchSpring_Manager_Factory_SearchRequestBodyFactory
{
    public function make()
    {
        $requestBody = new SearchSpring_Manager_Entity_SearchRequestBody();
        return $requestBody;
    }
}
