<?php
/**
 * Lib Curl
 * @package lib-curl
 * @version 0.0.3
 */

return [
    '__name' => 'lib-curl',
    '__version' => '0.2.0',
    '__git' => 'git@github.com:getphun/lib-curl.git',
    '__license' => 'MIT',
    '__author' => [
        'name' => 'Iqbal Fauzi',
        'email' => 'iqbalfawz@gmail.com',
        'website' => 'https://iqbalfn.com/'
    ],
    '__files' => [
        'etc/log/lib-curl' => ['install', 'remove'],
        'modules/lib-curl' => ['install', 'update', 'remove']
    ],
    '__dependencies' => [
        'required' => [],
        'optional' => []
    ],
    '__inject' => [
        [
            'name' => 'libCurl',
            'question' => 'lib-curl app config',
            'children' => [
        		[
        			'name' => 'log',
        			'default' => true,
        			'question' => 'Log all curl access',
        			'rule' => 'boolean'
        		]
            ]
        ]
    ],
    '__gitignore' => [
        'etc/log/lib-curl/*' => true,
        '!etc/log/lib-curl/.gitkeep' => true
    ],
    'autoload' => [
        'classes' => [
            'LibCurl\\Library' => [
                'type' => 'file',
                'base' => 'modules/lib-curl/library'
            ],
            'LibCurl\\Server' => [
                'type' => 'file',
                'base' => 'modules/lib-curl/server'
            ]
        ]
    ],
    'server' => [
        'lib-curl' => [
            'cURL' => 'LibCurl\\Server\\PHP::curl'
        ]
    ],
    
    'libCurl' => [
        'log' => false
    ]
];
