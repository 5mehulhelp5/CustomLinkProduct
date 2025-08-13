<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Renga\CustomLinkProduct\Block\Product;

use Renga\CustomLinkProduct\Model\Product\Link;

/**
 * Catalog repair products block
 */
class RepairProducts extends AbstractLinkProducts
{
    /**
     * Get the link type method name to call on the link model
     *
     * @return string
     */
    protected function getLinkTypeMethod(): string
    {
        return 'useRepairLinks';
    }

    /**
     * Get block title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->getData('title') ?: __('Repair Products');
    }
}
