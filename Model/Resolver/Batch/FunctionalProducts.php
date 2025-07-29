<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Renga\CustomLinkProduct\Model\Resolver\Batch;

use Magento\RelatedProductGraphQl\Model\Resolver\Batch\AbstractLikedProducts;
use Renga\CustomLinkProduct\Model\Product\Link;

/**
 * Functional Products Resolver
 */
class FunctionalProducts extends AbstractLikedProducts
{
    /**
     * @inheritDoc
     */
    protected function getNode(): string
    {
        return 'functional_link_products';
    }

    /**
     * @inheritDoc
     */
    protected function getLinkType(): int
    {
        return Link::LINK_TYPE_FUNCTIONAL;
    }
}
