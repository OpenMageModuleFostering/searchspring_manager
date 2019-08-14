<?php
/**
 * Image.php
 *
 * @copyright B7 Interactive, LLC. All Rights Reserved.
 */

/**
 * Class SearchSpring_Manager_Helper_Catalog_Image
 *
 * Extension of core Catalog Image helper, to help optimize for speed
 *
 * Everytime we call 'setBaseFile' on the image model, there are several
 * file existence checks, and a really expensive memory check that calls
 * file size stats. To get around this, i've added a function for people
 * who first want to just see if the desired resized image exists, and
 * what it's filename is on disk. If the file does not exist, then call
 * the regular __toString function to actually run the core full logic,
 * and create the new resized file.
 *
 * @author Jake Shelby <jake@b7interactive.com>
 */
class SearchSpring_Manager_Helper_Catalog_Image extends Mage_Catalog_Helper_Image
{

	// Overriding this to use our model instead
	protected function _setModel($model)
	{
		$this->_model = Mage::getModel('searchspring_manager/catalog_product_image');
		return $this;
	}

	// Overriding this, so we're not calling set base file on our image model initially
	public function init(Mage_Catalog_Model_Product $product, $attributeName, $imageFile=null)
	{
		if(is_null($imageFile)) {
			$imageFile = $product->getData($attributeName);
		}

		return parent::init($product, $attributeName, $imageFile);
	}

	public function ifCachedGetUrl()
	{
		$model = $this->_getModel();

		// For speed, we don't need to run memory checks
		$model->skipMemoryCheck();

		try {

			// Runs logic to build supposed mutated image filename
			$model->setBaseFile($this->getImageFile());

			if ($model->isCached()) {
				$url = $model->getUrl();
			} else {
				$url = false;
			}

		} catch (Exception $e) {
			$url = Mage::getDesign()->getSkinUrl($this->getPlaceholder());
		}

		// So we don't mess up anything else, re-enable memory checks
		$model->skipMemoryCheck(false);

		return $url;
	}

}
