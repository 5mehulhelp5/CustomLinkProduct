<?php

namespace Renga\CustomLinkProduct\Model\ProductLink\CollectionProvider;

use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\ProductLink\CollectionProviderInterface;

class FunctionalProducts implements CollectionProviderInterface
{
    private $functionalLinkModel;
    /**
     * @param \Renga\CustomLinkProduct\Model\FunctionalProduct $functionalLinkModel
     */
    public function __construct(
        \Renga\CustomLinkProduct\Model\FunctionalProduct $functionalLinkModel
    ) {
        $this->functionalLinkModel = $functionalLinkModel;
    }

    /**
     * {@inheritdoc}
     */
    public function getLinkedProducts(Product $product)
    {
        return (array) $this->functionalLinkModel->getFunctionalLinkProducts($product);
    }
}
