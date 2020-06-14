<?php
$EM_CONF[$_EXTKEY] = [
    'title' => 'Favicon',
    'description' => 'Add favicon through page settings. Renders icons for different devices.',
    'category' => 'frontend',
    'version' => '0.2.0',
    'state' => 'stable',
    'author' => 'Tanel Põld',
    'author_email' => 'tanel@brightside.ee',
    'author_company' => 'Brightside OÜ / t3brightside.com',
    'constraints' => [
        'depends' => [
            'typo3' => '9.5.0-10.4.99',
        ],
        'conflicts' => [
        ],
    ],
    'autoload' => [
        'psr-4' => [
            'Brightside\\Favicon\\' => 'Classes',
        ]
    ]
];
