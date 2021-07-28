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
            'table_empty' => 'No Referrers available yet.'
        ],

        'statistics' => [
            'label' => 'Simple Statistics - Basic',
            'title' => 'Synder Statistics - Basic',
            'views' => 'Views',
            'visits' => 'Visits',
            'visitors' => 'Visitors'
        ]
    ],

    'backend' => [
        'title' => 'Statistics'
    ]
];
