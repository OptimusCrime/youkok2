<?php
return [
    'settings' => [
        'displayErrorDetails' => getenv('DEV') === '1',
        'addContentLengthHeader' => false,

        'base_dir' => dirname(__DIR__),

        'logger' => [
            'name' => 'slim-app',
            'path' => '/var/logs/apache2/app.log',
            'level' => \Monolog\Logger::DEBUG,
        ],

        'cache' => [
            'host' => 'cache',
            'port' => getenv('REDIS_PORT')
        ],

        'db' => [
            'driver' => 'mysql',
            'host' => 'db',
            'database' => getenv('MYSQL_TABLE'),
            'username' => getenv('MYSQL_USER'),
            'password' => getenv('MYSQL_PASSWORD'),
            'port' => getenv('MYSQL_PORT'),
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
        'templates_dir' => '/var/www/html/templates/',
        'file_directory' => '/var/www/files/',
        'cache_directory' => '/var/www/cache/',

        'file_endings' => [
            'pdf', 'txt', 'java', 'py', 'html', 'htm', 'sql'
        ],

        // Site specific settings, use uppercase for their keywords
        'site' => [
            'VERSION' => '3.0.0-alpha',
            'GOOGLE_ANALYTICS' => false,
            'GOOGLE_SENSE' => false,
            'GOOGLE_ANALYTICS_CODE' => 'foo',
            'GOOGLE_SENSE_CODE' => 'foo',
            'EMAIL_CONTACT' => 'foo@bar.tld',
        ],
    ],
];
