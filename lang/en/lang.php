<?php 

return [
    'plugin' => [
        'name' => 'Simple Analytics',
        'description' => 'Simple and GDPR-friendly Analytics system for your OctoberCMS website.'
    ],

    'widgets' => [
        'referrers' => [
            'label' => 'Simple Statistics - Top Referrers',
            'title' => 'Synder Statistics - Top Referrers',
            'tr_hosts' => 'Primary Host',
            'tr_urls' => 'Related URLs',
            'tr_views' => 'Views',
            'tr_last' => 'Last Visit',
            'table_empty' => 'No referrers available yet.'
        ],

        'statistics' => [
            'label' => 'Simple Statistics - Basic',
            'title' => 'Synder Statistics - Basic',
            'views' => 'Views',
            'visits' => 'Visits',
            'visitors' => 'Visitors'
        ],

        'systems' => [
            'label' => 'Simple Statistics - Browser / OS Usage',
            'title' => 'Synder Statistics - Browser / OS Usage',
            'empty' => 'No data available'
        ],

        'pages' => [
            'label' => 'Simple Statistics - Top Pages',
            'title' => 'Synder Statistics - Top Pages',
            'tr_path' => 'Method - URL',
            'tr_views' => 'Views',
            'tr_visits' => 'Visits',
            'tr_last' => 'Last Visit',
            'table_empty' => 'No data available yet.'
        ]
    ],

    'backend' => [
        'title' => 'Statistics'
    ]
];
