<?php
/**
 * Product.php
 *
 * @copyright B7 Interactive, LLC. All Rights Reserved.
 */

/**
 * Class SearchSpring_Manager_Operation_Product
 *
 * Abstract class for all product operations
 *
 * @author Nate Brunette <nate@b7interactive.com>
 */
abstract class SearchSpring_Manager_Operation_Product implements SearchSpring_Manager_Operation_ProductOperation
{
    /**
     * SearchSpring operation class prefix
     */
    const OPERATION_CLASS_PREFIX = 'SearchSpring_Manager_Operation_Product_';

    /**
     * Service to sanitize string data
     *
     * @var SearchSpring_Manager_String_Sanitizer
     */
    private $sanitizer;

    /**
     * Records collection
     *
     * @var SearchSpring_Manager_Entity_RecordsCollection $records
     */
    private $records;

    /**
     * An array of additional data
     *
     * @var array $parameters
     */
    private $parameters;

    /**
     * An array of reserved field names for the current operation that can't be
     * used for attribute fields.
     */
    protected $_localReservedFields = array();

    /**
     * An array of reserved field names across all operations that can't be used
     * for attribute fields.
     */
    protected $_globalReservedFields = array();

    /**
     * Constructor
     *
     * @param SearchSpring_Manager_String_Sanitizer $sanitizer
     * @param SearchSpring_Manager_Entity_RecordsCollection $records
     * @param array $parameters
     */
    public function __construct(
        SearchSpring_Manager_String_Sanitizer $sanitizer,
        SearchSpring_Manager_Entity_RecordsCollection $records,
        array $parameters = array(),
        SearchSpring_Manager_Model_Config $config = NULL
    ) {
        if(is_null($config)) {
            $config = Mage::helper('searchspring_manager')->getConfig();
        }

        $this->sanitizer = $sanitizer;
        $this->records = $records;
        $this->config = $config;
        $this->parameters = $parameters;
    }

    /**
     * Overridable method to determine if an operation is valid for the product
     *
     * Will allow operations to cancel adding a product record.  Defaults to true.
     *
     * @param Mage_Catalog_Model_Product $product
     *
     * @return bool
     */
    public function isValid(Mage_Catalog_Model_Product $product)
    {
        return true;
    }

    /**
     * Get records collection
     *
     * @return SearchSpring_Manager_Entity_RecordsCollection
     */
    protected final function getRecords()
    {
        return $this->records;
    }

    /**
     * {@inheritdoc}
     */
    public final function setRecords(SearchSpring_Manager_Entity_RecordsCollection $records)
    {
        $this->records = $records;

        return $this;
    }

    /**
     * Get the sanitizer
     *
     * @return SearchSpring_Manager_String_Sanitizer
     */
    protected final function getSanitizer()
    {
        return $this->sanitizer;
    }

    /**
     * Get a parameter from array
     *
     * @param string $key
     *
     * @return mixed
     *
     * @throws InvalidArgumentException If the key does not exist
     */
    protected final function getParameter($key)
    {
        if (!isset($this->parameters[$key])) {
            throw new InvalidArgumentException('Key does not exist');
        }

        return $this->parameters[$key];
    }

    /**
     * Gets the config object
     *
     * @return SearchSpring_Manager_Model_Config
     */
    protected final function getConfig() {
        return $this->config;
    }

    /**
     * {@inheritdoc}
     */
	public function prepareCollection($productCollection) {
		return $this;
	}

    /**
     * {@inheritdoc}
     */
	public function prepare($productCollection) {
		return $this;
	}

    /**
     * Returns a list of reserved field names that can't be used by attributes
     *
     * @return array
     */
    public function getLocalReservedFields() {
        return $this->_localReservedFields;
    }

    public function setGlobalReservedFields($reservedFields) {
        $this->_globalReservedFields = $reservedFields;
    }
}
