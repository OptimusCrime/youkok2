<?php
namespace Youkok\Biz\Services\Job\Jobs;

use RedisException;
use Youkok\Biz\Services\CacheService;
use Youkok\Common\Utilities\CacheKeyGenerator;

class ClearRedisCachePartitionsService implements JobServiceInterface
{
    private CacheService $cacheService;

    public function __construct(CacheService $cacheService)
    {
        $this->cacheService = $cacheService;
    }

    /**
     * @throws RedisException
     */
    public function run(): void
    {
        foreach (ClearRedisCachePartitionsService::partitionsToClear() as $partition) {
            $this->cacheService->delete($partition);
        }
    }

    private static function partitionsToClear(): array {
        return [
            CacheKeyGenerator::keyForBoxesNumberOfFilesThisMonth()
        ];
    }
}
