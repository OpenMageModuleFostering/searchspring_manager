<?php
/**
 * SetRatings.php
 *
 * @copyright B7 Interactive, LLC. All Rights Reserved.
 */

/**
 * Class SearchSpring_Manager_Operation_Product_SetRatings
 *
 * Set rating data to the feed
 *
 * @author Nate Brunette <nate@b7interactive.com>
 */
class SearchSpring_Manager_Operation_Product_SetRatings extends SearchSpring_Manager_Operation_Product
{
    /**#@+
     * Feed Constants
     */
    const FEED_RATING_PERCENTAGE = 'rating_percentage';
    const FEED_RATING_STAR = 'rating_star';
    const FEED_RATING_COUNT = 'rating_count';
    const FEED_REVIEWS_COUNT = 'reviews_count';
    /**#@-*/

    protected $_localReservedFields = array(
        self::FEED_RATING_PERCENTAGE,
        self::FEED_RATING_STAR,
        self::FEED_RATING_COUNT,
        self::FEED_REVIEWS_COUNT,
    );

    /**
     * Set rating data to the feed
     *     - rating_percentage
     *     - rating_star
     *     - rating_count
     *
     * @param Mage_Catalog_Model_Product $product
     *
     * @return $this
     */
    public function perform(Mage_Catalog_Model_Product $product)
    {
        $reviews = Mage::getModel('review/review')
            ->getResourceCollection()
            ->addEntityFilter('product', $product->getId())
            ->addStatusFilter(Mage_Review_Model_Review::STATUS_APPROVED)
            ->addRateVotes();

        $ratings = array();
        foreach($reviews->getItems() as $review) {
            foreach($review->getRatingVotes() as $vote) {
                $ratings[] = $vote->getPercent();
            }
        }

        $ratingCount = count($ratings);

        $ratingPercentage = (0 === $ratingCount) ? 0 : array_sum($ratings) / $ratingCount;
        $this->getRecords()->set(self::FEED_RATING_PERCENTAGE, $ratingPercentage);
        $this->getRecords()->set(self::FEED_RATING_STAR, round($ratingPercentage / 20));
        $this->getRecords()->set(self::FEED_RATING_COUNT, $ratingCount);
        $this->getRecords()->set(self::FEED_REVIEWS_COUNT, count($reviews));

        return $this;
    }
}
