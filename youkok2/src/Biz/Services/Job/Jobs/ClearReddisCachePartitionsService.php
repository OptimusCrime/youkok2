<?php
namespace Youkok\Biz\Services\Job\Jobs;

use Youkok\Biz\Services\CacheService;
use Youkok\Common\Utilities\CacheKeyGenerator;

class ClearReddisCachePartitionsService implements JobServiceInterface
{
    private CacheService $cacheService;

    public function __construct(CacheService $cacheService)
    {
        $this->cacheService = $cacheService;
    }

    public function run(): void
    {
        foreach (ClearReddisCachePartitionsService::partitionsToClear() as $partition) {
            $this->cacheService->delete($partition);
        }
    }

    private static function partitionsToClear(): array {
        return [
            CacheKeyGenerator::keyForBoxesNumberOfFilesThisMonth()
        ];
    }
}
