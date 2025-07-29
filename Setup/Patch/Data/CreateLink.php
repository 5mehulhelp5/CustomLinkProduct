<?php
namespace Renga\CustomLinkProduct\Setup\Patch\Data;

use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;

class CreateLink implements DataPatchInterface
{
    /**
     * @param ModuleDataSetupInterface $moduleDataSetup
     */
    public function __construct(
        Protected ModuleDataSetupInterface $moduleDataSetup
    ) {
    }

    public function apply()
    {
        $setup = $this->moduleDataSetup;

        $data = [
            [
                'link_type_id' => \Renga\CustomLinkProduct\Model\Product\Link::LINK_TYPE_SIMILAR,
                'code' => \Renga\CustomLinkProduct\Model\Product\Link::LINK_TYPE_SIMILAR_CODE
            ],
            [
                'link_type_id' => \Renga\CustomLinkProduct\Model\Product\Link::LINK_TYPE_REPAIR,
                'code' => \Renga\CustomLinkProduct\Model\Product\Link::LINK_TYPE_REPAIR_CODE
            ],
            [
                'link_type_id' => \Renga\CustomLinkProduct\Model\Product\Link::LINK_TYPE_FUNCTIONAL,
                'code' => \Renga\CustomLinkProduct\Model\Product\Link::LINK_TYPE_FUNCTIONAL_CODE
            ]
        ];

        foreach ($data as $bind) {
            $setup->getConnection()
                ->insertForce($setup->getTable('catalog_product_link_type'), $bind);
        }

        $data = [
            [
                'link_type_id' => \Renga\CustomLinkProduct\Model\Product\Link::LINK_TYPE_SIMILAR,
                'product_link_attribute_code' => 'position',
                'data_type' => 'int',
            ],
            [
                'link_type_id' => \Renga\CustomLinkProduct\Model\Product\Link::LINK_TYPE_REPAIR,
                'product_link_attribute_code' => 'position',
                'data_type' => 'int',
            ],
            [
                'link_type_id' => \Renga\CustomLinkProduct\Model\Product\Link::LINK_TYPE_FUNCTIONAL,
                'product_link_attribute_code' => 'position',
                'data_type' => 'int',
            ]
        ];
        $setup->getConnection()
            ->insertMultiple($setup->getTable('catalog_product_link_attribute'), $data);
    }

    /**
     * {@inheritdoc}
     */
    public static function getDependencies()
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function getAliases()
    {
        return [];
    }
}
