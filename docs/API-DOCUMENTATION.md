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

The module provides three mutations for updating different types of product links:

1. `updateSimilarLinkProducts` - Update similar products links
2. `updateRepairLinkProducts` - Update repair parts products links
3. `updateFunctionalLinkProducts` - Update functional equivalent products links

All mutations follow the same pattern and accept the same input format.

## Input Format

All mutations use the same input format:

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

## Response Format

All mutations return the same output format:

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
