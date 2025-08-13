<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Renga\CustomLinkProduct\Block\Product;

use Magento\Catalog\Block\Product\AbstractProduct;
use Magento\Catalog\Block\Product\Context;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\Product\Visibility as ProductVisibility;
use Magento\Catalog\Model\ResourceModel\Product\Collection;
use Magento\Framework\App\ActionInterface;
use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\Url\Helper\Data as UrlHelper;
use Renga\CustomLinkProduct\Model\Product\Link;

/**
 * Abstract base class for all link product blocks
 */
abstract class AbstractLinkProducts extends AbstractProduct implements IdentityInterface
{
    /**
     * @var Collection
     */
    protected $_itemCollection;

    /**
     * Catalog product visibility
     *
     * @var ProductVisibility
     */
    protected $_catalogProductVisibility;

    /**
     * @var Link
     */
    protected $_linkModel;

    /**
     * @var UrlHelper
     */
    protected $urlHelper;

    /**
     * @param Context $context
     * @param ProductVisibility $catalogProductVisibility
     * @param Link $linkModel
     * @param UrlHelper $urlHelper
     * @param array $data
     */
    public function __construct(
        Context $context,
        ProductVisibility $catalogProductVisibility,
        Link $linkModel,
        UrlHelper $urlHelper,
        array $data = []
    ) {
        $this->_catalogProductVisibility = $catalogProductVisibility;
        $this->_linkModel = $linkModel;
        $this->urlHelper = $urlHelper;
        parent::__construct($context, $data);
    }

    /**
     * Prepare data
     *
     * @return $this
     */
    protected function _prepareData()
    {
        $product = $this->getProduct();
        /* @var $product Product */

        // If product is not available, return empty collection
        if (!$product) {
            $this->_itemCollection = new Collection();
            return $this;
        }

        try {
            // Call the appropriate link type method on the link model
            $linkTypeMethod = $this->getLinkTypeMethod();
            $this->_itemCollection = $this->_linkModel->$linkTypeMethod()
                ->getProductCollection()
                ->setIsStrongMode();
            $this->_itemCollection->setProduct($product)
                ->addAttributeToSelect('required_options')
                ->setPositionOrder()
                ->addStoreFilter();

            $this->_addProductAttributesAndPrices($this->_itemCollection);
            $this->_itemCollection->setVisibility($this->_catalogProductVisibility->getVisibleInCatalogIds());

            $this->_itemCollection->load();

            foreach ($this->_itemCollection as $product) {
                $product->setDoNotUseCategoryId(true);
            }
        } catch (\Exception $e) {
            // Log the error and return empty collection
            $this->_logger->critical($e);
            $this->_itemCollection = new Collection();
        }

        return $this;
    }

    /**
     * Before to html handler
     *
     * @return $this
     */
    protected function _beforeToHtml()
    {
        $this->_prepareData();
        return parent::_beforeToHtml();
    }

    /**
     * Get collection items
     *
     * @return Collection
     */
    public function getItems()
    {
        /**
         * getIdentities() depends on _itemCollection populated, but it can be empty if the block is hidden
         */
        if ($this->_itemCollection === null) {
            $this->_prepareData();
        }
        return $this->_itemCollection;
    }

    /**
     * Return identifiers for produced content
     *
     * @return array
     */
    public function getIdentities()
    {
        $identities = [];
        foreach ($this->getItems() as $item) {
            $identities[] = $item->getIdentities();
        }
        return array_merge([], ...$identities);
    }

    /**
     * Get post parameters for adding product to cart
     *
     * @param Product $product
     * @return array
     */
    public function getAddToCartPostParams(Product $product)
    {
        $url = $this->getAddToCartUrl($product, ['_escape' => false]);
        return [
            'action' => $url,
            'data' => [
                'product' => (int) $product->getEntityId(),
                ActionInterface::PARAM_NAME_URL_ENCODED => $this->urlHelper->getEncodedUrl($url),
            ]
        ];
    }

    /**
     * Get the link type method name to call on the link model
     *
     * @return string
     */
    abstract protected function getLinkTypeMethod(): string;

    /**
     * Get block title
     *
     * @return string
     */
    abstract public function getTitle();
}
