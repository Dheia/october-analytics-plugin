<?php

return [
    'plugin' => [
        'name' => 'Simple Analytics',
        'description' => 'Simple, GDPR-friendly and Consent-free Analytics system for your OctoberCMS website.'
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
        'statistics' => [
            'label' => 'Simple Statistics - General',
            'title' => 'Synder Statistics - General',
            'views' => 'Views',
            'visits' => 'Visits',
            'visitors' => 'Visitors',
            'count_value' => 'Today / 7 Days',
            'toggle' => 'Click to toggle',
            'show_counts' => 'Show Counts',
            'color_views' => 'Views Color',
            'color_visits' => 'Visits Color',
            'color_visitors' => 'Visitors Color',
            'color_error' => 'Please enter a valid HEX color value (ex.: #008dc9).'
        ],
        'systems' => [
            'label' => 'Simple Statistics - Browser / OS Usage',
            'title' => 'Synder Statistics - Browser / OS Usage',
            'empty' => 'No data available yet.',
            'show_legend' => 'Show Legend'
        ],
        'referrers' => [
            'label' => 'Simple Statistics - Top Referrers',
            'title' => 'Synder Statistics - Top Referrers',
            'tr_hosts' => 'Primary Host',
            'tr_urls' => 'Related URLs',
            'tr_views' => 'Views',
            'tr_last' => 'Last Visit',
            'table_empty' => 'No referrers available yet.',
            'timeperiod' => 'Time Period',
            'amount' => 'Number of Items',
            'amount_error' => 'Please enter numbers only'
        ],
        'pages' => [
            'label' => 'Simple Statistics - Top Pages',
            'title' => 'Synder Statistics - Top Pages',
            'tr_path' => 'Method - URL',
            'tr_views' => 'Views',
            'tr_visits' => 'Visits',
            'tr_last' => 'Last Visit',
            'table_empty' => 'No data available yet.',
            'timeperiod' => 'Time Period',
            'amount' => 'Number of Items',
            'amount_error' => 'Please enter numbers only'
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
            'bots' => 'Bot Configuration',
            'events' => 'Event Manager',
            'tracking' => 'Tracking System'
        ],
        '_plus' => [
            'events_label' => 'Event Manager',
            'events_comment' => 'The Event Manager allows you to track and analyse specific elements and pages of your website.',
            'tracking_label' => 'Tracking System',
            'tracking_comment' => 'The Tracking system allows you to track your visitors across sessions and also across browsers and devices in certain circumstances, which, however, requires whose consent.',
            'wip_label' => 'Synder\'s Advanced Analytics plugin is Coming Soon',
            'wip_comment' => 'This and many other awesome features are part of the upcoming Advanced Analytics plugin, which is not available yet.<br><br>You can <a href="https://www.synder.dev/reserve-advanced-analytics" target="_blank">register here</a> free of charge and without obligation to receive a voucher as soon as the product is available.'
        ],

        'filter_backend_users' => 'Filter Backend Users',
        'filter_backend_users_desc' => 'Logged-In backend users are not included in the statistics.',

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
        ],
        
        'section_development' => 'Development Options',
        'dev_reevaluate' => 'Re-Evaluate User Agents',
        'dev_reevaluate_desc' => 'Manually re-evaluate Bot Probability depending on the stored User Agent.',

        'bot_lazy' => 'Lazy Bot Evaluation',
        'bot_lazy_desc' => 'The performance of the visitor is not impaired by a lazy bot evaluation.<br />Set this to \'On\' if you\'re using the Bot-probability filter for the statistics only.',
        'bot_filter' => 'Filter per Bot-probability',
        'bot_filter_desc' => 'The bot probability is calculated between 0.1 (human) to 5.0 (bot), thus the lower the value, the more requests are filtered out. (Recommended: 4.2).',

        'section_bot_robots' => 'Robots.txt Honeypot',
        'hint_bot_robots_label' => 'What is a robots.txt file?',
        'hint_bot_robots_comment' => 'A robots.txt file is used by search engine crawlers, such as Google or Bing, and declares some rules how these crawlers should access and index your website. Many bots will just ignore this file completely, however, some bad bots will explicit crawl this file and access the URLs against the defined rules.',
        'bot_robots' => 'Use robots.txt Honeypot',
        'bot_robots_desc' => 'Adds a generated Honeypot page to robots.txt.',
        'bot_robots_relocate' => 'Re-Locate robots.txt entry',
        'bot_robots_relocate_desc' => 'Relocates the robots.txt entry all 90-days.',
        'bot_robots_relocate_cron' => 'Use Scheduled Task',
        'bot_robots_relocate_cron_desc' => 'Scheduled Tasks requires an <a href="https://octobercms.com/docs/setup/installation#crontab-setup" target="_blank">additional OctoberCMS step</a>.',
        'bot_robots_test' => 'Test robots.txt',
        'bot_robots_test_comment' => 'Save your settings before testing.',

        'section_bot_inlink' => 'Invisible Link Honeypot',
        'hint_bot_inlink_label' => 'What does an invisible Link?',
        'hint_bot_inlink_comment' => 'The most crawler bots (such as Google and Bing, but also the bad ones) collects all links on a website to follow and index them accordingly. Using an invisible link, which is mostly placed in the footer, will attract such bots as well and since human users won\'t see it, bots can be lured into the trap.',
        'bot_inlink' => 'Use invisible link Honeypot',
        'bot_inlink_desc' => 'Places an invisible link in the footer of your website.',
        'bot_inlink_relocate' => 'Re-Locate invisible link',
        'bot_inlink_relocate_desc' => 'Relocates the invisible link all 90-days.',
        'bot_inlink_test' => 'Test invisible link',
    ]
];
