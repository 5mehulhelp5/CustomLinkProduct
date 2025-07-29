<?php

namespace Renga\CustomLinkProduct\Ui\DataProvider\Product\Related;

use Magento\Catalog\Ui\DataProvider\Product\Related\AbstractDataProvider;
use Renga\CustomLinkProduct\Model\Product\Link;

class RepairLinkDataProvider extends AbstractDataProvider
{
    /**
     * {@inheritdoc}
     */
    protected function getLinkType()
    {
        return Link::LINK_TYPE_REPAIR_CODE;
    }
}
