# Example Request Payloads for Product Link Updates

## Request Examples

### 1. Update Similar Link Products

```graphql
mutation {
  updateSimilarLinkProducts(
    input: {
      product_sku: "24-MB01"
      linked_product_skus: ["24-MB02", "24-MB03", "24-MB04"]
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

### 2. Update Repair Link Products

```graphql
mutation {
  updateRepairLinkProducts(
    input: {
      product_sku: "24-MB01"
      linked_product_skus: ["24-WB03", "24-WB04"]
      position: 1
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

### 3. Update Functional Link Products

```graphql
mutation {
  updateFunctionalLinkProducts(
    input: {
      product_sku: "24-MB01"
      linked_product_skus: ["24-UB02", "24-WB06"]
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

## Input Parameters

- **product_sku** (required): The SKU of the product to which the links will be added
- **linked_product_skus** (required): An array of SKUs to link to the product
- **position** (optional): The position of the linked products. If not specified, products will be added at the end

## Response Examples

### Successful Response

```json
{
  "data": {
    "updateSimilarLinkProducts": {
      "product": {
        "sku": "24-MB01",
        "name": "Joust Duffle Bag"
      },
      "success": true,
      "message": "Product links updated successfully.",
      "successful_links": [
        {
          "sku": "24-MB02",
          "name": "Fusion Backpack",
          "position": 0
        },
        {
          "sku": "24-MB03",
          "name": "Crown Summit Backpack",
          "position": 1
        }
      ],
      "invalid_links": [],
      "duplicate_links": [],
      "already_linked_skus": []
    }
  }
}
```

### Response with Validation Issues

```json
{
  "data": {
    "updateSimilarLinkProducts": {
      "product": {
        "sku": "24-MB01",
        "name": "Joust Duffle Bag"
      },
      "success": true,
      "message": "Product links updated successfully.",
      "successful_links": [
        {
          "sku": "24-MB02",
          "name": "Fusion Backpack",
          "position": 0
        }
      ],
      "invalid_links": [
        {
          "sku": "invalid-sku",
          "reason": "Product does not exist"
        },
        {
          "sku": "disabled-sku",
          "reason": "Product is disabled"
        }
      ],
      "duplicate_links": [
        "24-MB02"
      ],
      "already_linked_skus": [
        "24-MB03"
      ]
    }
  }
}
```

### Failed Response (No Valid Links)

```json
{
  "data": {
    "updateSimilarLinkProducts": {
      "product": {
        "sku": "24-MB01",
        "name": "Joust Duffle Bag"
      },
      "success": false,
      "message": "No valid product links to add.",
      "successful_links": [],
      "invalid_links": [
        {
          "sku": "invalid-sku-1",
          "reason": "Product does not exist"
        },
        {
          "sku": "invalid-sku-2",
          "reason": "Product does not exist"
        }
      ],
      "duplicate_links": [],
      "already_linked_skus": []
    }
  }
}
```
