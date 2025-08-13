<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Renga\CustomLinkProduct\Block\Product;

use Renga\CustomLinkProduct\Model\Product\Link;

/**
 * Catalog similar products block
 */
class SimilarProducts extends AbstractLinkProducts
{
    /**
     * Get the link type method name to call on the link model
     *
     * @return string
     */
    protected function getLinkTypeMethod(): string
    {
        return 'useSimilarLinks';
    }

    /**
     * Get block title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->getData('title') ?: __('Similar Products');
    }
}
