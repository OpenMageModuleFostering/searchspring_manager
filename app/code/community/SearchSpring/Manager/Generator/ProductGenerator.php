<?php
/**
 * ProductGenerator.php
 *
 * @copyright B7 Interactive, LLC. All Rights Reserved.
 */

/**
 * Class SearchSpring_Manager_Generator_ProductGenerator
 *
 * Handles generation of product records
 *
 * @author Nate Brunette <nate@b7interactive.com>
 */
class SearchSpring_Manager_Generator_ProductGenerator
{
    /**
     * Object that handles writing a record collection
     *
     * @var SearchSpring_Manager_Writer_ProductWriter $writer
     */
    private $writer;

    /**
     * Transforms product collection to record collection
     *
     * @var SearchSpring_Manager_Transformer_ProductCollectionToRecordCollectionTransformer
     */
    private $transformer;

    /**
     * Provides a collection
     *
     * @var SearchSpring_Manager_Provider_ProductCollectionProvider
     */
    private $collectionProvider;

    /**
     * Constructor
     *
     * @param SearchSpring_Manager_Provider_ProductCollectionProvider $collectionProvider
     * @param SearchSpring_Manager_Writer_ProductWriter $writer
     * @param SearchSpring_Manager_Transformer_ProductCollectionToRecordCollectionTransformer $transformer
     */
    public function __construct(
        SearchSpring_Manager_Provider_ProductCollectionProvider $collectionProvider,
        SearchSpring_Manager_Writer_ProductWriter $writer,
        SearchSpring_Manager_Transformer_ProductCollectionToRecordCollectionTransformer $transformer
    ) {
        $this->collectionProvider = $collectionProvider;
        $this->writer = $writer;
        $this->transformer = $transformer;
    }

    /**
     * Gets a product collection, transform to record collection, and then write the records collection
     *
     * @return string The response message
     */
    public function generate()
    {
		Varien_Profiler::start(__METHOD__.": getCollection()");
        $productCollection = $this->collectionProvider->getCollection();
		Varien_Profiler::stop(__METHOD__.": getCollection()");

		Varien_Profiler::start(__METHOD__.": transform()");
        $recordCollection = $this->transformer->transform($productCollection);
		Varien_Profiler::stop(__METHOD__.": transform()");

		Varien_Profiler::start(__METHOD__.": write()");
        $resultMessage = $this->writer->write($recordCollection);
		Varien_Profiler::stop(__METHOD__.": write()");

        return $resultMessage;
    }
}
