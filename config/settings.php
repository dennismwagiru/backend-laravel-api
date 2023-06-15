<?php

return [
    'per_page' => 30,
    'sources' => [
        'news-api' => [
            'api-key' => env('NEWS_API_PEY'),
            'label' => 'News Api'
        ],
        'the-guardian' => [
            'api-key' => env('THE_GUARDIAN_API_KEY'),
            'label' => 'The Guardian'
        ],
        'news-cred' => [
            'api-key' => env('NEWS_CRED_API_KEY'),
            'label' => 'News Cred'
        ],
        'nwt' => [
            'api-key' => env('NWT_API_KEY'),
            'label' => 'New York Times'
        ]
    ]
];