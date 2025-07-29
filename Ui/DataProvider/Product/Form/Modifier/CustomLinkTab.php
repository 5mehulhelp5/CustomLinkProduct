<?php

namespace Renga\CustomLinkProduct\Ui\DataProvider\Product\Form\Modifier;

use Magento\Catalog\Ui\DataProvider\Product\Form\Modifier\Related;
use Magento\Ui\Component\Form\Fieldset;
use Renga\CustomLinkProduct\Model\Product\Link;

class CustomLinkTab extends Related
{
    /**
     * @var string
     */
    private static $previousGroup = 'search-engine-optimization';
    /**
     * @var int
     */
    private static $sortOrder = 90;
    /**
     * {@inheritdoc}
     */
    public function modifyMeta(array $meta)
    {
        $meta = array_replace_recursive(
            $meta,
            [
                static::GROUP_RELATED => [
                    'children' => [
                        $this->scopePrefix . Link::LINK_TYPE_SIMILAR_CODE => $this->getSimilarFieldset(),
                        $this->scopePrefix . Link::LINK_TYPE_REPAIR_CODE => $this->getRepairFieldset(),
                        $this->scopePrefix . Link::LINK_TYPE_FUNCTIONAL_CODE => $this->getFunctionalFieldset()
                    ],
                    'arguments' => [
                        'data' => [
                            'config' => [
                                'label' => __('Related Products, Up-Sells, Cross-Sells and Custom Types'),
                                'collapsible' => true,
                                'componentType' => Fieldset::NAME,
                                'dataScope' => static::DATA_SCOPE,
                                'sortOrder' => $this->getNextGroupSortOrder(
                                    $meta,
                                    self::$previousGroup,
                                    self::$sortOrder
                                ),
                            ],
                        ],
                    ],
                ],
            ]
        );
        return $meta;
    }
    /**
     * Prepares config for the Custom type products fieldset
     *
     * @return array
     */
    protected function getSimilarFieldset()
    {
        $content = __(
            'SKUs that perform the same function but may differ in form or fit, typically within the same manufacturer or brand.'
        );
        return [
            'children' => [
                'button_set' => $this->getButtonSet(
                    $content,
                    __('Similar Items Products'),
                    $this->scopePrefix . Link::LINK_TYPE_SIMILAR_CODE
                ),
                'modal' => $this->getGenericModal(
                    __('Similar Items Products'),
                    $this->scopePrefix . Link::LINK_TYPE_SIMILAR_CODE
                ),
                Link::LINK_TYPE_SIMILAR_CODE => $this->getGrid($this->scopePrefix . Link::LINK_TYPE_SIMILAR_CODE),
            ],
            'arguments' => [
                'data' => [
                    'config' => [
                        'additionalClasses' => 'admin__fieldset-section',
                        'label' => __('Similar Items Products'),
                        'collapsible' => false,
                        'componentType' => Fieldset::NAME,
                        'dataScope' => '',
                        'sortOrder' => 90,
                    ],
                ],
            ]
        ];
    }

    protected function getRepairFieldset()
    {
        $content = __(
            'Components or spare parts required for the repair or maintenance of the primary SKU, from either the same or different manufacturers or brands.'
        );
        return [
            'children' => [
                'button_set' => $this->getButtonSet(
                    $content,
                    __('Repair Parts Products'),
                    $this->scopePrefix . Link::LINK_TYPE_REPAIR_CODE
                ),
                'modal' => $this->getGenericModal(
                    __('Repair Parts Products'),
                    $this->scopePrefix . Link::LINK_TYPE_REPAIR_CODE
                ),
                Link::LINK_TYPE_REPAIR_CODE => $this->getGrid($this->scopePrefix . Link::LINK_TYPE_REPAIR_CODE),
            ],
            'arguments' => [
                'data' => [
                    'config' => [
                        'additionalClasses' => 'admin__fieldset-section',
                        'label' => __('Repair Parts Products'),
                        'collapsible' => false,
                        'componentType' => Fieldset::NAME,
                        'dataScope' => '',
                        'sortOrder' => 100,
                    ],
                ],
            ]
        ];
    }

    protected function getFunctionalFieldset()
    {
        $content = __(
            'SKUs that share the same form, fit, and function but come from different manufacturers or brands.'
        );
        return [
            'children' => [
                'button_set' => $this->getButtonSet(
                    $content,
                    __('Functional Equivalents Products'),
                    $this->scopePrefix . Link::LINK_TYPE_FUNCTIONAL_CODE
                ),
                'modal' => $this->getGenericModal(
                    __('Functional Equivalents Products'),
                    $this->scopePrefix . Link::LINK_TYPE_FUNCTIONAL_CODE
                ),
                Link::LINK_TYPE_FUNCTIONAL_CODE => $this->getGrid($this->scopePrefix . Link::LINK_TYPE_FUNCTIONAL_CODE),
            ],
            'arguments' => [
                'data' => [
                    'config' => [
                        'additionalClasses' => 'admin__fieldset-section',
                        'label' => __('Functional Equivalents Products'),
                        'collapsible' => false,
                        'componentType' => Fieldset::NAME,
                        'dataScope' => '',
                        'sortOrder' => 110,
                    ],
                ],
            ]
        ];
    }
    /**
     * Retrieve all data scopes
     *
     * @return array
     */
    protected function getDataScopes()
    {
        return [
            Link::LINK_TYPE_SIMILAR_CODE,
            Link::LINK_TYPE_REPAIR_CODE,
            Link::LINK_TYPE_FUNCTIONAL_CODE
        ];
    }
}
