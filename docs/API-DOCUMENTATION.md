# Renga CustomLinkProduct GraphQL API Documentation

This document provides comprehensive documentation for using the GraphQL API to manage custom product link types in Magento 2:
- Similar Link Products
- Repair Link Products
- Functional Link Products

## Table of Contents

- [Query API](#query-api)
- [Mutation API](#mutation-api)
- [Input Format](#input-format)
- [Response Format](#response-format)
- [Examples](#examples)
- [Error Handling](#error-handling)
- [Important Notes](#important-notes)
- [Additional Resources](#additional-resources)

## Query API

The module extends the `ProductInterface` with the following fields:

```graphql
interface ProductInterface {
    similar_link_products: [ProductInterface]
        @doc(description: "An array of products to be displayed in a Similar Products block.")
        @resolver(class: "Renga\\CustomLinkProduct\\Model\\Resolver\\Batch\\SimilarProducts")
    repair_link_products: [ProductInterface]
        @doc(description: "An array of products to be displayed in a Repair Parts Products block.")
        @resolver(class: "Renga\\CustomLinkProduct\\Model\\Resolver\\Batch\\RepairProducts")
    functional_link_products: [ProductInterface]
        @doc(description: "An array of products to be displayed in a Functional Equivalents Products block.")
        @resolver(class: "Renga\\CustomLinkProduct\\Model\\Resolver\\Batch\\FunctionalProducts")
}
```

These fields can be accessed through Magento's standard product queries (`products`, `product`). Each field returns an array of products that implement the `ProductInterface`, allowing you to request any standard product fields.

### Basic Example Query

```graphql
query {
  products(filter: { sku: { eq: "24-MB01" } }) {
    items {
      sku
      name
      similar_link_products {
        sku
        name
      }
      repair_link_products {
        sku
        name
      }
      functional_link_products {
        sku
        name
      }
    }
  }
}
```

### Query Capabilities

You can query these custom link types in various ways:

1. **Individual Link Types**: Query each type separately
2. **Additional Fields**: Include any standard product fields in your queries
3. **Filtering**: Filter products before retrieving their linked products
4. **Pagination**: Use pagination for handling large result sets
5. **Variables**: Use variables to make your queries more flexible

For comprehensive examples of all these query patterns, including cURL examples and expected responses, see the [Query Examples](query-examples.md) documentation.

## Mutation API

The module provides four mutations for updating different types of product links:

1. `updateSimilarLinkProducts` - Update similar products links
2. `updateRepairLinkProducts` - Update repair parts products links
3. `updateFunctionalLinkProducts` - Update functional equivalent products links
4. `updateAllLinkProducts` - Update all three types of product links in a single request

The first three mutations follow the same pattern and accept the same input format. The fourth mutation (`updateAllLinkProducts`) uses a different input format that allows specifying links for all three types in a single request.

## Input Format

### Individual Link Type Updates

The first three mutations (`updateSimilarLinkProducts`, `updateRepairLinkProducts`, `updateFunctionalLinkProducts`) use the same input format:

```graphql
input UpdateProductLinksInput {
    product_sku: String!
    linked_product_skus: [String!]!
    position: Int
}
```

| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| product_sku | String | Yes | The SKU of the product to which the links will be added |
| linked_product_skus | [String!]! | Yes | An array of SKUs to link to the product |
| position | Int | No | The position of the linked products. If not specified, products will be added at the end |

### Unified Link Type Update

The `updateAllLinkProducts` mutation uses a different input format that allows specifying links for all three types in a single request:

```graphql
input UpdateAllProductLinksInput {
    product_sku: String!
    similar_product_skus: [String!]
    repair_product_skus: [String!]
    functional_product_skus: [String!]
    position: Int
}
```

| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| product_sku | String | Yes | The SKU of the product to which the links will be added |
| similar_product_skus | [String!] | No | An array of SKUs to link as similar products |
| repair_product_skus | [String!] | No | An array of SKUs to link as repair products |
| functional_product_skus | [String!] | No | An array of SKUs to link as functional products |
| position | Int | No | The starting position of the linked products. If not specified, products will be added at the end |

Note: For the unified update, at least one of `similar_product_skus`, `repair_product_skus`, or `functional_product_skus` must be provided.

## Response Format

### Individual Link Type Updates

The first three mutations return the same output format:

```graphql
type UpdateProductLinksOutput {
    product: ProductInfo
    success: Boolean!
    message: String
    successful_links: [ProductLinkInfo]
    invalid_links: [InvalidProductLinkInfo]
    duplicate_links: [String]
    already_linked_skus: [String]
}
```

| Field | Type | Description |
|-------|------|-------------|
| product | ProductInfo | The product after updating the links, including only its SKU and name |
| success | Boolean | Indicates whether the update was successful |
| message | String | A message describing the result of the update operation |
| successful_links | [ProductLinkInfo] | An array of successfully added product links with details |
| invalid_links | [InvalidProductLinkInfo] | An array of invalid product links with reasons |
| duplicate_links | [String] | An array of duplicate SKUs found in the input |
| already_linked_skus | [String] | An array of SKUs that were already linked to the product |

### Unified Link Type Update

The `updateAllLinkProducts` mutation returns a different output format that includes results for all three link types:

```graphql
type UpdateAllProductLinksOutput {
    product: ProductInfo
    success: Boolean!
    message: String
    similar_links_result: LinkTypeResult
    repair_links_result: LinkTypeResult
    functional_links_result: LinkTypeResult
}

type LinkTypeResult {
    link_type: String!
    success: Boolean!
    message: String
    successful_links: [ProductLinkInfo]
    invalid_links: [InvalidProductLinkInfo]
    duplicate_links: [String]
    already_linked_skus: [String]
}
```

| Field | Type | Description |
|-------|------|-------------|
| product | ProductInfo | The product after updating the links, including only its SKU and name |
| success | Boolean | Indicates whether the overall update was successful |
| message | String | A message describing the overall result of the update operation |
| similar_links_result | LinkTypeResult | The result of updating similar link products |
| repair_links_result | LinkTypeResult | The result of updating repair link products |
| functional_links_result | LinkTypeResult | The result of updating functional link products |

Each `LinkTypeResult` contains:

| Field | Type | Description |
|-------|------|-------------|
| link_type | String | The type of link (similar, repair, or functional) |
| success | Boolean | Indicates whether the update for this link type was successful |
| message | String | A message describing the result of the update operation for this link type |
| successful_links | [ProductLinkInfo] | An array of successfully added product links with details |
| invalid_links | [InvalidProductLinkInfo] | An array of invalid product links with reasons |
| duplicate_links | [String] | An array of duplicate SKUs found in the input |
| already_linked_skus | [String] | An array of SKUs that were already linked to the product |

## Examples

### Update Similar Link Products

```graphql
mutation {
  updateSimilarLinkProducts(
    input: {
      product_sku: "24-MB01"
      linked_product_skus: ["24-MB02", "24-MB03"]
      position: 0
    }
  ) {
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
```

### Update Repair Link Products

```graphql
mutation {
  updateRepairLinkProducts(
    input: {
      product_sku: "24-MB01"
      linked_product_skus: ["24-MB04", "24-MB05"]
      position: 0
    }
  ) {
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
```

### Update Functional Link Products

```graphql
mutation {
  updateFunctionalLinkProducts(
    input: {
      product_sku: "24-MB01"
      linked_product_skus: ["24-MB06", "24-MB07"]
      position: 0
    }
  ) {
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
```

### Update All Link Types in a Single Request

```graphql
mutation {
  updateAllLinkProducts(
    input: {
      product_sku: "24-MB01"
      similar_product_skus: ["24-MB02", "24-MB03"]
      repair_product_skus: ["24-WB03", "24-WB04"]
      functional_product_skus: ["24-UB02", "24-WB06"]
      position: 0
    }
  ) {
    product {
      sku
      name
    }
    success
    message
    similar_links_result {
      link_type
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
    repair_links_result {
      link_type
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
    functional_links_result {
      link_type
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
}
```

## Error Handling

The API will return appropriate error messages in the following cases:
- If the product SKU does not exist
- If any of the linked product SKUs do not exist
- If there is an issue with the input parameters
- If there is an error during the update process

Example error response:

```json
{
  "errors": [
    {
      "message": "The product with SKU \"invalid-sku\" does not exist.",
      "extensions": {
        "category": "graphql-no-such-entity"
      }
    }
  ]
}
```

## Important Notes

1. These mutations replace all existing links of the specified type with the new links provided in the input.
2. The position parameter determines the starting position for the first linked product. Subsequent products will have incrementing positions.
3. These mutations require admin permissions to execute.
4. If any of the provided SKUs do not exist, the mutation will fail with an appropriate error message.
5. The examples use sample SKUs from a typical Magento installation. Replace them with actual SKUs from your catalog.

## Additional Resources

For more detailed examples, see:
- [Example GraphQL Payloads](example-payloads.md) - Contains detailed GraphQL mutation examples and response examples for different scenarios
- [Example cURL Requests](curl-examples.md) - Contains cURL request examples and expected responses
