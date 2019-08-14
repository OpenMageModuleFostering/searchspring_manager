<?php
/**
 * ResponseWriter.php
 *
 * @copyright B7 Interactive, LLC. All Rights Reserved.
 */

/**
 * Class SearchSpring_Manager_Writer_Product_ResponseWriter
 *
 * Write a collection as a response
 *
 * @author Nate Brunette <nate@b7interactive.com>
 */
class SearchSpring_Manager_Writer_Product_ResponseWriter implements SearchSpring_Manager_Writer_ProductWriter
{
    /**
     * Parameters for writing responses
     *
     * @var SearchSpring_Manager_Writer_Product_Params_ResponseWriterParams
     */
    private $params;

    /**
     * Constructor
     *
     * @param SearchSpring_Manager_Writer_Product_Params_ResponseWriterParams $params
     */
    public function __construct(SearchSpring_Manager_Writer_Product_Params_ResponseWriterParams $params)
    {
        $this->params = $params;
    }

    /**
     * Write a response and return that as the message
     *
     * @param SearchSpring_Manager_Entity_RecordsCollection $recordsCollection
     * @return array
     */
    public function write(SearchSpring_Manager_Entity_RecordsCollection $recordsCollection)
    {
        $response = array('status' => self::REGEN_COMPLETE, 'products' => $recordsCollection->toArray());
        if (!$this->params->isLast()) {
            $response['status'] = self::REGEN_CONTINUE;
        }

        return $response;
    }
}
