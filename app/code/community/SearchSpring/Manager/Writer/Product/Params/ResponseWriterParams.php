<?php
/**
 * WriterParams.php
 *
 * @copyright B7 Interactive, LLC. All Rights Reserved.
 */

/**
 * Class SearchSpring_Manager_Writer_Provider_WriterParams
 *
 * Store parameters for response writer
 *
 * @author Nate Brunette <nate@b7interactive.com>
 */
class SearchSpring_Manager_Writer_Product_Params_ResponseWriterParams
{

	/**
	 * Total number of products in Magento
	 *
	 * @var int $totalProducts
	 */
	private $totalProducts;

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
     * @var SearchSpring_Manager_Entity_RequestParams
     */
    private $requestParams;

    /**
     * Constructor
     *
     * @param SearchSpring_Manager_Entity_RequestParams $requestParams
     * @param int $totalProducts
     */
	public function __construct(SearchSpring_Manager_Entity_RequestParams $requestParams, $totalProducts)
	{
        $this->requestParams = $requestParams;
		$this->totalProducts = $totalProducts;
    }

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
        $totalCount = $this->requestParams->getCount() + $this->requestParams->getOffset();

        // if we defaulted to 0, we're only doing one iteration
        if (0 === $totalCount) {
            return true;
        }

		if (null === $this->isLast) {
			$this->isLast = ($this->totalProducts <= $totalCount);
		}

		return $this->isLast;
	}
}
