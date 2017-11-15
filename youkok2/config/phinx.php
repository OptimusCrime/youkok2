<?php
require __DIR__ . '/../vendor/autoload.php';

use Youkok\Utilities\EnvParser;
use Youkok\Helpers\SettingsParser;

EnvParser::parse('/etc/apache2/sites-enabled/envs/', ['default', 'production']);

$settingsParser = new SettingsParser();
$settingsParser->parse([
    __DIR__ . '/../config/default-settings.php',
    __DIR__ . '/../config/settings.php'
]);

return [
    'paths' => [
        'migrations' => '%%PHINX_CONFIG_DIR%%/../phinx/migrations',
        'seeds' => '%%PHINX_CONFIG_DIR%%/../phinx/seeds',
    ],
    'environments' => [
        'default_migration_table' => 'phinxlog',
        'default_database' => 'production',
        'production' => [
            'adapter' => 'mysql',
            'host' => 'db',
            'name' => getenv('MYSQL_TABLE'),
            'user' => getenv('MYSQL_USER'),
            'pass' => getenv('MYSQL_PASSWORD'),
            'port' => getenv('MYSQL_PORT'),
            'charset' => 'utf8',
        ],
        'test' => [
            'adapter' => 'sqlite',
            'name' => './tests/files/db.sqlite3',
        ]
    ]
];