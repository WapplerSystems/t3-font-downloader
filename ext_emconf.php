<?php

$EM_CONF['font_downloader'] = [
    'title' => 'Font downloader for TYPO3',
    'description' => '',
    'category' => 'fe',
    'version' => '12.0.0',
    'state' => 'stable',
    'author' => 'Sven Wappler',
    'author_email' => 'typo3YYYY@wappler.systems',
    'author_company' => 'WapplerSystems',
    'constraints' => [
        'depends' => [
            'php' => '8.0.0-8.2.99',
            'typo3' => '12.0.0-12.4.99',
        ],
    ],
];
