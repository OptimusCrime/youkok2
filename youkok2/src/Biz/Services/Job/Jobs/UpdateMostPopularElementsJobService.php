<?php
namespace Youkok\Biz\Services\Job\Jobs;

use RedisException;
use Youkok\Biz\Services\PopularListing\MostPopularElementsService;

class UpdateMostPopularElementsJobService implements JobServiceInterface
{
    private MostPopularElementsService $mostPopularElementsService;

    public function __construct(MostPopularElementsService $mostPopularElementsService)
    {
        $this->mostPopularElementsService = $mostPopularElementsService;
    }

    /**
     * @throws RedisException
     */
    public function run(): void
    {
        $this->mostPopularElementsService->refreshAll();
    }
}
