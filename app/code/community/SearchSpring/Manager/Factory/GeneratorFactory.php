<?php
/**
 * GeneratorFactory.php
 *
 * @copyright B7 Interactive, LLC. All Rights Reserved.
 */

/**
 * Class SearchSpring_Manager_Factory_GeneratorFactory
 *
 * Creates a generator object
 *
 * @author Nate Brunette <nate@b7interactive.com>
 */
class SearchSpring_Manager_Factory_GeneratorFactory
{
	/**#@+
	 * Generator types
	 */
	const TYPE_FEED = 'feed';
	const TYPE_PRODUCT = 'product';
	const TYPE_CATEGORY = 'category';
	/**#@-*/

	const XML_PATH_OPERATION_THIRD_PARTY = 'global/searchspring/operation/third_party';

	/**
	 * Create a generator
	 *
	 * @param string $type Generator type
	 * @param SearchSpring_Manager_Entity_RequestParams $requestParams Request parameters
	 * @param array $params Additional parameters
	 *
	 * @throws OutOfBoundsException If a key is not found
	 * @throws InvalidArgumentException If the type is not found
	 *
	 * @return SearchSpring_Manager_Generator_ProductGenerator
	 *
	 */
	public function make($type, SearchSpring_Manager_Entity_RequestParams $requestParams, array $params = array())
	{
		$operationsBuilder = new SearchSpring_Manager_Builder_OperationBuilder();
		$operationsCollection = new SearchSpring_Manager_Entity_OperationsCollection();
		$productRecords = new SearchSpring_Manager_Entity_RecordsCollection();

		// setup default operations
		$this->createOperations($operationsBuilder, $operationsCollection, $productRecords);

		$transformer = new SearchSpring_Manager_Transformer_ProductCollectionToRecordCollectionTransformer(
			$productRecords,
			$operationsCollection
		);

		switch($type) {
			case self::TYPE_FEED:
				if (!isset($params['filename'])) {
					throw new OutOfBoundsException('Key "filename" not found in array');
				}

				$filename = $params['filename'];

				$generator = $this->makeFeedGenerator($transformer, $requestParams, $filename);

				break;
			case self::TYPE_PRODUCT:
				if (!isset($params['ids'])) {
					throw new OutOfBoundsException('Key "ids" not found in array');
				}

				$ids = $params['ids'];

				$generator = $this->makeProductGenerator($transformer, $ids, $requestParams);

				break;
			case self::TYPE_CATEGORY:
				if (!isset($params['ids'])) {
					throw new OutOfBoundsException('Key "ids" not found in array');
				}

				$ids = $params['ids'];

				if (!is_array($ids)) {
					throw new InvalidArgumentException('Ids must be an array');
				}

				$hlp = Mage::helper('searchspring_manager');
				$validator = new SearchSpring_Manager_Validator_ProductValidator($hlp->getConfig());

				// get product ids for each category
				$productIds = array();
				foreach ($ids as $id) {
					/** @var Mage_Catalog_Model_Category $category */
					$category = Mage::getModel('catalog/category')->load($id);

					// Check for non-existent category
					if(!is_null($category->getId())) {
						/** @var Mage_Catalog_Model_Resource_Product_Collection $products */
						$products = Mage::getModel('catalog/product')
							->getCollection()
							->joinField('category_id', 'catalog/category_product', 'category_id', 'product_id = entity_id', null, 'left')
							->addAttributeToSelect('*')
							->addAttributeToFilter('category_id', array('in' => $id));
						foreach($products as $product) {
							if($validator->isValid($product)) {
								$productIds[] = $product->getId();
							}
						}
					}
				}

				// remove duplicates
				$productIds = array_unique($productIds);

				// make a product generator based on product ids
				$generator = $this->makeProductGenerator($transformer, $productIds, $requestParams);

				break;
			default:
				throw new InvalidArgumentException('Type not found.');
		}

		return $generator;
	}

	/**
	 * Create default operations
	 *
	 * @param SearchSpring_Manager_Builder_OperationBuilder $operationsBuilder
	 * @param SearchSpring_Manager_Entity_OperationsCollection $operationsCollection
	 * @param SearchSpring_Manager_Entity_RecordsCollection $productRecords
	 */
	private function createOperations(
		SearchSpring_Manager_Builder_OperationBuilder $operationsBuilder,
		SearchSpring_Manager_Entity_OperationsCollection $operationsCollection,
		SearchSpring_Manager_Entity_RecordsCollection $productRecords
	) {
		$hlp = Mage::helper('searchspring_manager');

		$operationsBuilder->setSanitizer(new SearchSpring_Manager_String_Sanitizer())
			->setRecords($productRecords)
			->setClassPrefix(SearchSpring_Manager_Operation_Product::OPERATION_CLASS_PREFIX);

		$operationsCollection->append($operationsBuilder->build('SetFields'));
		$operationsCollection->append($operationsBuilder->build('SetCoreFields'));
		$operationsCollection->append($operationsBuilder->build('SetImages'));
		$operationsCollection->append($operationsBuilder->build('SetOptions'));
		$operationsCollection->append($operationsBuilder->build('SetCategories'));

		// Parameter for timespan comes from user configuration, by default
		$operationsCollection->append($operationsBuilder->build('SetReport',
				array(
					'timespan' => $hlp->getSalesRankTimespan(),
				)
			)
		);

		// add pricing factory and if we should display zero priced products as additional data
		$operationsCollection->append($operationsBuilder->build('SetPricing',
				array(
					'pricingFactory' => new SearchSpring_Manager_Factory_PricingFactory(),
					'displayZeroPrice' => (int)$hlp->isZeroPriceIndexingEnabled(),
				)
			)
		);

		$operationsCollection->append($operationsBuilder->build('SetRatings'));

		$this->addThirdPartyOperations($operationsCollection, $operationsBuilder);

		// dispatch event allowing additional operations to be added before loop starts
		$operationsBuilder->setClassPrefix(null);
		Mage::dispatchEvent('searchspring_add_operations', array(
				'operations' => $operationsCollection,
				'builder' => $operationsBuilder,
			)
		);

		$this->buildReservedFields($operationsCollection);
	}

