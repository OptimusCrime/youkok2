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

        'cache' => [
            'host' => 'cache',
            'port' => '6379'
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

        'admin' => [
            'pass1' => '$2y$10$EBVb8yJDKN6/E9M/O8nQQ.DoA3t/UIPxn6VoXlxs65xWa2zHAm/tK', // lorem
            'pass2' => '$2y$10$TKwzrC/P4RSwz7dyAaV6TOXyLtPg9MotXHXTro3T4Hiznx4hryZMK', // empty string
            'pass3' => '$2y$10$TKwzrC/P4RSwz7dyAaV6TOXyLtPg9MotXHXTro3T4Hiznx4hryZMK', // empty string
            'pass4' => '$2y$10$TKwzrC/P4RSwz7dyAaV6TOXyLtPg9MotXHXTro3T4Hiznx4hryZMK', // empty string
            'pass5' => '$2y$10$TKwzrC/P4RSwz7dyAaV6TOXyLtPg9MotXHXTro3T4Hiznx4hryZMK', // empty string
            'pass6' => '$2y$10$TKwzrC/P4RSwz7dyAaV6TOXyLtPg9MotXHXTro3T4Hiznx4hryZMK', // empty string
        ],

        // MUST have an ending slash
        'file_directory' => '/var/www/files/',

        'file_endings' => [
            'pdf', 'txt', 'java', 'py', 'html', 'htm', 'sql'
        ],

        'file_endings_display' => [
            'pdf'
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
