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
- Supports both individual link type updates and a unified update for all link types
- Includes validation for product links
- Supports detailed error reporting
- Displays linked products on the product detail page using responsive carousels
- Supports customization of carousel appearance and behavior

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

Or update all link types in a single request:

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
    success
    message
  }
}
```

## Frontend Display

The module adds three carousel sections (sliders) to the product detail page, displaying the linked products:

1. **Similar Products** - Products that are similar to the current product
2. **Repair Parts** - Products that can be used to repair the current product
3. **Functional Equivalents** - Products that are functionally equivalent to the current product

### Technical Implementation

The carousels are implemented using:

- **Slick Carousel Library** - A responsive, feature-rich carousel solution
- **Magento Block System** - Custom block classes for each carousel type
- **Responsive Design** - Adapts to different screen sizes automatically
- **RequireJS** - For JavaScript component loading and dependency management

### Features

Each carousel includes the following features:

- **Responsive Design** - Shows 4 products on large screens, 3 on medium screens, 2 on small screens, and 1 on mobile
- **Autoplay** - Automatically scrolls through products every 3 seconds
- **Navigation Arrows** - Allows manual navigation through the carousel
- **Infinite Loop** - Continuous scrolling through the products
- **Smooth Animation** - 300ms transition speed between slides
- **Add to Cart** - Direct "Add to Cart" functionality for each product
- **Product Information** - Displays product image, name, price, and stock status

### Product Display

Each product in the carousel displays:

- Product image (optimized for the carousel)
- Product name (with link to product page)
- Product price
- Add to Cart button (or stock status if not saleable)

### Customization

You can customize the appearance and behavior of the carousels in several ways:

#### Layout XML

The carousels are added to the product detail page using layout XML. You can modify the `catalog_product_view.xml` file to:

- Change the position of the carousels
- Modify the title of each carousel
- Remove specific carousels
- Add additional arguments to the blocks

Example:

```xml
<referenceContainer name="content.aside">
    <block class="Renga\CustomLinkProduct\Block\Product\SimilarProducts" name="catalog.product.similar" template="Renga_CustomLinkProduct::product/similar_products.phtml" after="catalog.product.related">
        <arguments>
            <argument name="title" translate="true" xsi:type="string">Similar Products</argument>
        </arguments>
    </block>
    <!-- Other carousel blocks -->
</referenceContainer>
```

#### CSS

The module includes CSS for styling the carousels. You can override these styles in your theme's CSS to customize:

- Spacing and margins
- Colors and typography
- Navigation arrow appearance
- Product item styling

Example:

```css
/* Change spacing between carousels */
.similar-products,
.repair-products,
.functional-products {
    margin-bottom: 60px;
}

/* Customize navigation arrows */
.similar-products .slick-prev,
.similar-products .slick-next {
    background-color: #f0f0f0;
    border-radius: 50%;
}

/* Style product names */
.similar-products .product-item-name {
    font-weight: bold;
    color: #333;
}
```

#### JavaScript

The carousel functionality is implemented using the Slick Carousel library. You can customize the carousel behavior by modifying the configuration in your theme's JavaScript:

```js
// Override default configuration
require([
    'jquery'
], function ($) {
    'use strict';
    
    // Wait for the carousel to be initialized
    $(document).on('carousel:initialized', function() {
        // Customize the similar products carousel
        $('.similar-carousel').slick('unslick').slick({
            dots: true,                // Show navigation dots
            infinite: true,            // Enable infinite looping
            speed: 500,                // Animation speed in milliseconds
            slidesToShow: 3,           // Number of slides to show at once
            slidesToScroll: 1,         // Number of slides to scroll at a time
            autoplay: true,            // Enable autoplay
            autoplaySpeed: 5000,       // Autoplay speed in milliseconds
            arrows: true,              // Show navigation arrows
            centerMode: true,          // Center the active slide
            centerPadding: '60px',     // Padding for center mode
            // Responsive settings for different screen sizes
            responsive: [
                {
                    breakpoint: 768,
                    settings: {
                        slidesToShow: 2
                    }
                },
                {
                    breakpoint: 480,
                    settings: {
                        slidesToShow: 1,
                        centerMode: false
                    }
                }
            ]
        });
    });
});
```

## Documentation

For detailed API documentation, please refer to the following resources in the `docs` directory:

- [API Documentation](docs/API-DOCUMENTATION.md) - Comprehensive API documentation
- [Query Examples](docs/query-examples.md) - Detailed GraphQL query examples
- [Example GraphQL Payloads](docs/example-payloads.md) - Detailed GraphQL mutation examples
- [Example cURL Requests](docs/curl-examples.md) - cURL request examples

## License

[OSL-3.0](https://opensource.org/licenses/OSL-3.0)
