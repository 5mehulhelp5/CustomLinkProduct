# GraphQL Query Examples for Custom Link Products

This document provides comprehensive examples for querying custom product link types using GraphQL. The Renga_CustomLinkProduct module extends Magento's ProductInterface with three custom link types:

- `similar_link_products`: Products that are similar to the current product
- `repair_link_products`: Products that can be used to repair the current product
- `functional_link_products`: Products that are functionally equivalent to the current product

## Table of Contents

- [Basic Queries](#basic-queries)
- [Individual Link Type Queries](#individual-link-type-queries)
- [Advanced Queries](#advanced-queries)
- [Pagination Examples](#pagination-examples)
- [Using Variables](#using-variables)
- [cURL Examples](#curl-examples)
- [Response Examples](#response-examples)

## Basic Queries

### Query All Link Types for a Specific Product

This query retrieves a product by SKU and includes all three types of linked products:

```graphql
query GetProductWithAllLinkTypes {
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

## Individual Link Type Queries

### Query Similar Link Products

```graphql
query GetProductWithSimilarLinks {
  products(filter: { sku: { eq: "24-MB01" } }) {
    items {
      sku
      name
      similar_link_products {
        sku
        name
        price_range {
          minimum_price {
            regular_price {
              value
              currency
            }
          }
        }
      }
    }
  }
}
```

### Query Repair Link Products

```graphql
query GetProductWithRepairLinks {
  products(filter: { sku: { eq: "24-MB01" } }) {
    items {
      sku
      name
      repair_link_products {
        sku
        name
        price_range {
          minimum_price {
            regular_price {
              value
              currency
            }
          }
        }
      }
    }
  }
}
```

### Query Functional Link Products

```graphql
query GetProductWithFunctionalLinks {
  products(filter: { sku: { eq: "24-MB01" } }) {
    items {
      sku
      name
      functional_link_products {
        sku
        name
        price_range {
          minimum_price {
            regular_price {
              value
              currency
            }
          }
        }
      }
    }
  }
}
```

## Advanced Queries

### Query with Additional Product Fields

This example shows how to retrieve more detailed information about linked products:

```graphql
query GetDetailedProductLinks {
  products(filter: { sku: { eq: "24-MB01" } }) {
    items {
      sku
      name
      similar_link_products {
        sku
        name
        description {
          html
        }
        price_range {
          minimum_price {
            regular_price {
              value
              currency
            }
            discount {
              amount_off
              percent_off
            }
            final_price {
              value
              currency
            }
          }
        }
        image {
          url
          label
        }
        url_key
      }
    }
  }
}
```

### Query with Category Information

This example shows how to include category information for linked products:

```graphql
query GetProductLinksWithCategories {
  products(filter: { sku: { eq: "24-MB01" } }) {
    items {
      sku
      name
      similar_link_products {
        sku
        name
        categories {
          id
          name
          url_path
        }
      }
    }
  }
}
```

### Query with Stock Information

This example shows how to include stock information for linked products:

```graphql
query GetProductLinksWithStock {
  products(filter: { sku: { eq: "24-MB01" } }) {
    items {
      sku
      name
      similar_link_products {
        sku
        name
        only_x_left_in_stock
        stock_status
      }
    }
  }
}
```

## Pagination Examples

### Paginating Through Products

When dealing with a large number of products, you can use pagination:

```graphql
query GetPaginatedProducts {
  products(
    filter: { category_id: { eq: "5" } }
    pageSize: 10
    currentPage: 1
  ) {
    total_count
    page_info {
      page_size
      current_page
      total_pages
    }
    items {
      sku
      name
      similar_link_products {
        sku
        name
      }
    }
  }
}
```

## Using Variables

### Query with Variables

Using variables makes your queries more flexible and reusable:

```graphql
query GetProductWithLinks($sku: String!) {
  products(filter: { sku: { eq: $sku } }) {
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

Variables JSON:

```json
{
  "sku": "24-MB01"
}
```

## cURL Examples

### Basic cURL Request

```bash
curl -X POST \
  https://your-magento-instance.com/graphql \
  -H 'Content-Type: application/json' \
  -d '{
    "query": "query { products(filter: { sku: { eq: \"24-MB01\" } }) { items { sku name similar_link_products { sku name } } } }"
  }'
```

### cURL Request with Variables

```bash
curl -X POST \
  https://your-magento-instance.com/graphql \
  -H 'Content-Type: application/json' \
  -d '{
    "query": "query GetProductWithLinks($sku: String!) { products(filter: { sku: { eq: $sku } }) { items { sku name similar_link_products { sku name } } } }",
    "variables": {
      "sku": "24-MB01"
    }
  }'
```

### cURL Request with Authentication

For queries that require authentication:

```bash
curl -X POST \
  https://your-magento-instance.com/graphql \
  -H 'Content-Type: application/json' \
  -H 'Authorization: Bearer YOUR_CUSTOMER_TOKEN' \
  -d '{
    "query": "query { products(filter: { sku: { eq: \"24-MB01\" } }) { items { sku name similar_link_products { sku name } } } }"
  }'
```

## Response Examples

### Example Response for Basic Query

```json
{
  "data": {
    "products": {
      "items": [
        {
          "sku": "24-MB01",
          "name": "Joust Duffle Bag",
          "similar_link_products": [
            {
              "sku": "24-MB02",
              "name": "Fusion Backpack"
            },
            {
              "sku": "24-MB03",
              "name": "Crown Summit Backpack"
            }
          ],
          "repair_link_products": [
            {
              "sku": "24-WB03",
              "name": "Bolo Sport Watch"
            }
          ],
          "functional_link_products": [
            {
              "sku": "24-UB02",
              "name": "Impulse Duffle"
            }
          ]
        }
      ]
    }
  }
}
```

### Example Response with No Linked Products

```json
{
  "data": {
    "products": {
      "items": [
        {
          "sku": "24-MB01",
          "name": "Joust Duffle Bag",
          "similar_link_products": [],
          "repair_link_products": [],
          "functional_link_products": []
        }
      ]
    }
  }
}
```

## Notes

1. Replace `https://your-magento-instance.com/graphql` with your actual Magento GraphQL endpoint.
2. Replace `YOUR_CUSTOMER_TOKEN` with a valid customer token if authentication is required.
3. The examples use sample SKUs from a typical Magento installation. Replace them with actual SKUs from your catalog.
4. The availability of certain fields (like `only_x_left_in_stock`) depends on your Magento configuration and installed modules.
5. For large catalogs, always use pagination to improve performance.
