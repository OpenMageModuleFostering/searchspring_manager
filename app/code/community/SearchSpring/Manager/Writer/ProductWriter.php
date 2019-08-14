<?php
/**
 * ProductWriter.php
 *
 * @copyright B7 Interactive, LLC. All Rights Reserved.
 */

/**
 * Interface SearchSpring_Manager_Writer_ProductWriter
 *
 * @author Nate Brunette <nate@b7interactive.com>
 */
interface SearchSpring_Manager_Writer_ProductWriter
{
    /**
     * Continue response
     */
    const REGEN_CONTINUE = 'continue';

    /**
     * Complete response
     */
    const REGEN_COMPLETE = 'complete';

    /**
     * Write a collection
     *
     * @param SearchSpring_Manager_Entity_RecordsCollection $recordsCollection
     *
     * @return string The response message
     */
    public function write(SearchSpring_Manager_Entity_RecordsCollection $recordsCollection);
}
