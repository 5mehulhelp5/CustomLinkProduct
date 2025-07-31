# Renga CustomLinkProduct Module

## Overview

The Renga CustomLinkProduct module extends Magento 2 with custom product link types and GraphQL APIs to manage them:

- **Similar Link Products**: Products that are similar to the current product
- **Repair Link Products**: Products that can be used to repair the current product
- **Functional Link Products**: Products that are functionally equivalent to the current product

## Installation

### Composer Installation

```bash
composer require renga/module-custom-link-product
```

### Manual Installation

1. Create the following directory structure in your Magento installation:
   ```
   app/code/Renga/CustomLinkProduct
   ```

2. Download the module code and place it in the directory

3. Enable the module:
   ```bash
   bin/magento module:enable Renga_CustomLinkProduct
   bin/magento setup:upgrade
   bin/magento setup:di:compile
   bin/magento cache:clean
   ```

## Features

- Adds three custom product link types to Magento 2
- Provides GraphQL APIs for querying and updating product links
- Includes validation for product links
- Supports detailed error reporting

## Usage

The module provides both Query and Mutation GraphQL APIs:

### Query API

Retrieve linked products of specific types:

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

The module supports various query patterns including filtering, pagination, and retrieving detailed product information. See the [Query Examples](docs/query-examples.md) documentation for comprehensive examples.

### Mutation API

Update product links of specific types:

```graphql
mutation {
  updateSimilarLinkProducts(
    input: {
      product_sku: "24-MB01"
      linked_product_skus: ["24-MB02", "24-MB03"]
      position: 0
    }
  ) {
    success
    message
  }
}
```

## Documentation

For detailed API documentation, please refer to the following resources in the `docs` directory:

- [API Documentation](docs/API-DOCUMENTATION.md) - Comprehensive API documentation
- [Query Examples](docs/query-examples.md) - Detailed GraphQL query examples
- [Example GraphQL Payloads](docs/example-payloads.md) - Detailed GraphQL mutation examples
- [Example cURL Requests](docs/curl-examples.md) - cURL request examples

## License

[OSL-3.0](https://opensource.org/licenses/OSL-3.0)
