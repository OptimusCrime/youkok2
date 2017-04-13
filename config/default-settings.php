<?php
return [
    'settings' => [
        'displayErrorDetails' => true,
        'addContentLengthHeader' => false,

        'base_dir' => dirname(__DIR__),

        'logger' => [
            'name' => 'slim-app',
            'path' => isset($_ENV['docker']) ? 'php://stdout' : __DIR__ . '/../logs/app.log',
            'level' => \Monolog\Logger::DEBUG,
        ],

        'db' => [
            'driver' => 'mysql',
            'host' => isset($_ENV['docker']) ? 'db' : 'localhost',
            'database' => 'youkok2',
            'username' => isset($_ENV['MYSQL_USER']) ? $_ENV['MYSQL_USER'] : 'root',
            'password' => isset($_ENV['MYSQL_PASSWORD']) ? $_ENV['MYSQL_PASSWORD'] : 'youkok2',
            'charset'   => 'utf8',
            'collation' => 'utf8_unicode_ci',
            'prefix'    => '',
        ],

        // Site specific settings, use uppercase for their keywords
        'site' => [
            'DEV' => true,
            'VERSION' => '3.0.0-alpha',
            'GOOGLE_ANALYTICS' => false,
            'EMAIL_CONTACT' => 'foo@bar.tld'
        ],
    ],
];
