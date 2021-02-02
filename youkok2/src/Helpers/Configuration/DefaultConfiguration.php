<?php
namespace Youkok\Helpers\Configuration;

class DefaultConfiguration
{
    public static function getDefaultConfiguration(): array
    {
        // Note: This array must have all values as strings
        return [
            Configuration::DEV => '1',
            Configuration::SSL => '0',

            Configuration::LOGGER_NAME => 'youkok2',

            Configuration::DIRECTORY_TEMPLATE => '/code/site/templates/',
            Configuration::DIRECTORY_FILES => '/code/file/',

            Configuration::MYSQL_HOST => 'youkok2-db',
            Configuration::MYSQL_USER => 'youkok2',
            Configuration::MYSQL_PASSWORD => 'youkok2',
            Configuration::MYSQL_DATABASE => 'youkok2',
            Configuration::MYSQL_PORT => '3306',

            Configuration::CACHE_HOST => 'youkok2-cache',
            Configuration::CACHE_PORT => '6379',

            Configuration::FILE_UPLOAD_MAX_SIZE_IN_BYTES => '10000000',
            Configuration::FILE_UPLOAD_ALLOWED_TYPES => 'pdf,txt,java,py,html,htm,sql'
        ];
    }
}
