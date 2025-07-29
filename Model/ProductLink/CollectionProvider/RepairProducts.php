<?php

namespace Renga\CustomLinkProduct\Model\ProductLink\CollectionProvider;

use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\ProductLink\CollectionProviderInterface;

class RepairProducts implements CollectionProviderInterface
{
    public $repairLinkModel;
    /**
     * @param \Renga\CustomLinkProduct\Model\RepairProduct $repairLinkModel
     */
    public function __construct(
        \Renga\CustomLinkProduct\Model\RepairProduct $repairLinkModel
    ) {
        $this->repairLinkModel = $repairLinkModel;
    }

    /**
     * {@inheritdoc}
     */
    public function getLinkedProducts(Product $product)
    {
        return (array) $this->repairLinkModel->getRepairLinkProducts($product);
    }
}
