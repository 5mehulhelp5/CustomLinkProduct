<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Renga\CustomLinkProduct\Model\Resolver\Mutation;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Api\Data\ProductLinkInterfaceFactory;
use Magento\Catalog\Api\ProductLinkManagementInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Exception\GraphQlInputException;
use Magento\Framework\GraphQl\Exception\GraphQlNoSuchEntityException;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Renga\CustomLinkProduct\Model\Product\Link;
use Renga\CustomLinkProduct\Model\Utility\ProductLinkUtility;

/**
 * Resolver for updating all types of product links in a single request
 */
class UpdateAllLinkProducts implements ResolverInterface
{
    /**
     * @var ProductRepositoryInterface
     */
    private $productRepository;

    /**
     * @var ProductLinkManagementInterface
     */
    private $productLinkManagement;

    /**
     * @var ProductLinkInterfaceFactory
     */
    private $productLinkFactory;

    /**
     * @var ProductLinkUtility
     */
    private $productLinkUtility;

    /**
     * @param ProductRepositoryInterface $productRepository
     * @param ProductLinkManagementInterface $productLinkManagement
     * @param ProductLinkInterfaceFactory $productLinkFactory
     * @param ProductLinkUtility $productLinkUtility
     */
    public function __construct(
        ProductRepositoryInterface $productRepository,
        ProductLinkManagementInterface $productLinkManagement,
        ProductLinkInterfaceFactory $productLinkFactory,
        ProductLinkUtility $productLinkUtility
    ) {
        $this->productRepository = $productRepository;
        $this->productLinkManagement = $productLinkManagement;
        $this->productLinkFactory = $productLinkFactory;
        $this->productLinkUtility = $productLinkUtility;
    }

    /**
     * @inheritdoc
     */
    public function resolve(
        Field $field,
        $context,
        ResolveInfo $info,
        array $value = null,
        array $args = null
    ) {
        $this->validateInputArgs($args);

        $productSku = $args['input']['product_sku'];
        $similarProductSkus = $args['input']['similar_product_skus'] ?? [];
        $repairProductSkus = $args['input']['repair_product_skus'] ?? [];
        $functionalProductSkus = $args['input']['functional_product_skus'] ?? [];
        $position = $args['input']['position'] ?? 0;

        try {
            // Get and validate the main product
            $product = $this->productLinkUtility->getAndValidateProduct($productSku);
            $productData = ['sku' => $product->getSku(), 'name' => $product->getName()];

            // Process each link type
            $similarResult = $this->productLinkUtility->processLinks($productSku, $similarProductSkus, Link::LINK_TYPE_SIMILAR_CODE, $position);
            $repairResult = $this->productLinkUtility->processLinks($productSku, $repairProductSkus, Link::LINK_TYPE_REPAIR_CODE, $position);
            $functionalResult = $this->productLinkUtility->processLinks($productSku, $functionalProductSkus, Link::LINK_TYPE_FUNCTIONAL_CODE, $position);

            // Determine overall success
            $overallSuccess = $similarResult['success'] || $repairResult['success'] || $functionalResult['success'];
            $overallMessage = $overallSuccess
                ? __('Product links updated successfully.')
                : __('No valid product links to add.');

            return [
                'product' => $productData,
                'success' => $overallSuccess,
                'message' => $overallMessage,
                'similar_links_result' => $this->productLinkUtility->formatLinkTypeResult('similar', $similarResult),
                'repair_links_result' => $this->productLinkUtility->formatLinkTypeResult('repair', $repairResult),
                'functional_links_result' => $this->productLinkUtility->formatLinkTypeResult('functional', $functionalResult)
            ];
        } catch (NoSuchEntityException $e) {
            throw new GraphQlNoSuchEntityException(
                __('The product with SKU "%1" does not exist.', $productSku)
            );
        } catch (InputException | LocalizedException $e) {
            throw new GraphQlInputException(__($e->getMessage()));
        } catch (\Exception $e) {
            throw new GraphQlInputException(__('An error occurred while updating product links: %1', $e->getMessage()));
        }
    }

    /**
     * Validate input arguments
     *
     * @param array|null $args
     * @throws GraphQlInputException
     */
    private function validateInputArgs(?array $args): void
    {
        if (!isset($args['input']) || !isset($args['input']['product_sku'])) {
            throw new GraphQlInputException(__('Required parameters are missing'));
        }

        // At least one of the link type arrays must be provided
        if (
            (!isset($args['input']['similar_product_skus']) || empty($args['input']['similar_product_skus'])) &&
            (!isset($args['input']['repair_product_skus']) || empty($args['input']['repair_product_skus'])) &&
            (!isset($args['input']['functional_product_skus']) || empty($args['input']['functional_product_skus']))
        ) {
            throw new GraphQlInputException(
                __('At least one of similar_product_skus, repair_product_skus, or functional_product_skus must be provided')
            );
        }
    }
}
