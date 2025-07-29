# CustomLinkProduct

**Custom Link Product** adds support for multiple custom product relations in Magento 2, with GraphQL compatibility.

---

## Features

* Supports importing **custom link products** via Magento's CSV Import.
* Provides GraphQL fields to retrieve custom-linked products (similar to related, up-sell, and cross-sell products).
* Handles backend logic for linking products.
  *(Frontend/PWA implementation must be done separately or via GraphQL.)*

---

## PHP Compatibility

This module is compatible with:

* **PHP 7.4**
* **PHP 8.1**
* **PHP 8.2**

> PHP 8.2 deprecates dynamic properties. Ensure all class properties are explicitly declared to avoid warnings.

---

## Importing Custom Link Products

To import custom link products using Magento's Import CSV:

1. Add a new column `customlink_skus` in your CSV.
2. Enter linked product SKUs separated by commas.
   Example:

   ```
   sku,name,customlink_skus
   24-WB06,"Product Name","sku-123,sku-456"
   ```

---

## GraphQL Usage

Retrieve custom-linked products the same way as related/up-sell/cross-sell products:

**Magento DevDocs Reference:**
[Products Query](https://devdocs.magento.com/guides/v2.4/graphql/queries/products.html#retrieve-related-products-up-sells-and-cross-sells)

### Example Query

```graphql
{
  products(filter: { sku: { eq: "24-WB06" } }) {
    items {
      uid
      name
      related_products {
        uid
        name
      }
      upsell_products {
        uid
        name
      }
      crosssell_products {
        uid
        name
      }
      similar_link_products {
        uid
        name
      }
      repair_link_products {
        uid
        name
      }
      functional_link_products {
        uid
        name
      }
    }
  }
}
```

---

## PHP Usage

```php
public function __construct(
    \Renga\CustomLinkProduct\Model\SimilarProduct $similarlinkproduct
) {
    $this->similarlinkproduct = $similarlinkproduct;
}

$product = $currentProduct;

// Returns product collection
$similarLinkItems = $this->similarlinkproduct->getSimilarLinkProducts($product);

// Returns product IDs
$similarLinkItemIds = $this->similarlinkproduct->getSimilarLinkProductIds($product);
```

---

## Installation

### 1. Composer (Recommended)

```bash
composer require renga/custom-link-type-product
```

### 2. Manual Installation

1. Download the extension.
2. Unzip the file.
3. Create the folder:

   ```
   {Magento_Root}/app/code/Renga/CustomLinkProduct
   ```
4. Copy the unzipped content to that folder.

---

## 3. Enable & Deploy

From your Magento root directory:

```bash
php bin/magento module:enable Renga_CustomLinkProduct
php bin/magento setup:upgrade
php bin/magento setup:di:compile
php bin/magento setup:static-content:deploy -f
php bin/magento cache:clean
php bin/magento cache:flush
```

---

## License

This module is licensed under proprietary license.

---

## Author

Developed by Rengaraj.

---

## Dependencies

This module requires:
* Magento Framework (~104.0.0 or ~105.0.0)
* Magento Catalog Module (~104.0.0 or ~105.0.0)
* Magento Store Module (~101.0.0 or ~102.0.0)

---
