<?php
namespace Youkok\Biz\Services\Job\Jobs;

use RedisException;
use Youkok\Biz\Services\PopularListing\MostPopularCoursesService;

class UpdateMostPopularCoursesJobService implements JobServiceInterface
{
    private MostPopularCoursesService $mostPopularCoursesService;

    public function __construct(MostPopularCoursesService $mostPopularCoursesService)
    {
        $this->mostPopularCoursesService = $mostPopularCoursesService;
    }

    /**
     * @throws RedisException
     */
    public function run(): void
    {
        $this->mostPopularCoursesService->refreshAll();
    }
}
