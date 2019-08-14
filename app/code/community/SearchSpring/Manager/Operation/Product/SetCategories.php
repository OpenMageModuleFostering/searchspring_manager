<?php
/**
 * SetCategories.php
 *
 * @copyright B7 Interactive, LLC. All Rights Reserved.
 */

/**
 * Class SearchSpring_Manager_Operation_Product_SetCategories
 *
 * Set category data to the feed
 *
 * @author Nate Brunette <nate@b7interactive.com>
 */
class SearchSpring_Manager_Operation_Product_SetCategories extends SearchSpring_Manager_Operation_Product
{
    /**#@+
     * Feed Constants
     */
    const FEED_CATEGORY_HIERARCHY = 'category_hierarchy';
    const FEED_CATEGORY_NAME = 'category';
	const FEED_CATEGORY_IDS = 'category_ids';
    /**#@-*/

	/**
	 * Loaded category model cache
	 *
     * @var Mage_Catalog_Model_Category[]
	 */
	protected $_categoryCache = array();

    /**
     * Sets category data to feed
     *     - category_hierarchy
     *     - category_name
     *
     * @param Mage_Catalog_Model_Product $product
     *
     * @return $this
     */
    public function perform(Mage_Catalog_Model_Product $product)
    {
        $categoryHierarchies = array();
        $categoryNames = array();
		$categoryIds = array();


        /** @var Mage_Catalog_Model_Category $category */
        foreach($product->getCategoryIds() as $categoryId) {
            // load category data
            $category = $this->_getCategory($categoryId);

            if (!$category->getData('is_active')) {
                continue;
            }

            // Skip this store's root category
            if ($this->_isRoot($category)) {
                continue;
            }

            $categoryPath = $category->getPathIds();

			$categoryIds = array_merge($categoryIds, $categoryPath);

            $hierarchies = $this->buildCategoryHierarchy($categoryPath);

            foreach ($hierarchies as $value) {
                $categoryHierarchies[] = $value;
            }

            $categoryNames[] = $category->getName();
        }

        $categoryHierarchies = array_unique($categoryHierarchies);

        if (empty($categoryHierarchies)) {
            $this->getRecords()->set(self::FEED_CATEGORY_HIERARCHY, $categoryHierarchies);
        }

        // we need to do an additional loop here because array_unique preserves keys
        // this causes an issue when converting to json as it will create an object if keys aren't sequential
        foreach ($categoryHierarchies as $hierarchy) {
            $this->getRecords()->add(self::FEED_CATEGORY_HIERARCHY, $hierarchy);
        }

		$this->getRecords()->set(self::FEED_CATEGORY_IDS, array_values(array_unique($categoryIds)));

        $this->getRecords()->set(self::FEED_CATEGORY_NAME, $categoryNames);

        return $this;
    }

    /**
     * Build the category hierarchy based on path
     *
     * Will return an array with all levels
     *
     * Example:
     *     Level 1
     *     Level 1 | Level 2
     *     Level 1 | Level 2 | Level 3
     *
     * @param array $categoryPath
     *
     * @return array
     */
    private function buildCategoryHierarchy(array $categoryPath)
    {
        $hierarchy = array();
        $currentHierarchy = array();
        foreach ($categoryPath as $categoryId) {
            $category = $this->_getCategory($categoryId);

            // Skip this store's root category
            if ($this->_isRoot($category)) {
                continue;
             }

            $currentHierarchy[] = $category->getName();
            $hierarchy[] = implode('/', $currentHierarchy);
        }

        return $hierarchy;
    }

	protected function _getCategory($categoryId) {
		if (!isset($this->_categoryCache[$categoryId])) {
			$this->_categoryCache[$categoryId] = Mage::getModel('catalog/category')->load($categoryId);
		}
		return $this->_categoryCache[$categoryId];
	}

	protected function _isRoot($category) {
		return (
			0 === (int)$category->getLevel() ||
			1 === (int)$category->getLevel()
		);
	}

}
