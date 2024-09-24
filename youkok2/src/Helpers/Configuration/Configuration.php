<?php
namespace Youkok\Helpers\Configuration;

class Configuration
{
    const string DEV = 'DEV';
    const string SSL = 'SSL';

    CONST STRING DIRECTORY_TEMPLATE = 'DIRECTORY_TEMPLATE';
    CONST STRING DIRECTORY_FILES = 'DIRECTORY_FILES';

    CONST STRING DB_HOST = 'MYSQL_HOST';
    CONST STRING DB_USER = 'MYSQL_USER';
    CONST STRING DB_PASSWORD = 'MYSQL_PASSWORD';
    CONST STRING DB_DATABASE = 'MYSQL_DATABASE';
    CONST STRING DB_PORT = 'MYSQL_PORT';

    CONST STRING REDIS_HOST = 'CACHE_HOST';
    CONST STRING REDIS_PORT = 'CACHE_PORT';

    CONST STRING FILE_UPLOAD_MAX_SIZE_IN_BYTES = 'FILE_UPLOAD_MAX_SIZE_IN_BYTES';
    CONST STRING FILE_UPLOAD_ALLOWED_TYPES = 'FILE_UPLOAD_ALLOWED_TYPES';

    CONST STRING ADMIN_COOKIE = 'ADMIN_COOKIE';
    CONST STRING ADMIN_PASS_PREFIX = 'ADMIN_PASS';

    private static ?Configuration $instance = null;
    private array $configuration;

    private function __construct()
    {
        $this->configuration = [];
    }

    public function isDev(): bool
    {
        return $this->lookup(static::DEV, 'true') === '1';
    }

    public function isSSL(): bool
    {
        return $this->lookup(static::SSL, 'false') === '1';
    }

    public function getDirectoryTemplate(): string
    {
        return $this->lookup(static::DIRECTORY_TEMPLATE, '/code/site/templates/');
    }

    public function getDirectoryFiles(): string
    {
        return $this->lookup(static::DIRECTORY_FILES, '/code/files/');
    }

    public function getDbHost(): string
    {
        return $this->lookup(static::DB_HOST, 'youkok2-db');
    }

    public function getDbUser(): string
    {
        return $this->lookup(static::DB_USER, 'youkok2');
    }

    public function getDbPassword(): string
    {
        return $this->lookup(static::DB_PASSWORD, 'youkok2');
    }

    public function getDbDatabase(): string
    {
        return $this->lookup(static::DB_DATABASE, 'postgres');
    }

    public function getDbPort(): int
    {
        return intval($this->lookup(static::DB_PORT, '5432'));
    }

    public function getRedisHost(): string
    {
        return $this->lookup(static::REDIS_HOST, 'youkok2-cache');
    }

    public function getCachePort(): int
    {
        return intval($this->lookup(static::REDIS_PORT, '6379'));
    }

    public function getFileUploadMaxSizeInBytes(): int
    {
        return intval($this->lookup(static::FILE_UPLOAD_MAX_SIZE_IN_BYTES, '10000000'));
    }

    public function getFileUploadAllowedTypes(): array
    {
        return static::formatAllowedTypes($this->lookup(static::FILE_UPLOAD_ALLOWED_TYPES, 'pdf,txt,java,py,html,htm,sql'));
    }

    public function getAdminCookie(): string
    {
        return $this->lookup(static::ADMIN_COOKIE, 'foobar');
    }

    public function getAdminPass(int $value): string
    {
        return $this->lookup(static::ADMIN_PASS_PREFIX . $value, '');
    }

    private function lookup(string $key, string $fallback): string
    {
        if (isset($this->configuration[$key])) {
            return $this->configuration[$key];
        }

        // Check if environment override
        $envValue = getenv($key);

        if (is_string($envValue) && mb_strlen($envValue) > 0) {
            $this->configuration[$key] = $envValue;
            return $this->configuration[$key];
        }

        return $fallback;
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
