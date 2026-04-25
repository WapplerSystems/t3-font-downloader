<?php

$EM_CONF['font_downloader'] = [
    'title' => 'Font downloader for TYPO3',
    'description' => 'Automatically downloads external CSS fonts (Google Fonts, Font Awesome, ...) and serves them locally for GDPR compliance',
    'category' => 'fe',
    'version' => '14.0.1',
    'state' => 'stable',
    'author' => 'Sven Wappler',
    'author_email' => 'typo3YYYY@wappler.systems',
    'author_company' => 'WapplerSystems',
    'constraints' => [
        'depends' => [
            'typo3' => '14.0.0-14.4.99',
        ],
    ],
];
