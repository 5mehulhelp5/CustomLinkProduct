<?php
/**
 * Test script for updating similar link products via GraphQL with enhanced validation
 *
 * This script tests the updateSimilarLinkProducts GraphQL mutation with various scenarios.
 *
 * SETUP INSTRUCTIONS:
 * ------------------
 * 1. Configure the GraphQL endpoint URL below to match your Magento installation
 * 2. Set the authentication token if your GraphQL endpoint requires it
 * 3. Run the script: php test-update-similar-products.php
 *
 * TROUBLESHOOTING:
 * ---------------
 * - If you get a 404 error, check that the GraphQL endpoint URL is correct
 * - If you get authentication errors, verify your auth token is valid
 * - If you get "SKU not found" errors, update the test cases with valid SKUs from your catalog
 */

// Configuration
// IMPORTANT: Update this URL to match your Magento installation's GraphQL endpoint
// Examples:
// - Local development: 'http://magento.test/graphql'
// - Docker: 'http://localhost:8080/graphql'
// - Production: 'https://your-store.com/graphql'
$graphqlEndpoint = 'http://magento.test/graphql';

// Your Magento admin or customer token for authentication
// Leave empty if your endpoint doesn't require authentication
$authToken = '';

// Enable debug mode to see detailed response information
$debug = true;

// GraphQL mutation with all response fields
$query = <<<'GRAPHQL'
mutation UpdateSimilarLinkProducts($input: UpdateProductLinksInput!) {
  updateSimilarLinkProducts(input: $input) {
    product {
      sku
      name
    }
    success
    message
    successful_links {
      sku
      name
      position
    }
    invalid_links {
      sku
      reason
    }
    duplicate_links
    already_linked_skus
  }
}
GRAPHQL;

// Test cases
$testCases = [
    'valid_skus' => [
        'description' => 'Test with valid product SKUs',
        'input' => [
            'product_sku' => '24-MB01',  // Update with an actual product SKU
            'linked_product_skus' => ['24-MB02', '24-MB03'],  // Update with actual product SKUs
            'position' => 0
        ]
    ],
    'invalid_skus' => [
        'description' => 'Test with invalid product SKUs',
        'input' => [
            'product_sku' => '24-MB01',  // Update with an actual product SKU
            'linked_product_skus' => ['invalid-sku-1', 'invalid-sku-2', '24-MB02'],  // Mix of invalid and valid SKUs
            'position' => 0
        ]
    ],
    'duplicate_skus' => [
        'description' => 'Test with duplicate SKUs in input',
        'input' => [
            'product_sku' => '24-MB01',  // Update with an actual product SKU
            'linked_product_skus' => ['24-MB02', '24-MB02', '24-MB03', '24-MB03'],  // Duplicate SKUs
            'position' => 0
        ]
    ],
    'mixed_scenario' => [
        'description' => 'Test with mixed scenario (valid, invalid, duplicate SKUs)',
        'input' => [
            'product_sku' => '24-MB01',  // Update with an actual product SKU
            'linked_product_skus' => ['24-MB02', 'invalid-sku', '24-MB02', '24-MB03'],  // Mixed scenario
            'position' => 0
        ]
    ]
];

