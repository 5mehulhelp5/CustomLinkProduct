<?php
namespace Renga\CustomLinkProduct\Model;

use Magento\Catalog\Model\ProductLink\CollectionProvider\Proxy;
use Magento\Catalog\Model\Product;
use Renga\CustomLinkProduct\Model\Product\Link;

/**
 * Repair Product Link Model
 */
class RepairProduct extends AbstractLinkProduct
{
    /**
     * Get the link type code
     *
     * @return string
     */
    protected function getLinkTypeCode(): string
    {
        return 'repair_link';
    }

    /**
     * Apply the appropriate link type to the link instance
     *
     * @return Link
     */
    protected function applyLinkType(): Link
    {
        return $this->getLinkInstance()->useRepairLinks();
    }

    /**
     * Retrieve array of repair link products
     *
     * @param Product $currentProduct
     * @return array
     */
    public function getRepairLinkProducts(Product $currentProduct): array
    {
        return $this->getLinkedProducts($currentProduct);
    }

    /**
     * Retrieve repair link products identifiers
     *
     * @param Product $currentProduct
     * @return array
     */
    public function getRepairLinkProductIds(Product $currentProduct): array
    {
        return $this->getLinkedProductIds($currentProduct);
    }

    /**
     * Retrieve collection repair link product
     *
     * @param Product $currentProduct
     * @return \Magento\Catalog\Model\ResourceModel\Product\Link\Product\Collection
     */
    public function getRepairLinkProductCollection(Product $currentProduct): \Magento\Catalog\Model\ResourceModel\Product\Link\Product\Collection
    {
        return $this->getLinkedProductCollection($currentProduct);
    }
}
