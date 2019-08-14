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
    const FEED_CATEGORY_NAME = 'category_name';
	const FEED_CATEGORY_IDS = 'category_ids';
    /**#@-*/

	/**
	 * Category names we should skip
	 *
	 * @var array
	 */
	private $skipCategories = array('Root Catalog', 'Default Category', null);

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
            $category = Mage::getModel('catalog/category')->load($categoryId);

            if (!$category->getData('is_active')) {
                continue;
            }

            $categoryName = $category->getData('name');
            if (in_array($categoryName, $this->skipCategories)) {
                continue;
            }

            $categoryPath = $category->getPathIds();

			$categoryIds = array_merge($categoryIds, $categoryPath);

            $hierarchies = $this->buildCategoryHierarchy($categoryPath);

            foreach ($hierarchies as $value) {
                $categoryHierarchies[] = $value;
            }

            $categoryNames[] = $categoryName;
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
            $category = Mage::getModel('catalog/category')->load($categoryId);

            $categoryName = $category->getData('name');
            if (in_array($categoryName, $this->skipCategories)) {
                continue;
            }
            $currentHierarchy[] = $categoryName;
            $hierarchy[] = implode('/', $currentHierarchy);
        }

        return $hierarchy;
    }
}
