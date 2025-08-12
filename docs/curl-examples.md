# Example cURL Requests for Product Link Updates

## 1. Update Similar Link Products

### Request

```bash
curl -X POST \
  https://your-magento-instance.com/graphql \
  -H 'Content-Type: application/json' \
  -H 'Authorization: Bearer YOUR_ADMIN_TOKEN' \
  -d '{
    "query": "mutation { updateSimilarLinkProducts(input: { product_sku: \"24-MB01\", linked_product_skus: [\"24-MB02\", \"24-MB03\", \"24-MB04\"], position: 0 }) { product { sku name } success message successful_links { sku name position } invalid_links { sku reason } duplicate_links already_linked_skus } }"
}'
```

### Response

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
        },
        {
          "sku": "24-MB04",
          "name": "Wayfarer Messenger Bag",
          "position": 2
        }
      ],
      "invalid_links": [],
      "duplicate_links": [],
      "already_linked_skus": []
    }
  }
}
```

## 2. Update Repair Link Products

### Request

```bash
curl -X POST \
  https://your-magento-instance.com/graphql \
  -H 'Content-Type: application/json' \
  -H 'Authorization: Bearer YOUR_ADMIN_TOKEN' \
  -d '{
    "query": "mutation { updateRepairLinkProducts(input: { product_sku: \"24-MB01\", linked_product_skus: [\"24-WB03\", \"24-WB04\"], position: 1 }) { product { sku name } success message successful_links { sku name position } invalid_links { sku reason } duplicate_links already_linked_skus } }"
}'
```

### Response

```json
{
  "data": {
    "updateRepairLinkProducts": {
      "product": {
        "sku": "24-MB01",
        "name": "Joust Duffle Bag"
      },
      "success": true,
      "message": "Product links updated successfully.",
      "successful_links": [
        {
          "sku": "24-WB03",
          "name": "Bolo Sport Watch",
          "position": 1
        },
        {
          "sku": "24-WB04",
          "name": "Luma Analog Watch",
          "position": 2
        }
      ],
      "invalid_links": [],
      "duplicate_links": [],
      "already_linked_skus": []
    }
  }
}
```

## 3. Update Functional Link Products

### Request

```bash
curl -X POST \
  https://your-magento-instance.com/graphql \
  -H 'Content-Type: application/json' \
  -H 'Authorization: Bearer YOUR_ADMIN_TOKEN' \
  -d '{
    "query": "mutation { updateFunctionalLinkProducts(input: { product_sku: \"24-MB01\", linked_product_skus: [\"24-UB02\", \"24-WB06\"] }) { product { sku name } success message successful_links { sku name position } invalid_links { sku reason } duplicate_links already_linked_skus } }"
}'
```

### Response

```json
{
  "data": {
    "updateFunctionalLinkProducts": {
      "product": {
        "sku": "24-MB01",
        "name": "Joust Duffle Bag"
      },
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
```

## 4. Update All Link Types in a Single Request

### Request

```bash
curl -X POST \
  https://your-magento-instance.com/graphql \
  -H 'Content-Type: application/json' \
  -H 'Authorization: Bearer jiqwcd2woaa1pifmwxe8hfiwykzzo6yv' \
  -d '{
    "query": "mutation { updateAllLinkProducts(input: { product_sku: \"24-MB01\", similar_product_skus: [\"24-MB02\", \"24-MB03\"], repair_product_skus: [\"24-WB03\", \"24-WB04\"], functional_product_skus: [\"24-UB02\", \"24-WB06\"], position: 0 }) { product { sku name } success message similar_links_result { link_type success message successful_links { sku name position } invalid_links { sku reason } duplicate_links already_linked_skus } repair_links_result { link_type success message successful_links { sku name position } invalid_links { sku reason } duplicate_links already_linked_skus } functional_links_result { link_type success message successful_links { sku name position } invalid_links { sku reason } duplicate_links already_linked_skus } } }"
}'
```

### Response

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

## Notes

1. Replace `https://your-magento-instance.com/graphql` with your actual Magento GraphQL endpoint.
2. Replace `YOUR_ADMIN_TOKEN` with a valid admin token. You can obtain this by authenticating with the Magento API.
3. These mutations require admin permissions to execute.
4. The examples use sample SKUs from a typical Magento installation. Replace them with actual SKUs from your catalog.
5. The mutations replace all existing links of the specified type with the new links provided in the input.
