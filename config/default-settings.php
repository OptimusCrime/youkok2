<?php
return [
    'settings' => [
        'displayErrorDetails' => false,
        'addContentLengthHeader' => false,

        'base_dir' => dirname(__DIR__),

        'logger' => [
            'name' => 'slim-app',
            'path' => isset($_ENV['docker']) ? 'php://stdout' : __DIR__ . '/../logs/app.log',
            'level' => \Monolog\Logger::DEBUG,
        ],

        // Site specific settings, use uppercase for their keywords
        'site' => [
            'DEV' => true,
            'VERSION' => '3.0.0-alpha',
            'GOOGLE_ANALYTICS' => false,
        ],
    ],
];