// Run test cases
foreach ($testCases as $testName => $testCase) {
    echo "Running test: {$testCase['description']}" . PHP_EOL;
    echo "---------------------------------------------" . PHP_EOL;

    // Prepare the request
    $data = [
        'query' => $query,
        'variables' => [
            'input' => $testCase['input']
        ]
    ];

    // Initialize cURL
    $ch = curl_init($graphqlEndpoint);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

    // Set headers
    $headers = ['Content-Type: application/json'];

    // Add authorization header if token is provided
    if (!empty($authToken)) {
        $headers[] = 'Authorization: Bearer ' . $authToken;
    }

    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

    // Execute the request
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    curl_close($ch);

    // Output the results
    if ($error) {
        echo "cURL Error: " . $error . PHP_EOL;
    } else {
        // Debug output
        if ($debug) {
            echo "HTTP Status Code: " . $httpCode . PHP_EOL;
            echo "Raw Response:" . PHP_EOL;
            echo $response . PHP_EOL;
            echo "---------------------------------------------" . PHP_EOL;
        }

        $result = json_decode($response, true);

        // Check if json_decode was successful
        if ($result === null && json_last_error() !== JSON_ERROR_NONE) {
            echo "Error decoding JSON response: " . json_last_error_msg() . PHP_EOL;
            continue;
        }

        // Check for GraphQL errors
        if (isset($result['errors'])) {
            echo "GraphQL Errors:" . PHP_EOL;
            foreach ($result['errors'] as $error) {
                echo "- " . $error['message'] . PHP_EOL;
                if (isset($error['extensions']['debugMessage'])) {
                    echo "  Debug: " . $error['extensions']['debugMessage'] . PHP_EOL;
                }
            }
        } else {
            // Check if data and updateSimilarLinkProducts exist in the response
            if (!isset($result['data']) || !isset($result['data']['updateSimilarLinkProducts'])) {
                echo "Error: Expected 'data.updateSimilarLinkProducts' not found in response" . PHP_EOL;
                continue;
            }

            // Display test results
            $data = $result['data']['updateSimilarLinkProducts'];

            // Check if success and message exist
            if (isset($data['success'])) {
                echo "Success: " . ($data['success'] ? 'Yes' : 'No') . PHP_EOL;
            } else {
                echo "Success: Not provided in response" . PHP_EOL;
            }

            if (isset($data['message'])) {
                echo "Message: " . $data['message'] . PHP_EOL;
            } else {
                echo "Message: Not provided in response" . PHP_EOL;
            }

            // Check if successful_links exists and is an array
            if (isset($data['successful_links']) && is_array($data['successful_links'])) {
                echo "Successful Links: " . count($data['successful_links']) . PHP_EOL;
                foreach ($data['successful_links'] as $link) {
                    echo "  - SKU: " . (isset($link['sku']) ? $link['sku'] : 'N/A');
                    echo ", Name: " . (isset($link['name']) ? $link['name'] : 'N/A');
                    echo ", Position: " . (isset($link['position']) ? $link['position'] : 'N/A') . PHP_EOL;
                }
            } else {
                echo "Successful Links: Not provided in response or not an array" . PHP_EOL;
            }

            // Check if invalid_links exists and is an array
            if (isset($data['invalid_links']) && is_array($data['invalid_links'])) {
                echo "Invalid Links: " . count($data['invalid_links']) . PHP_EOL;
                foreach ($data['invalid_links'] as $link) {
                    echo "  - SKU: " . (isset($link['sku']) ? $link['sku'] : 'N/A');
                    echo ", Reason: " . (isset($link['reason']) ? $link['reason'] : 'N/A') . PHP_EOL;
                }
            } else {
                echo "Invalid Links: Not provided in response or not an array" . PHP_EOL;
            }

            // Check if duplicate_links exists and is an array
            if (isset($data['duplicate_links']) && is_array($data['duplicate_links'])) {
                echo "Duplicate Links: " . count($data['duplicate_links']) . PHP_EOL;
                foreach ($data['duplicate_links'] as $sku) {
                    echo "  - SKU: " . $sku . PHP_EOL;
                }
            } else {
                echo "Duplicate Links: Not provided in response or not an array" . PHP_EOL;
            }

            // Check if already_linked_skus exists and is an array
            if (isset($data['already_linked_skus']) && is_array($data['already_linked_skus'])) {
                echo "Already Linked SKUs: " . count($data['already_linked_skus']) . PHP_EOL;
                foreach ($data['already_linked_skus'] as $sku) {
                    echo "  - SKU: " . $sku . PHP_EOL;
                }
            } else {
                echo "Already Linked SKUs: Not provided in response or not an array" . PHP_EOL;
            }

            // Check if product exists and has similar_link_products
            if (isset($data['product']) && is_array($data['product'])) {
                echo "Product Information:" . PHP_EOL;
                echo "  - SKU: " . (isset($data['product']['sku']) ? $data['product']['sku'] : 'N/A') . PHP_EOL;
                echo "  - Name: " . (isset($data['product']['name']) ? $data['product']['name'] : 'N/A') . PHP_EOL;
            } else {
                echo "Product Information: Not provided in response or not an array" . PHP_EOL;
            }
        }
    }

    echo PHP_EOL;
}
