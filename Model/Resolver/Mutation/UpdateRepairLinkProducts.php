<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Renga\CustomLinkProduct\Model\Resolver\Mutation;

use Renga\CustomLinkProduct\Model\Product\Link;

/**
 * Resolver for updating repair link products
 */
class UpdateRepairLinkProducts extends AbstractUpdateProductLinks
{
    /**
     * Get the link type for this resolver
     *
     * @return string
     */
    protected function getLinkType(): string
    {
        return Link::LINK_TYPE_REPAIR_CODE;
    }
}
