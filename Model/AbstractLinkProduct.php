<?php
namespace Renga\CustomLinkProduct\Model;

use Magento\Catalog\Model\ProductLink\CollectionProvider\Proxy;
use Magento\Catalog\Model\Product;
use Magento\Framework\DataObject;
use Renga\CustomLinkProduct\Model\Product\Link;

/**
 * Abstract base class for product link models
 */
abstract class AbstractLinkProduct extends DataObject
{
    /**
     * Product link instance
     * @var Link
     */
    protected $linkInstance;

    /**
     * Collection provider
     * @var Proxy
     */
    protected $collectionProvider;

    /**
     * Constructor
     *
     * @param Proxy $collectionProvider
     * @param Link $productLink
     */
    public function __construct(
        Proxy $collectionProvider,
        Link $productLink
    ){
        $this->collectionProvider = $collectionProvider;
        $this->linkInstance = $productLink;
    }

    /**
     * Retrieve link instance
     *
     * @return Link
     */
    public function getLinkInstance(): Link
    {
        return $this->linkInstance;
    }

    /**
     * Get the link type code (e.g., 'similar_link', 'repair_link', 'functional_link')
     *
     * @return string
     */
    abstract protected function getLinkTypeCode(): string;

    /**
     * Apply the appropriate link type to the link instance
     *
     * @return Link
     */
    abstract protected function applyLinkType(): Link;

    /**
     * Retrieve array of linked products
     *
     * @param Product $currentProduct
     * @return array
     */
    protected function getLinkedProducts(Product $currentProduct): array
    {
        $dataKey = $this->getLinkTypeCode() . '_products';
        $hasMethodName = 'has' . str_replace(' ', '', ucwords(str_replace('_', ' ', $dataKey)));

        if (!$this->$hasMethodName()) {
            $products = [];
            $collection = $this->getLinkedProductCollection($currentProduct);
            foreach ($collection as $product) {
                $products[] = $product;
            }
            $this->setData($dataKey, $products);
        }
        return $this->getData($dataKey);
    }

    /**
     * Retrieve linked products identifiers
     *
     * @param Product $currentProduct
     * @return array
     */
    protected function getLinkedProductIds(Product $currentProduct): array
    {
        $dataKey = $this->getLinkTypeCode() . '_product_ids';
        $hasMethodName = 'has' . str_replace(' ', '', ucwords(str_replace('_', ' ', $dataKey)));

        if (!$this->$hasMethodName()) {
            $ids = [];
            foreach ($this->getLinkedProducts($currentProduct) as $product) {
                $ids[] = $product->getId();
            }
            $this->setData($dataKey, $ids);
        }
        return $this->getData($dataKey);
    }

    /**
     * Retrieve collection of linked products
     *
     * @param Product $currentProduct
     * @return \Magento\Catalog\Model\ResourceModel\Product\Link\Product\Collection
     */
    protected function getLinkedProductCollection(Product $currentProduct): \Magento\Catalog\Model\ResourceModel\Product\Link\Product\Collection
    {
        $collection = $this->applyLinkType()->getProductCollection()->setIsStrongMode();
        $collection->setProduct($currentProduct);
        return $collection;
    }
}
