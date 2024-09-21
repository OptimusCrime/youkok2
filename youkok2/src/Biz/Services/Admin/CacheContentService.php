<?php
namespace Youkok\Biz\Services\Admin;

use RedisException;
use Youkok\Biz\Services\CacheService;

class CacheContentService
{
    private CacheService $cacheService;

    public function __construct(CacheService $cacheService)
    {
        $this->cacheService = $cacheService;
    }

    /**
     * @throws RedisException
     */
    public function getAllCacheContent(): array
    {
        $keys = $this->cacheService->getAllKeys();

        $output = [];
        foreach ($keys as $key) {
            $output[] = [
                'key' => $key,
                'value' => $this->cacheService->get($key)
            ];
        }

        // Sort by key low to high
        usort($output, function (array $first, array $second) {
            return strcmp($first['key'], $second['key']);
        });

        return $output;
    }
}
