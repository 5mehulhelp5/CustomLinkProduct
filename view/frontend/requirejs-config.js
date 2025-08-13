/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

var config = {
    map: {
        '*': {
            'similar-products-carousel': 'Renga_CustomLinkProduct/js/similar-products-carousel',
            'product-carousel': 'Renga_CustomLinkProduct/js/product-carousel'
        }
    },
    shim: {
        'Renga_CustomLinkProduct/js/similar-products-carousel': {
            deps: ['jquery', 'slick']
        },
        'Renga_CustomLinkProduct/js/product-carousel': {
            deps: ['jquery', 'slick']
        }
    }
};
