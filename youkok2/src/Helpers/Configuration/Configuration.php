<?php
namespace Youkok\Helpers\Configuration;

use Youkok\Biz\Exceptions\MissingConfigurationException;

class Configuration
{
    const DEV = 'dev';
    const SSL = 'ssl';

    const LOGGER_NAME = 'logger.name';

    const DIRECTORY_TEMPLATE = 'directory.template';
    const DIRECTORY_FILES = 'directory.files';

    const MYSQL_HOST = 'mysql.host';
    const MYSQL_USER = 'mysql.user';
    const MYSQL_PASSWORD = 'mysql.password';
    const MYSQL_DATABASE = 'mysql.database';
    const MYSQL_PORT = 'mysql.port';

    const ADMIN_COOKIE = 'admin.cookie';
    const ADMIN_PASS_PREFIX = 'admin.pass';

    const CACHE_HOST = 'cache.host';
    const CACHE_PORT = 'cache.port';

    const FILE_UPLOAD_MAX_SIZE_IN_BYTES = 'file_upload.max_size_in_bytes';
    const FILE_UPLOAD_ALLOWED_TYPES = 'file_upload.allowed_types';

    private static ?Configuration $instance = null;
    private array $defaultConfiguration;
    private array $configuration;

    private function __construct()
    {
        $this->defaultConfiguration = DefaultConfiguration::getDefaultConfiguration();
        $this->configuration = [];
    }

    public function isDev(): bool
    {
        return $this->lookup(static::DEV) === '1';
    }

    public function isSSL(): bool
    {
        return $this->lookup(static::SSL) === '1';
    }

    public function getLoggerName(): string
    {
        return $this->lookup(static::LOGGER_NAME);
    }

    public function getDirectoryTemplate(): string
    {
        return $this->lookup(static::DIRECTORY_TEMPLATE);
    }

    public function getDirectoryFiles(): string
    {
        return $this->lookup(static::DIRECTORY_FILES);
    }

    public function getMysqlHost(): string
    {
        return $this->lookup(static::MYSQL_HOST);
    }

    public function getMysqlUser(): string
    {
        return $this->lookup(static::MYSQL_USER);
    }

    public function getMysqlPassword(): string
    {
        return $this->lookup(static::MYSQL_PASSWORD);
    }

    public function getMysqlDatabase(): string
    {
        return $this->lookup(static::MYSQL_DATABASE);
    }

    public function getMysqlPort(): int
    {
        return intval($this->lookup(static::MYSQL_PORT));
    }

    public function getAdminCookie(): string
    {
        return $this->lookup(static::ADMIN_COOKIE);
    }

    public function getAdminPass(int $value): string
    {
        return $this->lookup(static::ADMIN_PASS_PREFIX . strval($value));
    }

    public function getCacheHost(): string
    {
        return $this->lookup(static::CACHE_HOST);
    }

    public function getCachePort(): int
    {
        return intval($this->lookup(static::CACHE_PORT));
    }

    public function getFileUploadMaxSizeInBytes(): int
    {
        return intval($this->lookup(static::FILE_UPLOAD_MAX_SIZE_IN_BYTES));
    }

    public function getFileUploadAllowedTypes(): array
    {
        return static::formatAllowedTypes($this->lookup(static::FILE_UPLOAD_ALLOWED_TYPES));
    }

    private function lookup(string $key): string
    {
        if (isset($this->configuration[$key])) {
            return $this->configuration[$key];
        }

        // Check if environment override
        $envKey = str_replace('.', '_', strtoupper($key));
        $envValue = getenv($envKey);

        if ($envValue !== null && $envValue !== false) {
            $this->configuration[$key] = $envValue;
            return $this->configuration[$key];
        }

        // No environment override, use the default value
        if (!isset($this->defaultConfiguration[$key])) {
            // If we got here, and there are no default configuration sat, throw an error
            throw new MissingConfigurationException('No value for key: ' . $key);
        }

        $this->configuration[$key] = $this->defaultConfiguration[$key];

        return $this->configuration[$key];
    }

    public static function getInstance(): Configuration
    {
        if (self::$instance === null) {
            self::$instance = new Configuration();
        }

        return self::$instance;
    }

    private static function formatAllowedTypes(string $types): array
    {
        $arr = [];

        $typesSplit = explode(',', $types);
        foreach ($typesSplit as $type) {
            if (mb_strlen($type) > 0) {
                $arr[] = $type;
            }
        }

        return $arr;
    }
}
