<?php
/**
 * SetPricing.php
 *
 * @copyright B7 Interactive, LLC. All Rights Reserved.
 */

/**
 * Class SearchSpring_Manager_Operation_Product_SetPricing
 *
 * Set pricing data to the feed
 *
 * @author Nate Brunette <nate@b7interactive.com>
 */
class SearchSpring_Manager_Operation_Product_SetPricing extends SearchSpring_Manager_Operation_Product
{
    protected $_localReservedFields = array(
        'price',
        'regular_price',
        'normal_price',
        'final_price'
    );

    /**
     * Set the pricing data
     *
     * @param Mage_Catalog_Model_Product $product
     *
     * @return $this
     * @throws UnexpectedValueException If pricing is not the right type
     */
    public function perform(Mage_Catalog_Model_Product $product)
    {
        /** @var SearchSpring_Manager_Factory_PricingFactory $pricingFactory */
        $pricingFactory = $this->getParameter('pricingFactory');

        // this will throw an exception if the product is not the right type
        // that's OK because if it does, we did something wrong
        $pricingStrategy = $pricingFactory->make($product);

        $pricingStrategy->calculatePrices();

        // prices should have been calculated earlier to determine if products should be deleted
        $this->getRecords()->set('price', $pricingStrategy->getSalePrice());
        $this->getRecords()->set('regular_price', $pricingStrategy->getTierPrice());
        $this->getRecords()->set('normal_price', $pricingStrategy->getNormalPrice());
        $this->getRecords()->set('final_price', $pricingStrategy->getFinalPrice());

        return $this;
    }

    /**
     * If price is zero, set invalid
     *
     * @param Mage_Catalog_Model_Product $product
     *
     * @return bool
     */
    public function isValid(Mage_Catalog_Model_Product $product)
    {
        /** @var SearchSpring_Manager_Factory_PricingFactory $pricingFactory */
        $pricingFactory = $this->getParameter('pricingFactory');
        $displayZeroPrice = $this->getParameter('displayZeroPrice');

        try {
            $pricingStrategy = $pricingFactory->make($product);
        } catch (UnexpectedValueException $e) {
            // product is not a type we're currently handling
            return false;
        }

        // we must run this before we can check prices
        $pricingStrategy->calculatePrices();

        $productHasZeroPrice = (0 == $pricingStrategy->getNormalPrice()
            && 0 == $pricingStrategy->getTierPrice()
            && 0 == $pricingStrategy->getSalePrice()
        );

        // if we shouldn't show 0 prices and the product price is 0
        if ($productHasZeroPrice && !$displayZeroPrice) {
            return false;
        }

        return true;
    }
}
