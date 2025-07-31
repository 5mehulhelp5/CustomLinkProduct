<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Renga\CustomLinkProduct\Model\Resolver\Mutation;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Api\Data\ProductLinkInterface;
use Magento\Catalog\Api\Data\ProductLinkInterfaceFactory;
use Magento\Catalog\Api\ProductLinkManagementInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\Product\Attribute\Source\Status;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Exception\GraphQlInputException;
use Magento\Framework\GraphQl\Exception\GraphQlNoSuchEntityException;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;

/**
 * Abstract resolver for updating product links
 */
abstract class AbstractUpdateProductLinks implements ResolverInterface
{
    /**
     * @var ProductRepositoryInterface
     */
    protected $productRepository;

    /**
     * @var ProductLinkManagementInterface
     */
    protected $productLinkManagement;

    /**
     * @var ProductLinkInterfaceFactory
     */
    protected $productLinkFactory;

    /**
     * @param ProductRepositoryInterface $productRepository
     * @param ProductLinkManagementInterface $productLinkManagement
     * @param ProductLinkInterfaceFactory $productLinkFactory
     */
    public function __construct(
        ProductRepositoryInterface $productRepository,
        ProductLinkManagementInterface $productLinkManagement,
        ProductLinkInterfaceFactory $productLinkFactory
    ) {
        $this->productRepository = $productRepository;
        $this->productLinkManagement = $productLinkManagement;
        $this->productLinkFactory = $productLinkFactory;
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
        $linkedProductSkus = $args['input']['linked_product_skus'];
        $position = $args['input']['position'] ?? 0;

        try {
            // Get and validate the main product
            $product = $this->getAndValidateProduct($productSku);

            // Get existing links
            $existingSkus = $this->getExistingLinkedSkus($productSku);

            // Process the linked product SKUs
            $result = $this->processLinkedProductSkus($productSku, $linkedProductSkus, $existingSkus, $position);

            // Extract results
            $links = $result['links'];
            $successfulLinks = $result['successful_links'];
            $invalidLinks = $result['invalid_links'];
            $duplicateLinks = $result['duplicate_links'];
            $alreadyLinkedSkus = $result['already_linked_skus'];

            // Update links and prepare response
            if (!empty($links)) {
                // Set the product links (this will replace existing links of this type)
                $this->productLinkManagement->setProductLinks($productSku, $links);

                // Get the updated product with new links
                $updatedProduct = $this->productRepository->get($productSku);

                return $this->prepareSuccessResponse(
                    $updatedProduct,
                    $successfulLinks,
                    $invalidLinks,
                    $duplicateLinks,
                    $alreadyLinkedSkus
                );
            } else {
                return $this->prepareFailureResponse(
                    $product,
                    $invalidLinks,
                    $duplicateLinks,
                    $alreadyLinkedSkus
                );
            }
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
        if (!isset($args['input']) || !isset($args['input']['product_sku']) || !isset($args['input']['linked_product_skus'])) {
            throw new GraphQlInputException(__('Required parameters are missing'));
        }
    }

    /**
     * Get and validate product
     *
     * @param string $productSku
     * @return ProductInterface
     * @throws GraphQlInputException
     * @throws NoSuchEntityException
     */
    private function getAndValidateProduct(string $productSku): ProductInterface
    {
        $product = $this->productRepository->get($productSku);

        // Validate product status
        if ($product->getStatus() != Status::STATUS_ENABLED) {
            throw new GraphQlInputException(
                __('The product with SKU "%1" is disabled and cannot have links added.', $productSku)
            );
        }

        return $product;
    }

    /**
     * Get existing linked SKUs
     *
     * @param string $productSku
     * @return array
     */
    private function getExistingLinkedSkus(string $productSku): array
    {
        $existingLinks = $this->productLinkManagement->getLinkedItemsByType($productSku, $this->getLinkType());
        $existingSkus = [];

        foreach ($existingLinks as $existingLink) {
            $existingSkus[] = $existingLink->getLinkedProductSku();
        }

        return $existingSkus;
    }

    /**
     * Process linked product SKUs
     *
     * @param string $productSku
     * @param array $linkedProductSkus
     * @param array $existingSkus
     * @param int $position
     * @return array
     */
    private function processLinkedProductSkus(
        string $productSku,
        array $linkedProductSkus,
        array $existingSkus,
        int $position
    ): array {
        $links = [];
        $successfulLinks = [];
        $invalidLinks = [];
        $duplicateLinks = [];
        $alreadyLinkedSkus = [];

        // Check for duplicate SKUs in input
        $uniqueSkus = array_unique($linkedProductSkus);
        $duplicateInputSkus = array_diff_assoc($linkedProductSkus, $uniqueSkus);

        foreach ($uniqueSkus as $index => $linkedSku) {
            // Skip if this is a duplicate in the input
            if (in_array($linkedSku, $duplicateInputSkus)) {
                $duplicateLinks[] = $linkedSku;
                continue;
            }

            // Skip if already linked
            if (in_array($linkedSku, $existingSkus)) {
                $alreadyLinkedSkus[] = $linkedSku;
                continue;
            }

            $result = $this->processLinkedProductSku($productSku, $linkedSku, $position + $index);

            if ($result['success']) {
                $links[] = $result['link'];
                $successfulLinks[] = $result['link_info'];
            } else {
                $invalidLinks[] = $result['error'];
            }
        }

        return [
            'links' => $links,
            'successful_links' => $successfulLinks,
            'invalid_links' => $invalidLinks,
            'duplicate_links' => $duplicateLinks,
            'already_linked_skus' => $alreadyLinkedSkus
        ];
    }

    /**
     * Process a single linked product SKU
     *
     * @param string $productSku
     * @param string $linkedSku
     * @param int $position
     * @return array
     */
    private function processLinkedProductSku(string $productSku, string $linkedSku, int $position): array
    {
        try {
            // Verify the linked product exists and is enabled
            $linkedProduct = $this->productRepository->get($linkedSku);

            if ($linkedProduct->getStatus() != Status::STATUS_ENABLED) {
                return [
                    'success' => false,
                    'error' => [
                        'sku' => $linkedSku,
                        'reason' => __('Product is disabled')
                    ]
                ];
            }

            // Create the link
            $link = $this->productLinkFactory->create();
            $link->setSku($productSku);
            $link->setLinkedProductSku($linkedSku);
            $link->setLinkType($this->getLinkType());
            $link->setPosition($position);

            return [
                'success' => true,
                'link' => $link,
                'link_info' => [
                    'sku' => $linkedSku,
                    'position' => $position,
                    'name' => $linkedProduct->getName()
                ]
            ];
        } catch (NoSuchEntityException $e) {
            return [
                'success' => false,
                'error' => [
                    'sku' => $linkedSku,
                    'reason' => __('Product does not exist')
                ]
            ];
        }
    }

    /**
     * Prepare success response
     *
     * @param ProductInterface $product
     * @param array $successfulLinks
     * @param array $invalidLinks
     * @param array $duplicateLinks
     * @param array $alreadyLinkedSkus
     * @return array
     */
    private function prepareSuccessResponse(
        ProductInterface $product,
        array $successfulLinks,
        array $invalidLinks,
        array $duplicateLinks,
        array $alreadyLinkedSkus
    ): array {
        // Convert product to array format for GraphQL
        $productData = ['sku' => $product->getSku(), 'name' => $product->getName()];

        return [
            'product' => $productData,
            'success' => true,
            'message' => __('Product links updated successfully.'),
            'successful_links' => $successfulLinks,
            'invalid_links' => $invalidLinks,
            'duplicate_links' => $duplicateLinks,
            'already_linked_skus' => $alreadyLinkedSkus
        ];
    }

    /**
     * Prepare failure response
     *
     * @param ProductInterface $product
     * @param array $invalidLinks
     * @param array $duplicateLinks
     * @param array $alreadyLinkedSkus
     * @return array
     */
    private function prepareFailureResponse(
        ProductInterface $product,
        array $invalidLinks,
        array $duplicateLinks,
        array $alreadyLinkedSkus
    ): array {
        // Format product for GraphQL even when no links are added
        $productData = ['sku' => $product->getSku(), 'name' => $product->getName()];

        return [
            'product' => $productData,
            'success' => false,
            'message' => __('No valid product links to add.'),
            'successful_links' => [],
            'invalid_links' => $invalidLinks,
            'duplicate_links' => $duplicateLinks,
            'already_linked_skus' => $alreadyLinkedSkus
        ];
    }

    /**
     * Get the link type for this resolver
     *
     * @return string
     */
    abstract protected function getLinkType(): string;
}
