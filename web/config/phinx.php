<?php
require_once dirname(__FILE__) . '/../_scripts/parse_env_variables.php';

$envDirectory = '/etc/apache2/sites-enabled/envs/';

parseEnvVariables($envDirectory . 'default');
parseEnvVariables($envDirectory . 'production');

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