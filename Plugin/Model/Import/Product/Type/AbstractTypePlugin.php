<?php

namespace Renga\CustomLinkProduct\Plugin\Model\Import\Product\Type;

use Magento\CatalogImportExport\Model\Import\Product\Type\AbstractType;

class AbstractTypePlugin
{
    /**
     * @param AbstractType $subject
     * @param string[]     $result
     * @return string[]
     */
    public function afterGetCustomFieldsMapping(AbstractType $subject, array $result): array
    {
        $result['_similarlink_sku'] = 'similarlink_skus';
        $result['_repairlink_sku'] = 'repairlink_skus';
        $result['_functionallink_sku'] = 'functionallink_skus';
        return $result;
    }
}
