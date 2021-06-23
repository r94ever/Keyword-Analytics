<?php

return [
    'variables' => [
        // Defines the valid length in (count by words) of the keyword
        'keyword_length' => [
            'min' => 2,
            'max' => 4
        ],

        // Defines the valid length (count by characters) of the headline title
        'title_length' => [
            'min' => 30,
            'max' => 60
        ],

        // Determine the allowed number of occurrences of the keyword in the title
        'keyword_in_title' => [
            'min' => 1,
            'max' => 2
        ],

        // Defines the valid length (count by characters) of the meta description
        'description_length' => [
            'min' => 140,
            'max' => 160
        ],

        // Determine the allowed number of occurrences of the keyword in the meta description
        'keyword_in_description' => [
            'min' => 1,
            'max' => 2
        ],

        // Defines the valid length (count by words) of the article content
        'content_length' => [
            'min' => 300
        ],

        // Determine the allowed number of occurrences of the keyword in the first paragraph of content
        'keyword_in_first_paragraph' => [
            'min' => 1,
            'max' => 2
        ],

        // Determine the allowed number of occurrences of the heading tags (h1 -> h6) in the article content
        'heading_in_content' => [
            'min' => 1
        ],

        // Determine the allowed number of occurrences of the keyword in the heading tags
        'keyword_in_heading' => [
            'min' => 1
        ],

        // Determine the allowed number of occurrences of the image tag in the article content
        'image_in_content' => [
            'min' => 1
        ],

        // Determine the allowed number of occurrences of the keyword in the ALT attribute of img tags
        'keyword_in_alt_image' => [
            'min' => 1
        ],

        // Determine the allowed number of occurrences of the links in the article content
        'link_in_content' => [
            'min' => 2
        ],

        // Determine the allowed number of occurrences of the keyword in the TITLE attribute of links
        'keyword_in_link_title' => [
            'min' => 1
        ],

        // Determine the keyword density (by percents) of article content
        'keyword_density' => [
            'min' => 1,
            'max' => 2
        ],

        // Determine the allowed number of occurrences of the keyword in the article's url
        'keyword_in_url' => [
            'min' => 1,
            'max' => 2
        ]
    ],

    /**
     * The following configuration is used when you want to run analysis from request input
     * without give any data.
     *
     * Example, with the default configuration, if the request key contains `keyword`, `html`, `meta_description`, `headline`, `url` then
     * you can run analytics instantly by Analytics::fromRequest()->run()->getResults();
     */
    'request_keys' => [
        'keyword'       => 'keyword',
        'html'          => 'html',
        'description'   => 'meta_description',
        'title'         => 'headline',
        'url'           => 'url'
    ],
];