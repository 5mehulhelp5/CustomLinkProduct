<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Renga\CustomLinkProduct\Model\Utility;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Api\Data\ProductLinkInterface;
use Magento\Catalog\Api\Data\ProductLinkInterfaceFactory;
use Magento\Catalog\Api\ProductLinkManagementInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\Product\Attribute\Source\Status;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\GraphQl\Exception\GraphQlInputException;

/**
 * Utility class for product link operations
 */
class ProductLinkUtility
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
     * Get and validate product
     *
     * @param string $productSku
     * @return ProductInterface
     * @throws GraphQlInputException
     * @throws NoSuchEntityException
     */
    public function getAndValidateProduct(string $productSku): ProductInterface
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
     * Get existing linked SKUs for a specific link type
     *
     * @param string $productSku
     * @param string $linkType
     * @return array
     */
    public function getExistingLinkedSkus(string $productSku, string $linkType): array
    {
        $existingLinks = $this->productLinkManagement->getLinkedItemsByType($productSku, $linkType);
        $existingSkus = [];

        foreach ($existingLinks as $existingLink) {
            $existingSkus[] = $existingLink->getLinkedProductSku();
        }

        return $existingSkus;
    }

    /**
     * Process a single linked product SKU
     *
     * @param string $productSku
     * @param string $linkedSku
     * @param string $linkType
     * @param int $position
     * @return array
     */
    public function processLinkedProductSku(string $productSku, string $linkedSku, string $linkType, int $position): array
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
            $link->setLinkType($linkType);
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
     * Process linked product SKUs
     *
     * @param string $productSku
     * @param array $linkedProductSkus
     * @param array $existingSkus
     * @param string $linkType
     * @param int $position
     * @return array
     */
    public function processLinkedProductSkus(
        string $productSku,
        array $linkedProductSkus,
        array $existingSkus,
        string $linkType,
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

            $result = $this->processLinkedProductSku($productSku, $linkedSku, $linkType, $position + $index);

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
     * Update product links for a specific link type
     *
     * @param string $productSku
     * @param array $links
     * @param string $linkType
     * @return void
     */
    public function updateProductLinks(string $productSku, array $links, string $linkType): void
    {
        // Set the product links (this will replace existing links of this type)
        $this->productLinkManagement->setProductLinks($productSku, $links);
    }

    /**
     * Prepare success response for individual link type update
     *
     * @param ProductInterface $product
     * @param array $successfulLinks
     * @param array $invalidLinks
     * @param array $duplicateLinks
     * @param array $alreadyLinkedSkus
     * @return array
     */
    public function prepareSuccessResponse(
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
     * Prepare failure response for individual link type update
     *
     * @param ProductInterface $product
     * @param array $invalidLinks
     * @param array $duplicateLinks
     * @param array $alreadyLinkedSkus
     * @return array
     */
    public function prepareFailureResponse(
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
     * Format link type result for combined update
     *
     * @param string $linkType
     * @param array $result
     * @return array
     */
    public function formatLinkTypeResult(string $linkType, array $result): array
    {
        return [
            'link_type' => $linkType,
            'success' => $result['success'],
            'message' => $result['message'],
            'successful_links' => $result['successful_links'] ?? [],
            'invalid_links' => $result['invalid_links'] ?? [],
            'duplicate_links' => $result['duplicate_links'] ?? [],
            'already_linked_skus' => $result['already_linked_skus'] ?? []
        ];
    }

    /**
     * Process links for a specific link type
     *
     * @param string $productSku
     * @param array $linkedProductSkus
     * @param string $linkType
     * @param int $position
     * @return array
     */
    public function processLinks(string $productSku, array $linkedProductSkus, string $linkType, int $position): array
    {
        if (empty($linkedProductSkus)) {
            return [
                'success' => false,
                'message' => __('No product SKUs provided for %1 links.', $linkType),
                'successful_links' => [],
                'invalid_links' => [],
                'duplicate_links' => [],
                'already_linked_skus' => []
            ];
        }

        // Get existing links
        $existingSkus = $this->getExistingLinkedSkus($productSku, $linkType);

        // Process the linked product SKUs
        $result = $this->processLinkedProductSkus($productSku, $linkedProductSkus, $existingSkus, $linkType, $position);

        // Extract results
        $links = $result['links'];
        $successfulLinks = $result['successful_links'];
        $invalidLinks = $result['invalid_links'];
        $duplicateLinks = $result['duplicate_links'];
        $alreadyLinkedSkus = $result['already_linked_skus'];

        // Update links and prepare response
        if (!empty($links)) {
            // Set the product links (this will replace existing links of this type)
            $this->updateProductLinks($productSku, $links, $linkType);

            return [
                'success' => true,
                'message' => __('Product links updated successfully.'),
                'successful_links' => $successfulLinks,
                'invalid_links' => $invalidLinks,
                'duplicate_links' => $duplicateLinks,
                'already_linked_skus' => $alreadyLinkedSkus
            ];
        } else {
            return [
                'success' => false,
                'message' => __('No valid product links to add.'),
                'successful_links' => [],
                'invalid_links' => $invalidLinks,
                'duplicate_links' => $duplicateLinks,
                'already_linked_skus' => $alreadyLinkedSkus
            ];
        }
    }
}
