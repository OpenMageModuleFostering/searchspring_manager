<?php
/**
 * IndexingRequestBodyFactory.php
 *
 * @copyright B7 Interactive, LLC. All Rights Reserved.
 */

/**
 * Class SearchSpring_Manager_Factory_IndexingRequestBodyFactory
 *
 * Create a request body
 *
 * @author Nate Brunette <nate@b7interactive.com>
 */
class SearchSpring_Manager_Factory_IndexingRequestBodyFactory
{
    /**
     * Make a request body
     *
     * @param $type
     * @param array $ids
     * @param bool $shouldDelete
     *
     * @throws InvalidArgumentException
     * @return SearchSpring_Manager_Entity_IndexingRequestBody
     *
     */
    public function make($type, array $ids, $shouldDelete = false)
    {
        if (!in_array($type, SearchSpring_Manager_Entity_IndexingRequestBody::$allowableTypes)) {
            throw new InvalidArgumentException('Type must be one of allowable types');
        }

        $requestBody = new SearchSpring_Manager_Entity_IndexingRequestBody($type, $ids, $shouldDelete);

        return $requestBody;
    }
}
