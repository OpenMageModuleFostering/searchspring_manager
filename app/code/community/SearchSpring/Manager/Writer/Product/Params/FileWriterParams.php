<?php
/**
 * WriterParams.php
 *
 * @copyright B7 Interactive, LLC. All Rights Reserved.
 */

/**
 * Class SearchSpring_Manager_Writer_Product_Params_FileWriterParams
 *
 * A parameter object that holds values needed for writing files
 *
 * @author Nate Brunette <nate@b7interactive.com>
 */
class SearchSpring_Manager_Writer_Product_Params_FileWriterParams
{
	/**
	 * Filename pattern
	 */
	const FILENAME_FINAL_PATTERN = 'searchspring_%s.xml';

	/**
	 * Temporary filename pattern
	 */
	const FILENAME_TEMP_PATTERN = 'searchspring_%s.%s.tmp.xml';

	/**
	 * Total number of products in Magento
	 *
	 * @var int $totalProducts
	 */
	private $totalProducts;

	/**
	 * The unique filename to avoid collisions during generation
	 *
	 * @var string $uniqueFilename
	 */
	private $uniqueFilename;

	/**
	 * Whether or not we're writing for the first time
	 *
	 * @var bool $isFirst
	 */
	private $isFirst;

	/**
	 * Whether or not we're writing the last set of records
	 *
	 * @var bool $isLast
	 */
	private $isLast;

	/**
	 * The temporary filename
	 *
	 * @var string $tempFilename
	 */
	private $tempFilename;

	/**
	 * The filename
	 *
	 * @var string $filename
	 */
	private $filename;

    /**
     * @var SearchSpring_Manager_Entity_RequestParams
     */
    private $requestParams;

    /**
     * Constructor
     *
     * @param SearchSpring_Manager_Entity_RequestParams $requestParams
     * @param int $totalProducts
     * @param string $uniqueFilename
     */
	public function __construct(SearchSpring_Manager_Entity_RequestParams $requestParams, $totalProducts, $uniqueFilename)
	{
        $this->requestParams = $requestParams;
		$this->totalProducts = $totalProducts;
		$this->uniqueFilename = $uniqueFilename;
    }

    /**
     * Get the request parameters object
     *
     * @return SearchSpring_Manager_Entity_RequestParams
     */
    public function getRequestParams()
    {
        return $this->requestParams;
    }

	/**
	 * Calculates if this is the first write
	 *
	 * @return bool
	 */
	public function isFirst()
	{
		if (null === $this->isFirst) {
			$this->isFirst = (0 === $this->requestParams->getOffset());
		}

		return $this->isFirst;
	}

	/**
	 * Calculates if this is the last write
	 *
	 * @return bool
	 */
	public function isLast()
	{
		if (null === $this->isLast) {
			$this->isLast = ($this->totalProducts <= ($this->requestParams->getCount() + $this->requestParams->getOffset()));
		}

		return $this->isLast;
	}

	/**
	 * Get temporary filename
	 *
	 * @return string
	 */
	public function getTempFilename()
	{
		if (null === $this->tempFilename) {
			$this->tempFilename = Mage::getBaseDir()
				. DS
				. sprintf(self::FILENAME_TEMP_PATTERN, $this->requestParams->getStore(), $this->uniqueFilename);
		}

		return $this->tempFilename;
	}

	/**
	 * Get filename
	 *
	 * @return string
	 */
	public function getFilename()
	{
		if (null === $this->filename) {
			$this->filename = Mage::getBaseDir() . DS . sprintf(self::FILENAME_FINAL_PATTERN, $this->requestParams->getStore());
		}

		return $this->filename;
	}
}
