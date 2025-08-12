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

### 4. Update All Link Types in a Single Request

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

## Input Parameters

### For Individual Link Type Updates (updateSimilarLinkProducts, updateRepairLinkProducts, updateFunctionalLinkProducts)

- **product_sku** (required): The SKU of the product to which the links will be added
- **linked_product_skus** (required): An array of SKUs to link to the product
- **position** (optional): The position of the linked products. If not specified, products will be added at the end

### For Unified Link Type Update (updateAllLinkProducts)

- **product_sku** (required): The SKU of the product to which the links will be added
- **similar_product_skus** (optional): An array of SKUs to link as similar products
- **repair_product_skus** (optional): An array of SKUs to link as repair products
- **functional_product_skus** (optional): An array of SKUs to link as functional products
- **position** (optional): The starting position of the linked products. If not specified, products will be added at the end

Note: For the unified update, at least one of similar_product_skus, repair_product_skus, or functional_product_skus must be provided.

## Response Examples

### Successful Response for Individual Update

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

### Successful Response for Unified Update

```json
{
  "data": {
    "updateAllLinkProducts": {
      "product": {
        "sku": "24-MB01",
        "name": "Joust Duffle Bag"
      },
      "success": true,
      "message": "Product links updated successfully.",
      "similar_links_result": {
        "link_type": "similar",
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
      },
      "repair_links_result": {
        "link_type": "repair",
        "success": true,
        "message": "Product links updated successfully.",
        "successful_links": [
          {
            "sku": "24-WB03",
            "name": "Bolo Sport Watch",
            "position": 0
          },
          {
            "sku": "24-WB04",
            "name": "Luma Analog Watch",
            "position": 1
          }
        ],
        "invalid_links": [],
        "duplicate_links": [],
        "already_linked_skus": []
      },
      "functional_links_result": {
        "link_type": "functional",
        "success": true,
        "message": "Product links updated successfully.",
        "successful_links": [
          {
            "sku": "24-UB02",
            "name": "Impulse Duffle",
            "position": 0
          },
          {
            "sku": "24-WB06",
            "name": "Aim Analog Watch",
            "position": 1
          }
        ],
        "invalid_links": [],
        "duplicate_links": [],
        "already_linked_skus": []
      }
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
