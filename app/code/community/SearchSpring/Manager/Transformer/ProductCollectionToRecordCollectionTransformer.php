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
    public function transform(Mage_Catalog_Model_Resource_Product_Collection $productCollection)
    {
        /** @var Mage_Catalog_Model_Product $product */
        foreach ($productCollection as $product) {
            // load product data
            $product->load($product->getId());

            // default to valid
            $productValid = true;

            /** @var SearchSpring_Manager_Operation_Product $operation */
            foreach ($this->operationsCollection as $operation) {
                // check if any operation validation invalidates this product
                if (false === $operation->isValid($product)) {
                    $productValid = false;
                }
            }

            // only set id if product is invalid and continue to next product
            if (false === $productValid) {
                $operation = new SearchSpring_Manager_Operation_Product_SetId(
                    new SearchSpring_Manager_String_Sanitizer(),
                    $this->recordsCollection
                );

                $operation->perform($product);

                // increment record
                $this->recordsCollection->next();

                continue;
            }

            foreach ($this->operationsCollection as $operation) {
                $operation->perform($product);
            }

            // increment record
            $this->recordsCollection->next();
        }

        return $this->recordsCollection;
	}
}