	/**
	 * Make a feed generator
	 *
	 * Uses a feed collection provider and file writer
	 *
	 * @param SearchSpring_Manager_Transformer_ProductCollectionToRecordCollectionTransformer $transformer
	 * @param SearchSpring_Manager_Entity_RequestParams $requestParams
	 * @param string $filename
	 *
	 * @return SearchSpring_Manager_Generator_ProductGenerator
	 */
	private function makeFeedGenerator(
		SearchSpring_Manager_Transformer_ProductCollectionToRecordCollectionTransformer $transformer,
		SearchSpring_Manager_Entity_RequestParams $requestParams,
		$filename
	) {
		$collectionProvider = new SearchSpring_Manager_Provider_ProductCollection_FeedProvider($requestParams);

		$xmlWriter = new XMLWriter();
		$writerParams = new SearchSpring_Manager_Writer_Product_Params_FileWriterParams(
			$requestParams,
			$collectionProvider->getCollectionCount(),
			$filename,
			Mage::helper('searchspring_manager')->getFeedPath()
		);
		$writer = new SearchSpring_Manager_Writer_Product_FileWriter($xmlWriter, $writerParams);
		$generator = new SearchSpring_Manager_Generator_ProductGenerator($collectionProvider, $writer, $transformer);

		return $generator;
	}

	/**
	 * Make a product generator
	 *
	 * Uses a product type product collection provider and response writer
	 *
	 * @param SearchSpring_Manager_Transformer_ProductCollectionToRecordCollectionTransformer $transformer
	 * @param array $ids
	 * @param SearchSpring_Manager_Entity_RequestParams $requestParams
	 *
	 * @return SearchSpring_Manager_Generator_ProductGenerator
	 */
	private function makeProductGenerator(
		SearchSpring_Manager_Transformer_ProductCollectionToRecordCollectionTransformer $transformer,
		array $ids,
		SearchSpring_Manager_Entity_RequestParams $requestParams
	) {
		$collectionProvider = new SearchSpring_Manager_Provider_ProductCollection_ProductProvider($ids, $requestParams);
		$writerParams = new SearchSpring_Manager_Writer_Product_Params_ResponseWriterParams(
			$requestParams,
			$collectionProvider->getCollectionCount()
		);
		$writer = new SearchSpring_Manager_Writer_Product_ResponseWriter($writerParams);
		$generator = new SearchSpring_Manager_Generator_ProductGenerator($collectionProvider, $writer, $transformer);

		return $generator;


	}

	/**
	 * Add external/third party operations, these are specified via
	 * core config. These are based on the existence and status of
	 * external modules.
	 *
	 * @param SearchSpring_Manager_Entity_OperationsCollection $operationsCollection
	 * @param SearchSpring_Manager_Builder_OperationBuilder $operationsBuilder
	 */
	public function addThirdPartyOperations($operationsCollection, $operationsBuilder) {

		$hlp = Mage::helper('searchspring_manager');

		if (Mage::getConfig()->getNode(self::XML_PATH_OPERATION_THIRD_PARTY)) {

			foreach (Mage::getConfig()->getNode(self::XML_PATH_OPERATION_THIRD_PARTY)->children() as $moduleName => $operations) {

				if (!$operations) continue;

				// Make Sure Third Party Module is enabled
				if (!$hlp->isModuleEnabled($moduleName)) {
					continue;
				}

				// Each Third Party Module Mentioned can have multiple operations
				foreach($operations->children() as $operationKey => $operationInfo) {

					// TODO - Maybe allow an operation to be disabled as a config within $operationInfo
					// TODO - Maybe allow an parameters to be passed to operation via $operationInfo
					$params = array();

					// Create Operation and Append to Collection
					$operationsCollection->append($operationsBuilder->build((string)$operationInfo->class, $params, false));

				}
			}
		}
	}

	/**
	 * Go through each operation and build a list of reserved fields to not use
	 * for attribute fields
	 */
	 public function buildReservedFields($operationsCollection) {
		 $reservedFields = array();
		 foreach ($operationsCollection as $operation) {
			 $reservedFields = array_merge($reservedFields, $operation->getLocalReservedFields());
		 }

		 foreach ($operationsCollection as $operation) {
			 $operation->setGlobalReservedFields($reservedFields);
		 }

	 }

}
