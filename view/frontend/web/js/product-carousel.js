/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'jquery',
    'slick'
], function ($) {
    'use strict';

    return function (config, element) {
        // Default configuration
        var carouselConfig = {
            dots: false,
            infinite: true,
            speed: 300,
            slidesToShow: 4,
            slidesToScroll: 1,
            autoplay: true,
            autoplaySpeed: 3000,
            arrows: true,
            responsive: [
                {
                    breakpoint: 1024,
                    settings: {
                        slidesToShow: 3,
                        slidesToScroll: 1
                    }
                },
                {
                    breakpoint: 768,
                    settings: {
                        slidesToShow: 2,
                        slidesToScroll: 1
                    }
                },
                {
                    breakpoint: 480,
                    settings: {
                        slidesToShow: 1,
                        slidesToScroll: 1
                    }
                }
            ]
        };

        // Apply custom configuration based on carousel type
        if (config && config.carouselType) {
            // Add carousel type as a class for CSS targeting
            $(element).addClass(config.carouselType + '-carousel-container');

            // Custom settings for specific carousel types can be added here
            switch (config.carouselType) {
                case 'similar':
                    // Custom settings for similar products carousel
                    break;
                case 'repair':
                    // Custom settings for repair products carousel
                    break;
                case 'functional':
                    // Custom settings for functional products carousel
                    break;
            }
        }

        // Initialize the carousel with the configuration
        $(element).slick(carouselConfig);
    };
});
