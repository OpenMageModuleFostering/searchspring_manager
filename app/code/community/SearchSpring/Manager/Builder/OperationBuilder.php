<?php
/**
 * OperationBuilder.php
 *
 * @copyright B7 Interactive, LLC. All Rights Reserved.
 */

/**
 * Class SearchSpring_Manager_Builder_OperationBuilder
 *
 * Builder for product operations
 *
 * @author Nate Brunette <nate@b7interactive.com>
 */
class SearchSpring_Manager_Builder_OperationBuilder
{
    /**
     * Service to sanitize string data
     *
     * @var SearchSpring_Manager_String_Sanitizer $sanitizer
     */
    private $sanitizer;

    /**
     * Api records collection
     *
     * @var SearchSpring_Manager_Entity_RecordsCollection $records
     */
    private $records;

    /**
     * The operation class name
     *
     * @var string $className
     */
    private $className;

    /**
     * Magento product
     *
     * @var Mage_Catalog_Model_Product $product
     */
    private $product;

    /**
     * The class prefix
     *
     * Use this to prepend to the class name when the object is built
     *
     * @var string $classPrefix
     */
    private $classPrefix = '';

    /**
     * Operation parameters
     *
     * Used for adding additional data to the operation
     *
     * @var array $parameters
     */
    private $parameters;

    /**
     * Constructor
     *
     * @param SearchSpring_Manager_String_Sanitizer $sanitizer
     * @param SearchSpring_Manager_Entity_RecordsCollection $records
     * @param null $className
     * @param Mage_Catalog_Model_Product $product
     * @param string $classPrefix
     */
    public function __construct(
        SearchSpring_Manager_String_Sanitizer $sanitizer = null,
        SearchSpring_Manager_Entity_RecordsCollection $records = null,
        $className = null,
        Mage_Catalog_Model_Product $product = null,
        $classPrefix = ''
    ) {
        $this->sanitizer = $sanitizer;
        $this->records = $records;
        $this->className = $className;
        $this->product = $product;
        $this->classPrefix = $classPrefix;
    }

    /**
     * Set the string sanitizer
     *
     * @param SearchSpring_Manager_String_Sanitizer $sanitizer
     *
     * @return $this
     */
    public function setSanitizer(SearchSpring_Manager_String_Sanitizer $sanitizer)
    {
        $this->sanitizer = $sanitizer;

        return $this;
    }

    /**
     * Set the records collection
     *
     * @param SearchSpring_Manager_Entity_RecordsCollection $records
     *
     * @return $this
     */
    public function setRecords(SearchSpring_Manager_Entity_RecordsCollection $records)
    {
        $this->records = $records;

        return $this;
    }

    /**
     * Set the class prefix
     *
     * @param $classPrefix
     *
     * @return $this
     */
    public function setClassPrefix($classPrefix)
    {
        $this->classPrefix = (string)$classPrefix;

        return $this;
    }

    /**
     * Set the class name
     *
     * @param string $className
     *
     * @return $this
     */
    public function setClassName($className)
    {
        $this->className = $className;

        return $this;
    }

    /**
     * Set additional data to operation
     *
     * @param array $parameters
     *
     * @return $this
     */
    public function setParameters(array $parameters)
    {
        $this->parameters = $parameters;

        return $this;
    }

    /**
     * Make a new operation instance
     *
     * @param null $className
     * @param array $parameters
     * @param bool $useClassPrefix
     *
     * @throws InvalidArgumentException If className is not a valid type
     * @throws UnexpectedValueException If a member variable is not set
     *
     * @return SearchSpring_Manager_Operation_Product
     */
    public function build($className = null, array $parameters = array(), $useClassPrefix = true)
    {
        if ($className !== null) {
            $this->className = $className;
        }

        if (null === $this->sanitizer) {
            throw new UnexpectedValueException('Sanitizer must not be null');
        }

        if (null === $this->records) {
            throw new UnexpectedValueException('Records collection must not be null');
        }

        if (null === $this->className) {
            throw new UnexpectedValueException('Class name must not be null');
        }

        $hlp = Mage::helper('searchspring_manager');

        $class = ($useClassPrefix) ? $this->classPrefix . $this->className : $this->className;
        $operation = new $class($this->sanitizer, $this->records, $parameters, $hlp->getConfig());

        $class = 'SearchSpring_Manager_Operation_Product';
        if (!$operation instanceof $class) {
            throw new InvalidArgumentException('$class must be an instance of SearchSpring_Manager_Operation_Product');
        }

        return $operation;
    }
}
