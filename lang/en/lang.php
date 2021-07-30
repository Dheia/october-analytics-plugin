<?php 

return [
    'plugin' => [
        'name' => 'Simple Analytics',
        'description' => 'Simple and GDPR-friendly Analytics system for your OctoberCMS website.'
    ],

    'shared' => [
        'time_all' => 'All Time',
        'time_7days' => 'Last 7 days',
        'time_14days' => 'Last 14 days',
        'time_31days' => 'Last 31 days',
        'time_week' => 'Current Week',
        'time_month' => 'Current Month',
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
            'table_empty' => 'No data available yet.',

            'config' => [
                'timeperiod' => 'Time Period',
                'formatdates' => 'Format Dates'
            ]
        ]
    ],

    'backend' => [
        'title' => 'Statistics'
    ],

    'config' => [
        '_label' => 'Simple Analytics',
        '_description' => 'General Settings for the Synder Simple Analytics plugin.',
        '_tabs' => [
            'general' => 'General Settings',
            'bots' => 'Bot Configuration'
        ],

        'filterbots' => 'Filter per Bot-probability',
        'filterbots_desc' => 'The bot probability is calculated between 0.1 (human) to 5.0 (bot), thus the lower the value, the more requests are filtered out. (Recommended: 4.2).',
        
        'section_bot_robots' => 'Robots.txt Honeypot',
        'hint_bot_robots_label' => 'What is a robots.txt file?',
        'hint_bot_robots_comment' => 'A robots.txt file is used by search engine crawlers, such as Google or Bing, and declares some rules how these crawlers should access and index your website. Many bots will just ignore this file completely. however, some bad bots will explicitly crawl this file and access the URLs agains the defined rules.',
        'bot_robots' => 'Use robots.txt Honeypot',
        'bot_robots_desc' => 'Adds a generated Honeypot file to robots.txt.',
        'bot_robots_relocate' => 'Re-Locate robots.txt entry',
        'bot_robots_relocate_desc' => 'Relocates the robots.txt entry all 90-days.',
        'bot_robots_test' => 'Test robots.txt',
        
        'section_bot_inlink' => 'Invisible Link Honeypot',
        'hint_bot_inlink_label' => 'What is a invisible Link?',
        'hint_bot_inlink_comment' => '',
        'bot_inlink' => 'Use invisible link',
        'bot_inlink_desc' => 'Adds an invisible link to the footer, which some bots will access.',
        'bot_inlink_relocate' => 'Re-Locate invisible link',
        'bot_inlink_relocate_desc' => 'Relocates the invisible link all 90-days.',
        'bot_inlink_test' => 'Test invisible link',

        'section_datetime' => 'Date/Time Formats',
        'dateformat' => 'DateTime Format',
        'dateformats' => [
            'plain' => 'Plain (Y-m-d H:i:s)',
            'big' => 'Big Endian (Y, M d. - H:i)',
            'middle' => 'Middle Endian (M d, Y - H:i)',
            'little' => 'Little Endian (d. M Y - H:i)',
            'custom' => 'Own DateTime Definition'
        ],

        'customformat' => 'Custom DateTime Format',
        'customformat_desc' => 'Supports the <a href="https://www.php.net/manual/en/datetime.format.php#refsect1-datetime.format-parameters" target="_blank">PHP DateTime Format</a> only.',

        'weekstart' => 'Start Week on',
        'weekdays' => [
            'sunday' => 'Sunday',
            'monday' => 'Monday',
            'tuesday' => 'Tuesday',
            'wednesday' => 'Wednesday',
            'thursday' => 'Thursday',
            'friday' => 'Friday',
            'saturday' => 'Saturday'
        ]
    ]
];
