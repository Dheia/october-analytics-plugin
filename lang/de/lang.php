<?php

return [
    'plugin' => [
        'name' => 'Simple Analytics',
        'description' => 'DSGVO-freundlich und Konsensfreies Analyse System für deine OctoberCMS Webseite.'
    ],

    'shared' => [
        'time_all' => 'Gesamte Zeit',
        'time_7days' => 'Letzten 7 Tage',
        'time_14days' => 'Letzten 14 Tage',
        'time_31days' => 'Letzten 31 Tage',
        'time_week' => 'Aktuelle Woche',
        'time_month' => 'Aktuelles Monat',
    ],

    'widgets' => [
        'statistics' => [
            'label' => 'Statistik - Allgemein',
            'title' => 'Synder Statistik - Allgemein',
            'views' => 'Aufrufe',
            'visits' => 'Besuche',
            'visitors' => 'Besucher',
            'count_value' => 'Heute / 7 Tage',
            'toggle' => 'Zum Umschalten klicken',
            'show_counts' => 'Zähler anzeigen',
            'color_views' => 'Farbe Aufrufe',
            'color_visits' => 'Farbe Besuche',
            'color_visitors' => 'Farbe Besucher',
            'color_error' => 'Bitte gebe einen gültigen HEX-Wert an (bsp.: #008dc9).'
        ],
        'systems' => [
            'label' => 'Statistik - Browser / OS Nutzung',
            'title' => 'Synder Statistik - Browser / OS Nutzung',
            'empty' => 'Noch keine Daten verfügbar.',
            'show_legend' => 'Legende anzeigen'
        ],
        'referrers' => [
            'label' => 'Statistik - Häufigsten Referrers',
            'title' => 'Synder Statistik - Häufigsten Referrers',
            'tr_hosts' => 'Primärer Host',
            'tr_urls' => 'Zugehörige URLs',
            'tr_views' => 'Aufrufe',
            'tr_last' => 'Letzter Besuch',
            'table_empty' => 'Noch keine Referrers verfügbar.',
            'timeperiod' => 'Zeitperiode',
            'amount' => 'Anzahl der Einträge',
            'amount_error' => 'Bitte gebe lediglich Nummern an'
        ],
        'pages' => [
            'label' => 'Statistik - Beliebte Seiten',
            'title' => 'Synder Statistik - Beliebte Seiten',
            'tr_path' => 'Methode - URL',
            'tr_views' => 'Aufrufe',
            'tr_visits' => 'Besuche',
            'tr_last' => 'Letzter Besuch',
            'table_empty' => 'Noch keine Daten verfügbar.',
            'timeperiod' => 'Zeitperiode',
            'amount' => 'Anzahl der Einträge',
            'amount_error' => 'Bitte gebe lediglich Nummern an'
        ]
    ],

    'backend' => [
        'title' => 'Statistiken'
    ],

    'config' => [
        '_label' => 'Simple Analytics',
        '_description' => 'Allgemeine Einstellungen für das Synder Simple Analytics Plugin.',
        '_tabs' => [
            'general' => 'Allgemeine Optionen',
            'bots' => 'Bot Konfiguration',
            'events' => 'Event Manager',
            'tracking' => 'Tracking System'
        ],
        '_plus' => [
            'events_label' => 'Event Manager',
            'events_comment' => 'Der Event Manager erlaubt dir spezifische Elemente oder Unterseiten deiner Webseite zu tracken und zu analysieren.',
            'tracking_label' => 'Tracking System',
            'tracking_comment' => 'Das Tracking System erlaubt dir deine Besucher über Sessions, und unter bestimmten Voraussetzungen auch über Browser und Geräte, hinweg zu tracken - erfordert allerdings dessen Zustimmung.',
            'wip_label' => 'Synders Advanced Analytics Plugin befindet sich noch in Arbeit',
            'wip_comment' => 'Dieses und weitere tolle Funktionen werden Bestandteil der kommenden Advanced Analytics Erweiterung, welche noch nicht verfügbar ist.<br><br>Du kannst dich allerdings <a href="https://www.synder.dev/reserve-advanced-analytics" target="_blank">hier</a> kostenfrei und unverbindlich anmelden um einen Gutschein bei Release zu erhalten.'
        ],

        'filter_backend_users' => 'Backend-Nutzer herausfiltern',
        'filter_backend_users_desc' => 'Angemeldete Backend-Nutzer werden nicht in die Statistik miteinbezogen.',

        'section_datetime' => 'Datum/Zeit Darstellung',
        'dateformat' => 'Datum/Zeit Format',
        'dateformats' => [
            'plain' => 'Schlicht (Y-m-d H:i:s)',
            'big' => 'Big Endian (Y, M d. - H:i)',
            'middle' => 'Middle Endian (M d, Y - H:i)',
            'little' => 'Little Endian (d. M Y - H:i)',
            'custom' => 'Eigene Definition'
        ],
        'customformat' => 'Eigene Definition',
        'customformat_desc' => 'Unterstützt lediglich das <a href="https://www.php.net/manual/en/datetime.format.php#refsect1-datetime.format-parameters" target="_blank">PHP DateTime Format</a>.',
        'weekstart' => 'Wochenstart am',
        'weekdays' => [
            'sunday' => 'Sonntag',
            'monday' => 'Montag',
            'tuesday' => 'Dienstag',
            'wednesday' => 'Mittwoch',
            'thursday' => 'Donnerstag',
            'friday' => 'Freitag',
            'saturday' => 'Samstag'
        ],
        
        'section_development' => 'Entwicklungseinstellungen',
        'dev_reevaluate' => 'User Agents neu evaluieren',
        'dev_reevaluate_desc' => 'Manuelle Neu-Evaluierung der Bot-Wahrscheinlichkeit anhand der User Agents.',

        'bot_lazy' => 'Langsame Bot-Evaluierung',
        'bot_lazy_desc' => 'Die Performanz des Besuchers wird bei einer langsamen Evaluierung nicht beeinträchtigt.<br />Nutze diese Option, wenn du den Bot-Wahrscheinlichkeitsfilter ohnehin nur für die Statistiken verwendest.',
        'bot_filter' => 'Filterung durch Bot-Wahrscheinlichkeit',
        'bot_filter_desc' => 'Der Bot-Wahrscheinlichkeitswert liegt zwischen 0,1 (Mensch) und 5,0 (Bot), je geringer der Filterwert desto mehr Anfragen werden herausgefiltert. (Empfehlung: 4,2)',

        'section_bot_robots' => 'Robots.txt Honeypot',
        'hint_bot_robots_label' => 'Was ist eine robots.txt Datei?',
        'hint_bot_robots_comment' => 'Die robots.txt Datei wird von Suchmaschinen-Crawlern, wie Google oder Bing, verwendet und definierten die Regeln wie diese Crawler deine Webseite indexieren sollen. Viele Bots ignorieren diese Datei, einge böse Bots suchen aber explizit nach dort ausgeschlossenen URLs.',
        'bot_robots' => 'robots.txt Honeypot aktivieren',
        'bot_robots_desc' => 'Fügt eine generierte Honeypot Seite zur robots.txt Datei hinzu.',
        'bot_robots_relocate' => 'robots.txt Eintrag neu belegen',
        'bot_robots_relocate_desc' => 'Belegt die eingetragene robots.txt URL alle 90 Tage neu.',
        'bot_robots_relocate_cron' => 'Als "Scheduled Task" ausführen',
        'bot_robots_relocate_cron_desc' => 'Scheduled Tasks erfordern <a href="https://octobercms.com/docs/setup/installation#crontab-setup" target="_blank">erweiterte OctoberCMS Schritte</a>.',
        'bot_robots_test' => 'robots.txt testen',
        'bot_robots_test_comment' => 'Speichere die Einstellungen vor dem Testen.',

        
        'bot_robots_relocate' => 'Re-Locate robots.txt entry',
        'bot_robots_relocate_desc' => 'Relocates the robots.txt entry all 90-days.',
        
        'bot_robots_test' => 'Test robots.txt',
        'bot_robots_test_comment' => 'Save your settings before testing.',

        'section_bot_inlink' => 'Invisible-Link Honeypot',
        'hint_bot_inlink_label' => 'Was bringt ein Invisible-Link?',
        'hint_bot_inlink_comment' => 'Die meisten Crawler-Bots, wie von Google aber auch die Bösen, sammeln und folgen sämtliche Links einer Webseite um diese entsprechend zu indexieren. Da Invisible-Links von Menschen nicht gesehen werden, solche Bots diese aber dennoch aufrufen, können die Bots so also getrennt erfasst werden.',
        'bot_inlink' => 'Invisible-Link Honeypot aktivieren',
        'bot_inlink_desc' => 'Platziert einen Invisible-Link in der Fußzeile deiner Webseite.',
        'bot_inlink_relocate' => 'Invisible-Link neu belegen',
        'bot_inlink_relocate_desc' => 'Belegt die URL des Invisible-Links alle 90 Tage neu.',
        'bot_inlink_test' => 'Invisible-Link testen',
    ]
];
