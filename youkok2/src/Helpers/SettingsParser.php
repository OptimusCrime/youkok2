<?php
namespace Youkok\Helpers;

use Youkok\Helpers\Configuration\Configuration;

class SettingsParser
{
    public static function getPhinxConfig(): array
    {
        $configuration = Configuration::getInstance();
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
                    'host' => $configuration->getDbHost(),
                    'user' => $configuration->getDbUser(),
                    'pass' => $configuration->getDbPassword(),
                    'name' => $configuration->getDbDatabase(),
                    'port' => $configuration->getDbPort(),
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
