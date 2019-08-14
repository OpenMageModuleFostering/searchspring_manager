<?php
/**
 * File IndexingRequestBody.php
 *
 * @copyright B7 Interactive, LLC. All Rights Reserved.
 */

/**
 * Class SearchSpring_Manager_Entity_IndexingRequestBody
 *
 * The class models a SearchSpring API request body which needs a feed id and an array of records.
 *
 * @author Nate Brunette <nate@b7interactive.com>
 */
class SearchSpring_Manager_Entity_IndexingRequestBody extends SearchSpring_Manager_Entity_RequestBody
{
    /**#@+
     * Request types
     */
    const TYPE_PRODUCT = 'product';
    const TYPE_CATEGORY = 'category';
    /**#@-*/

    /**
     * An array of allowable types
     *
     * @var array $allowableTypes
     */
    public static $allowableTypes = array(
        self::TYPE_PRODUCT,
        self::TYPE_CATEGORY,
    );

    /**
     * The type of request
     *
     * @var string $type
     */
    private $type;

	/**
     * An array of category/product ids
     *
     * @var array $ids
	 */
	private $ids;

    /**
     * If we should delete these ids
     *
     * @var bool $shouldDelete
     */
    private $shouldDelete;

    /**
     * Constructor
     *
     * @param string $type
     * @param array $ids
     * @param $shouldDelete
     */
    public function __construct($type, array $ids, $shouldDelete)
    {
        $this->type = $type;
        $this->ids = $ids;
        $this->shouldDelete = $shouldDelete;

        $this->feedId = Mage::helper('searchspring_manager')->getApiFeedId();
        if (null === $this->feedId) {
            throw new UnexpectedValueException('SearchSpring: Feed ID must be set');
        }
    }

	/**
	 * Returns a value that's allowed to be given to json_encode
	 *
	 * @return array
	 */
	public function jsonSerialize()
	{
		$body = array(
			'type' => $this->type,
			'ids' => $this->ids,
			'delete' => $this->shouldDelete,
			'feedId' => $this->feedId,
			'generateUrl' => Mage::getUrl('searchspring/generate/index',array('_secure'=>true))
		);

		return $body;
	}
}
