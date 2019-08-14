<?php
/**
 * ProductCollectionToRecordCollectionTransformer.php
 *
 * @copyright B7 Interactive, LLC. All Rights Reserved.
 */

/**
 * Class SearchSpring_Manager_Transformer_ProductCollectionToRecordCollectionTransformer
 *
 * Transform a Magento product collection to our records collection.  Only performs a 1-way transform
 *
 * @author Nate Brunette <nate@b7interactive.com>
 */
class SearchSpring_Manager_Transformer_ProductCollectionToRecordCollectionTransformer
{
    /**
     * @var SearchSpring_Manager_Entity_RecordsCollection
     */
    private $recordsCollection;

    /**
     * @var SearchSpring_Manager_Entity_OperationsCollection
     */
    private $operationsCollection;

    /**
     * Constructor
     *
     * @param SearchSpring_Manager_Entity_RecordsCollection $recordsCollection
     * @param SearchSpring_Manager_Entity_OperationsCollection $operationsCollection
     */
    public function __construct(
        SearchSpring_Manager_Entity_RecordsCollection $recordsCollection,
        SearchSpring_Manager_Entity_OperationsCollection $operationsCollection
    ) {
        $this->recordsCollection = $recordsCollection;
        $this->operationsCollection = $operationsCollection;
    }

    /**
     * Transforms product collection to records collection
     *
     * @param Mage_Catalog_Model_Resource_Product_Collection $productCollection
     *
     * @return SearchSpring_Manager_Entity_RecordsCollection
     */
    public function transform($productCollection)
    {
		Varien_Profiler::start(__METHOD__);

		// Prep our collection
		$this->prepareCollection($productCollection);

		// Finally load the collection
		$this->loadCollection($productCollection);

		// Prep our operations with the loaded collection
		$this->prepareOperations($productCollection);

        /** @var Mage_Catalog_Model_Product $product */
        foreach ($productCollection as $product) {

            // load product data
			Varien_Profiler::start(__METHOD__.": product->load()");
            $product->load($product->getId());
			Varien_Profiler::stop(__METHOD__.": product->load()");

            // default to valid
            $productValid = true;

            /** @var SearchSpring_Manager_Operation_Product $operation */
            foreach ($this->operationsCollection as $operation) {
                // check if any operation validation invalidates this product
				Varien_Profiler::start(__METHOD__.": operation->isValid()");
				Varien_Profiler::start(__METHOD__.": operation->isValid() : " . get_class($operation));
                if (false === $operation->isValid($product)) {
                    $productValid = false;
                }
				Varien_Profiler::stop(__METHOD__.": operation->isValid() : " . get_class($operation));
				Varien_Profiler::stop(__METHOD__.": operation->isValid()");
            }

            // only set id if product is invalid and continue to next product
            if (false === $productValid) {
				Varien_Profiler::start(__METHOD__.": invalid product, operation set id");
                $operation = new SearchSpring_Manager_Operation_Product_SetId(
                    new SearchSpring_Manager_String_Sanitizer(),
                    $this->recordsCollection
                );

                $operation->perform($product);

                // increment record
                $this->recordsCollection->next();

				Varien_Profiler::stop(__METHOD__.": invalid product, operation set id");

                continue;
            }

            foreach ($this->operationsCollection as $operation) {
				Varien_Profiler::start(__METHOD__.": operation->perform()");
				Varien_Profiler::start(__METHOD__.": operation->perform() : " . get_class($operation));
                $operation->perform($product);
				Varien_Profiler::stop(__METHOD__.": operation->perform()");
				Varien_Profiler::stop(__METHOD__.": operation->perform() : " . get_class($operation));
            }

            // increment record
            $this->recordsCollection->next();
        }

		Varien_Profiler::stop(__METHOD__);

        return $this->recordsCollection;
	}

	/**
	 * Before the operations validate or perform on each product, we want to
	 * make sure the product collection is prepared. This allows for things
	 * like adding joins/filters/selects on the collection.
	 *
	 * @param Mage_Catalog_Model_Resource_Product_Collection $productCollection
	 */
	protected function prepareCollection($productCollection) {

		foreach ($this->operationsCollection as $operation) {
			$operation->prepareCollection($productCollection);
		}

	}

	/**
	 * Before the operations validate or perform on each product, but after the
	 * product collection has been loaded, prepare each operation. This allows
	 * for pulling in extra data in mass, rather than calling the database for
	 * very single product.
	 *
	 * @param Mage_Catalog_Model_Resource_Product_Collection $productCollection
	 */
	protected function prepareOperations($productCollection) {

		foreach ($this->operationsCollection as $operation) {
			Varien_Profiler::start(__METHOD__.": operation->prepare() " . get_class($operation));
			$operation->prepare($productCollection);
			Varien_Profiler::stop(__METHOD__.": operation->prepare() " . get_class($operation));
		}

	}

	protected function loadCollection($productCollection) {

		Varien_Profiler::start(__METHOD__.": productCollection->load()");
		$productCollection->load();
		Varien_Profiler::stop(__METHOD__.": productCollection->load()");

	}

}
