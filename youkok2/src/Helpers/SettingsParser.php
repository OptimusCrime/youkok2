<?php

namespace Youkok\Helpers;

use Dotenv\Dotenv;

class SettingsParser
{
    const DEFAULT_FILE = '.env';
    const DEV_FILE = '.env-dev';

    public function __construct()
    {
        $baseDir = dirname(dirname(__DIR__)) . '/';
        $dotFile = static::getDotFile($baseDir);

        $dotenv = new Dotenv($baseDir, $dotFile);
        $dotenv->load();
    }

    public function getSlimConfig()
    {
        return [
            'settings' => [
                'displayErrorDetails' => getenv('DEV') === '1',
                'addContentLengthHeader' => false,

                'base_dir' => getenv('BASE_DIR'),

                'logger' => [
                    'name' => getenv('LOGGER_NAME'),
                    'path' => getenv('LOGGER_PATH'),
                    'level' => \Monolog\Logger::DEBUG,
                ],
            ]
        ];
    }

    public function getPhinxConfig()
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
                    'host' => getenv('DATABASE_HOST'),
                    'name' => getenv('DATABASE_TABLE'),
                    'user' => getenv('DATABASE_USER'),
                    'pass' => getenv('DATABASE_PASSWORD'),
                    'port' => getenv('DATABASE_PORT'),
                    'charset' => 'utf8',
                ],
                'test' => [
                    'adapter' => 'sqlite',
                    'name' => './tests/files/db.sqlite3',
                ]
            ]
        ];
    }

    private static function getDotFile($baseDir)
    {
        if (file_exists($baseDir . SettingsParser::DEFAULT_FILE)) {
            return SettingsParser::DEFAULT_FILE;
        }

        return SettingsParser::DEV_FILE;
    }
}
