<?php
/**
 * RequestParams.php
 *
 * @copyright B7 Interactive, LLC. All Rights Reserved.
 */

/**
 * Class SearchSpring_Manager_Entity_RequestParams
 *
 * Store request parameters
 *
 * @author Nate Brunette <nate@b7interactive.com>
 */
class SearchSpring_Manager_Entity_RequestParams
{
    /**
     * How many records to limit
     *
     * @var int $count
     */
    private $count;

    /**
     * Limit offest
     *
     * @var int $offset
     */
    private $offset;

    /**
     * The store id
     *
     * @var string $store
     */
    private $store;

    /**
     * Constructor
     *
     * @param int $count
     * @param int $offset
     * @param string $store
     */
    public function __construct($count, $offset, $store)
    {
        $this->count = $count;
        $this->offset = $offset;
        $this->store = $store;
    }

    /**
     * Get the offset
     *
     * @return int
     */
    public function getOffset()
    {
        return $this->offset;
    }

    /**
     * Get the store id
     *
     * @return string
     */
    public function getStore()
    {
        return $this->store;
    }

    /**
     * Get the limit count
     *
     * @return int
     */
    public function getCount()
    {
        return $this->count;
    }
}
