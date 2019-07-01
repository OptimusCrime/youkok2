<?php
namespace Youkok\Helpers;

use Monolog\Logger;

class SettingsParser
{
    public static function getSlimConfig(): array
    {
        return [
            'settings' => [
                'displayErrorDetails' => getenv('DEV') === '1',
                'addContentLengthHeader' => false,

                'base_dir' => getenv('BASE_DIRECTORY'),
            ]
        ];
    }

    public static function getPhinxConfig(): array
    {
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
                    'host' => getenv('MYSQL_HOST'),
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
    }
}
