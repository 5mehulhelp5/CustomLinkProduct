<?php
namespace Renga\CustomLinkProduct\Model;

use Magento\Catalog\Model\ProductLink\CollectionProvider\Proxy;
use Magento\Catalog\Model\Product;
use Renga\CustomLinkProduct\Model\Product\Link;

/**
 * Similar Product Link Model
 */
class SimilarProduct extends AbstractLinkProduct
{
    /**
     * Get the link type code
     *
     * @return string
     */
    protected function getLinkTypeCode(): string
    {
        return 'similar_link';
    }

    /**
     * Apply the appropriate link type to the link instance
     *
     * @return Link
     */
    protected function applyLinkType(): Link
    {
        return $this->getLinkInstance()->useSimilarLinks();
    }

    /**
     * Retrieve array of similar link products
     *
     * @param Product $currentProduct
     * @return array
     */
    public function getSimilarLinkProducts(Product $currentProduct): array
    {
        return $this->getLinkedProducts($currentProduct);
    }

    /**
     * Retrieve similar link products identifiers
     *
     * @param Product $currentProduct
     * @return array
     */
    public function getSimilarLinkProductIds(Product $currentProduct): array
    {
        return $this->getLinkedProductIds($currentProduct);
    }

    /**
     * Retrieve collection similar link product
     *
     * @param Product $currentProduct
     * @return \Magento\Catalog\Model\ResourceModel\Product\Link\Product\Collection
     */
    public function getSimilarLinkProductCollection(Product $currentProduct): \Magento\Catalog\Model\ResourceModel\Product\Link\Product\Collection
    {
        return $this->getLinkedProductCollection($currentProduct);
    }
}
