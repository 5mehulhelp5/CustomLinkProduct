<?php

namespace Renga\CustomLinkProduct\Model\ProductLink\CollectionProvider;

use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\ProductLink\CollectionProviderInterface;

class SimilarProducts implements CollectionProviderInterface
{
    public $similarLinkModel;
    /**
     * @param \Renga\CustomLinkProduct\Model\SimilarProduct $similarLinkModel
     */
    public function __construct(
        \Renga\CustomLinkProduct\Model\SimilarProduct $similarLinkModel
    ) {
        $this->similarLinkModel = $similarLinkModel;
    }

    /**
     * {@inheritdoc}
     */
    public function getLinkedProducts(Product $product)
    {
        return (array) $this->similarLinkModel->getSimilarLinkProducts($product);
    }
}
